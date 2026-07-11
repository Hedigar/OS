<?php

namespace App\Models;

use App\Core\Model;

class OrdemServico extends Model
{
    protected string $table = 'ordens_servico';
    
    public const STATUS_FINALIZADA = 5;
    public const STATUS_CANCELADA = 6;
    public const STATUS_DIAGNOSTICO_FINALIZADO = 15;
    
    /**
     * Retorna uma OS com dados do cliente, status e equipamento.
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT os.*, 
                       c.nome_completo as cliente_nome, 
                       c.documento as cliente_documento,
                       c.telefone_principal as cliente_telefone,
                       s.nome as status_nome, 
                       s.cor as status_cor,
                       e.tipo as equipamento_tipo,
                       e.marca as equipamento_marca,
                       e.modelo as equipamento_modelo,
                       e.serial as equipamento_serial,
                       e.senha as equipamento_senha,
                       e.voltagem as voltagem,
                       e.acessorios as equipamento_acessorios,
                       e.possui_fonte as equipamento_fonte,
                       e.sn_fonte as equipamento_sn_fonte
                    FROM {$this->table} os
                    JOIN clientes c ON os.cliente_id = c.id
                    JOIN status_os s ON os.status_atual_id = s.id
                    LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.id = :id AND os.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Busca paginada com detalhes e filtros opcionais.
     */
    public function getAllWithDetailsPaginado(
        string $search = '',
        int $limit = 10,
        int $offset = 0,
        array $filters = []
    ): array {
        $sql = $this->buildQueryWithDetails($search, $filters);
        $sql .= " ORDER BY os.id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        $params = $this->bindFilters($search, $filters);
        
        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $stmt->bindValue($key, $val, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $val);
            }
        }
        
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Conta OS com detalhes e filtros opcionais.
     */
    public function countAll(string $search = '', array $filters = []): int
    {
        $sql = $this->buildQueryWithDetails($search, $filters, true);
        $stmt = $this->db->prepare($sql);
        
        $params = $this->bindFilters($search, $filters);
        
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Constrói query base com JOINs.
     */
    private function buildQueryWithDetails(
        string $search = '',
        array $filters = [],
        bool $countOnly = false
    ): string {
        $select = $countOnly
            ? "COUNT(*) as total"
            : "os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor, e.modelo as equipamento_modelo";

        $sql = "SELECT $select
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.ativo = 1";
        
        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
            } else {
                $sql .= " AND c.nome_completo LIKE :search_nome";
            }
        }
        
        if (!empty($filters['status_id'])) {
            $sql .= " AND os.status_atual_id = :status_id";
        }
        if (!empty($filters['status_pagamento'])) {
            $sql .= " AND os.status_pagamento = :status_pagamento";
        }
        if (!empty($filters['status_entrega'])) {
            $sql .= " AND os.status_entrega = :status_entrega";
        }
        if (!empty($filters['sem_atualizacao_dias'])) {
            $sql .= " AND DATEDIFF(NOW(), COALESCE((SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id), os.created_at)) >= :sem_dias";
        }
        
        return $sql;
    }

    /**
     * Prepara parâmetros para bind.
     */
    private function bindFilters(string $search, array $filters): array
    {
        $params = [];
        
        if (!empty($search)) {
            if (is_numeric($search)) {
                $params[':search_id'] = $search;
                $params[':search_nome'] = "%{$search}%";
            } else {
                $params[':search_nome'] = "%{$search}%";
            }
        }
        
        if (!empty($filters['status_id'])) {
            $params[':status_id'] = (int)$filters['status_id'];
        }
        if (!empty($filters['status_pagamento'])) {
            $params[':status_pagamento'] = $filters['status_pagamento'];
        }
        if (!empty($filters['status_entrega'])) {
            $params[':status_entrega'] = $filters['status_entrega'];
        }
        if (!empty($filters['sem_atualizacao_dias'])) {
            $params[':sem_dias'] = (int)$filters['sem_atualizacao_dias'];
        }
        
        return $params;
    }

    public function updateTotals(int $osId, array $itens): bool
    {
        $totalProdutos = 0.00;
        $totalServicos = 0.00;
        $totalDesconto = 0.00;

        foreach ($itens as $item) {
            $tipo = $item['tipo_item'] ?? $item['tipo'] ?? '';
            if ($tipo === 'produto') {
                $totalProdutos += $item['valor_total'];
            } elseif ($tipo === 'servico') {
                $totalServicos += $item['valor_total'];
            }
            $totalDesconto += (float)($item['desconto'] ?? 0);
        }

        $totalOS = $totalProdutos + $totalServicos;

        $stmtNF = $this->db->prepare("SELECT emitir_nf FROM {$this->table} WHERE id = :id");
        $stmtNF->execute(['id' => $osId]);
        $osNF = $stmtNF->fetch();
        $emitirNF = (int)($osNF['emitir_nf'] ?? 0);
        
        $valorTaxaNF = 0.00;
        if ($emitirNF) {
            $configModel = new \App\Models\ConfiguracaoGeral();
            $percProdutos = (float)$configModel->getValor('nf_porcentagem_produtos') ?: 3;
            $percServicos = (float)$configModel->getValor('nf_porcentagem_servicos') ?: 6;
            
            $valorTaxaNF = ($totalProdutos * ($percProdutos / 100)) + ($totalServicos * ($percServicos / 100));
        }

        $sql = "UPDATE {$this->table} SET
                valor_total_produtos = :vtp,
                valor_total_servicos = :vts,
                valor_total_os = :vto,
                valor_desconto = :vd,
                valor_taxa_nf = :vtnf
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'vtp' => $totalProdutos,
            'vts' => $totalServicos,
            'vto' => $totalOS,
            'vd'  => $totalDesconto,
            'vtnf' => $valorTaxaNF,
            'id'  => $osId
        ]);
    }

    /**
     * Busca OS por campo específico.
     */
    public function findBy(string $campo, int $valor): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.{$campo} = :valor AND os.ativo = 1
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['valor' => $valor]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAlertasDashboard(
        int $diasSemAtualizacao = 2,
        int $diasAbertasAtraso = 3,
        int $diasFinalizadasRecentes = 2
    ): array {
        $sql = "SELECT 
                    os.id,
                    os.status_atual_id,
                    os.created_at,
                    os.status_pagamento,
                    os.status_entrega,
                    os.pos_venda_status,
                    c.nome_completo as cliente_nome,
                    c.telefone_principal as cliente_telefone,
                    s.nome as status_nome,
                    s.cor as status_cor,
                    COALESCE(
                        (SELECT MAX(h.created_at) 
                         FROM ordens_servico_status_historico h 
                         WHERE h.ordem_servico_id = os.id),
                        os.created_at
                    ) as ultima_atualizacao
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                JOIN clientes c ON os.cliente_id = c.id
                WHERE os.ativo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $now = new \DateTimeImmutable();
        $alertas = [];

        foreach ($rows as $row) {
            if (empty($row['created_at']) || empty($row['ultima_atualizacao'])) {
                continue;
            }

            try {
                $dataCriacao = new \DateTimeImmutable($row['created_at']);
                $dataUltimaAtualizacao = new \DateTimeImmutable($row['ultima_atualizacao']);
            } catch (\Exception $e) {
                continue;
            }

            $diasDesdeCriacao = $dataCriacao->diff($now)->days;
            $diasDesdeUltimaAtualizacao = $dataUltimaAtualizacao->diff($now)->days;

            $statusId = (int)($row['status_atual_id'] ?? 0);
            $osId = (int)($row['id'] ?? 0);
            $statusNome = $row['status_nome'] ?? '';
            $statusPagamento = $row['status_pagamento'] ?? 'pendente';
            $statusEntrega = $row['status_entrega'] ?? 'nao_entregue';
            $clienteNome = $row['cliente_nome'] ?? '';
            $clienteTelefone = $row['cliente_telefone'] ?? '';

            if ($statusId === self::STATUS_FINALIZADA) {
                if ($statusPagamento === 'pago' && $statusEntrega === 'entregue') {
                    $posVendaStatus = (int)($row['pos_venda_status'] ?? 0);
                    $diasEntrega = $diasDesdeUltimaAtualizacao;

                    if ($posVendaStatus === 0 && $diasEntrega >= 7) {
                        $primeiroNome = explode(' ', trim($clienteNome))[0] ?? '';
                        $alertas[] = [
                            'tipo' => 'pos_venda',
                            'nivel' => 'todos',
                            'prioridade' => 'media',
                            'os_id' => $osId,
                            'status_nome' => $statusNome,
                            'dias' => $diasEntrega,
                            'ultima_atualizacao' => $row['ultima_atualizacao'],
                            'cliente_nome' => $clienteNome,
                            'cliente_telefone' => $clienteTelefone,
                            'mensagem' => sprintf(
                                'Pós-venda: contatar cliente da OS #%d (%s) após %d dia(s) da entrega.',
                                $osId,
                                $primeiroNome,
                                $diasEntrega
                            )
                        ];
                    }
                    continue;
                }

                $mensagem = '';

                if ($statusPagamento !== 'pago' && $statusEntrega !== 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e está aguardando pagamento e entrega.',
                        $osId
                    );
                } elseif ($statusPagamento === 'pago' && $statusEntrega !== 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e paga. Aguardando entrega ao cliente.',
                        $osId
                    );
                } elseif ($statusPagamento !== 'pago' && $statusEntrega === 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e entregue. Pagamento pendente.',
                        $osId
                    );
                } else {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada. Verificar pendências.',
                        $osId
                    );
                }

                $alertas[] = [
                    'tipo' => 'os_finalizada',
                    'nivel' => 'todos',
                    'prioridade' => 'alta',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'status_pagamento' => $statusPagamento,
                    'status_entrega' => $statusEntrega,
                    'dias' => $diasDesdeUltimaAtualizacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => $mensagem
                ];

                continue;
            }

            if ($statusId === self::STATUS_CANCELADA) {
                continue;
            }

            if ($diasDesdeUltimaAtualizacao >= $diasSemAtualizacao) {
                $alertas[] = [
                    'tipo' => 'os_sem_atualizacao',
                    'nivel' => 'tecnico',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'dias' => $diasDesdeUltimaAtualizacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => sprintf(
                        'OS #%d está sem atualização há %d dia(s). Verificar andamento.',
                        $osId,
                        $diasDesdeUltimaAtualizacao
                    )
                ];
            } elseif ($diasDesdeCriacao >= $diasAbertasAtraso) {
                $alertas[] = [
                    'tipo' => 'os_atrasada',
                    'nivel' => 'tecnico',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'dias' => $diasDesdeCriacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => sprintf(
                        'OS #%d está em aberto há %d dia(s).',
                        $osId,
                        $diasDesdeCriacao
                    )
                ];
            }
        }

        return $alertas;
    }

    /**
     * Busca OS por status em um período.
     */
    private function findByStatusNoPeriodo(int $statusId, string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    os.id,
                    os.valor_total_os,
                    os.valor_desconto,
                    os.valor_taxa_nf,
                    os.defeito_relatado,
                    c.nome_completo as cliente,
                    DATE(COALESCE(h.created_at, os.updated_at)) as data_finalizacao
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN (
                    SELECT ordem_servico_id, MAX(created_at) as created_at
                    FROM ordens_servico_status_historico
                    WHERE status_id = ?
                    GROUP BY ordem_servico_id
                ) h ON os.id = h.ordem_servico_id
                WHERE os.ativo = 1 
                AND os.status_atual_id = ?
                AND os.status_atual_id != ?
                AND DATE(COALESCE(h.created_at, os.updated_at)) BETWEEN ? AND ?
                ORDER BY h.created_at DESC, os.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$statusId, $statusId, self::STATUS_CANCELADA, $dataInicio, $dataFim]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Query Scope: Retorna OS finalizadas em um período.
     */
    public function finalizadasNoPeriodo(string $dataInicio, string $dataFim): array
    {
        return $this->findByStatusNoPeriodo(self::STATUS_FINALIZADA, $dataInicio, $dataFim);
    }

    /**
     * Query Scope: Retorna OS com status Diagnóstico Finalizado em um período.
     */
    public function diagnosticoFinalizadoNoPeriodo(string $dataInicio, string $dataFim): array
    {
        return $this->findByStatusNoPeriodo(self::STATUS_DIAGNOSTICO_FINALIZADO, $dataInicio, $dataFim);
    }

    /**
     * Query Scope: Retorna todas OS com valores pendentes.
     */
    public function comPendencias(): array
    {
        $statusCancelado = self::STATUS_CANCELADA;
        
        $sql = "SELECT 
                    os.id,
                    os.valor_total_os,
                    os.valor_desconto,
                    os.valor_taxa_nf,
                    os.defeito_relatado,
                    DATE(os.created_at) as data,
                    c.nome_completo as cliente,
                    COALESCE((SELECT SUM(valor_bruto) FROM pagamentos_transacoes WHERE tipo_origem = 'os' AND origem_id = os.id AND ativo = 1), 0) as valor_pago
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                WHERE os.ativo = 1 
                AND os.valor_total_os > 0
                AND os.status_atual_id != ?
                HAVING valor_pago < (os.valor_total_os - COALESCE(os.valor_desconto, 0)) OR valor_pago = 0
                ORDER BY os.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$statusCancelado]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    // Métodos de compatibilidade - deprecated
    public function getAllWithDetailsPaginadoFiltered(string $search = '', int $limit = 10, int $offset = 0, array $filters = []): array
    {
        return $this->getAllWithDetailsPaginado($search, $limit, $offset, $filters);
    }
    
    public function countAllWithDetailsFiltered(string $search = '', array $filters = []): int
    {
        return $this->countAll($search, $filters);
    }
    
    public function findByClienteId(int $clienteId): array
    {
        return $this->findBy('cliente_id', $clienteId);
    }
    
    public function findByEquipamentoId(int $equipamentoId): array
    {
        return $this->findBy('equipamento_id', $equipamentoId);
    }
    
    public function getAllWithDetails(string $search = ''): array
    {
        return $this->getAllWithDetailsPaginado($search, 999999, 0);
    }
}
