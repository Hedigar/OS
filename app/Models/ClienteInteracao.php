<?php

namespace App\Models;

use App\Core\Model;

class ClienteInteracao extends Model
{
    protected string $table = 'cliente_interacoes';

    /**
     * Retorna o histórico unificado de interações de um cliente.
     * @param int $clienteId
     * @return array
     */
    public function getHistoricoUnificado(int $clienteId): array
    {
        // 1. CRM (cliente_interacoes)
        $sqlCRM = "SELECT 
                    'crm' as tipo_registro,
                    ci.tipo,
                    ci.assunto as titulo,
                    ci.descricao,
                    ci.resposta_cliente as detalhe,
                    ci.nota_satisfacao as nota,
                    ci.canal,
                    ci.ordem_servico_id as os_id,
                    u.nome as usuario_nome,
                    ci.created_at
                   FROM {$this->table} ci
                   LEFT JOIN usuarios u ON ci.usuario_id = u.id
                   WHERE ci.cliente_id = :cliente_id";
        
        // 2. Ordens de Serviço
        $sqlOS = "SELECT 
                    'os' as tipo_registro,
                    'OS' as tipo,
                    CONCAT('OS #', os.id) as titulo,
                    os.defeito_relatado as descricao,
                    os.laudo_tecnico as detalhe,
                    os.pos_venda_nota as nota,
                    'balcao' as canal,
                    os.id as os_id,
                    'Sistema' as usuario_nome,
                    os.created_at
                  FROM ordens_servico os
                  WHERE os.cliente_id = :cliente_id_os AND os.ativo = 1";

        // 3. Atendimentos Externos
        $sqlAE = "SELECT 
                    'atendimento_externo' as tipo_registro,
                    'Visita Técnica' as tipo,
                    CONCAT('Atendimento #', ae.id) as titulo,
                    ae.descricao_problema as descricao,
                    ae.observacoes_tecnicas as detalhe,
                    NULL as nota,
                    'presencial' as canal,
                    ae.ordem_servico_id as os_id,
                    u.nome as usuario_nome,
                    ae.created_at
                  FROM atendimentos_externos ae
                  LEFT JOIN usuarios u ON ae.usuario_id = u.id
                  WHERE ae.cliente_id = :cliente_id_ae AND ae.ativo = 1";

        // 4. Logs do Cliente
        $sqlLogs = "SELECT 
                    'log' as tipo_registro,
                    'Sistema' as tipo,
                    l.acao as titulo,
                    l.referencia as descricao,
                    NULL as detalhe,
                    NULL as nota,
                    'log' as canal,
                    NULL as os_id,
                    u.nome as usuario_nome,
                    l.created_at
                   FROM logs l
                   LEFT JOIN usuarios u ON l.usuario_id = u.id
                   WHERE l.referencia LIKE :cliente_ref";

        // Unificar e ordenar por data decrescente
        $sqlFinal = "($sqlCRM) UNION ALL ($sqlOS) UNION ALL ($sqlAE) UNION ALL ($sqlLogs) ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sqlFinal);
        $stmt->execute([
            'cliente_id' => $clienteId,
            'cliente_id_os' => $clienteId,
            'cliente_id_ae' => $clienteId,
            'cliente_ref' => "Cliente #{$clienteId}%"
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca clientes por critérios avançados de CRM (churn e tipos de serviço).
     */
    public function getClientesFiltroCRM(array $filtros): array
    {
        $sql = "SELECT c.id, c.nome_completo, c.telefone_principal, 
                       MAX(os.created_at) as ultima_visita,
                       CASE 
                           WHEN MAX(os.created_at) IS NULL THEN 9999 -- Nunca veio
                           ELSE DATEDIFF(NOW(), MAX(os.created_at)) 
                       END as dias_sem_vir
                FROM clientes c
                LEFT JOIN ordens_servico os ON c.id = os.cliente_id AND os.ativo = 1
                WHERE c.ativo = 1";
        
        $params = [];
        $having = [];

        // Filtro por período sem visita (Churn)
        // Se dias_min for informado, filtramos quem está há mais tempo que isso
        if (!empty($filtros['dias_min'])) {
            $having[] = "dias_sem_vir >= :dias_min";
            $params[':dias_min'] = (int)$filtros['dias_min'];
        }

        // Filtro por tipo de serviço/produto realizado
        if (!empty($filtros['termo_servico'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM itens_ordem_servico ios 
                JOIN ordens_servico os2 ON ios.ordem_servico_id = os2.id
                WHERE os2.cliente_id = c.id 
                AND ios.ativo = 1 
                AND ios.descricao LIKE :termo_servico
            )";
            $params[':termo_servico'] = "%" . $filtros['termo_servico'] . "%";
        }

        // Excluir clientes que já foram contactados NESTA campanha específica
        if (!empty($filtros['campanha_id'])) {
            $sql .= " AND NOT EXISTS (
                SELECT 1 FROM cliente_interacoes ci 
                WHERE ci.cliente_id = c.id 
                AND ci.campanha_id = :campanha_id
            )";
            $params[':campanha_id'] = (int)$filtros['campanha_id'];
        }

        $sql .= " GROUP BY c.id";

        if (!empty($having)) {
            $sql .= " HAVING " . implode(" AND ", $having);
        }

        $sql .= " ORDER BY dias_sem_vir DESC, c.nome_completo ASC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
