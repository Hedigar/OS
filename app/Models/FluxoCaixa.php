<?php

namespace App\Models;

use App\Core\Model;

class FluxoCaixa extends Model
{
    protected string $table = 'fluxo_caixa';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS fluxo_caixa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            data DATE NOT NULL,
            os_id INT DEFAULT NULL,
            atendimento_externo_id INT DEFAULT NULL,
            tipo ENUM('entrada', 'custo') NOT NULL,
            valor DECIMAL(10, 2) NOT NULL,
            referencia_tipo VARCHAR(50) NOT NULL,
            referencia_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_referencia (referencia_tipo, referencia_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sql);
    }

    /**
     * Registra um custo de item de OS (se ainda não existir)
     */
    public function registrarCustoItemOs(int $itemId, int $osId, float $valor, string $data = null): bool
    {
        $data = $data ?? date('Y-m-d');
        $sql = "INSERT IGNORE INTO {$this->table} 
                (data, os_id, tipo, valor, referencia_tipo, referencia_id) 
                VALUES (?, ?, 'custo', ?, 'item_os', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data, $osId, $valor, $itemId]);
    }

    /**
     * Registra um custo de item de atendimento externo (se ainda não existir)
     */
    public function registrarCustoItemAtendimento(int $itemId, int $atendimentoId, float $valor, string $data = null): bool
    {
        $data = $data ?? date('Y-m-d');
        $sql = "INSERT IGNORE INTO {$this->table} 
                (data, atendimento_externo_id, tipo, valor, referencia_tipo, referencia_id) 
                VALUES (?, ?, 'custo', ?, 'item_atendimento', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data, $atendimentoId, $valor, $itemId]);
    }

    /**
     * Registra o custo de taxa NF de OS/atendimento (se ainda não existir)
     */
    public function registrarCustoTaxaNf(string $tipoOrigem, int $origemId, float $valor, string $data = null): bool
    {
        if ($valor <= 0) {
            return true;
        }
        $data = $data ?? date('Y-m-d');
        $osId = $tipoOrigem === 'os' ? $origemId : null;
        $atendimentoId = $tipoOrigem === 'atendimento' ? $origemId : null;
        $sql = "INSERT IGNORE INTO {$this->table} 
                (data, os_id, atendimento_externo_id, tipo, valor, referencia_tipo, referencia_id) 
                VALUES (?, ?, ?, 'custo', ?, 'taxa_nf_{$tipoOrigem}', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data, $osId, $atendimentoId, $valor, $origemId]);
    }

    /**
     * Registra uma entrada de pagamento (se ainda não existir)
     */
    public function registrarEntradaPagamento(int $pagamentoId, string $tipoOrigem, int $origemId, float $valorBruto, string $data = null): bool
    {
        $data = $data ?? date('Y-m-d');
        $osId = $tipoOrigem === 'os' ? $origemId : null;
        $atendimentoId = $tipoOrigem === 'atendimento' ? $origemId : null;

        $sql = "INSERT IGNORE INTO {$this->table} 
                (data, os_id, atendimento_externo_id, tipo, valor, referencia_tipo, referencia_id) 
                VALUES (?, ?, ?, 'entrada', ?, 'pagamento', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data, $osId, $atendimentoId, $valorBruto, $pagamentoId]);
    }
    
    /**
     * Limpa todos os registros da tabela fluxo_caixa
     */
    public function limparTabela(): bool
    {
        $sql = "TRUNCATE TABLE {$this->table}";
        return $this->db->exec($sql) !== false;
    }

    /**
     * Atualiza o valor de um custo já lançado (usado quando item é editado em OS/Atend ainda aprovado/concluído com período aberto)
     */
    public function atualizarCusto(int $itemId, string $referenciaTipo, float $novoValor): bool
    {
        if ($novoValor <= 0) {
            return $this->removerCustoGenerico($itemId, $referenciaTipo);
        }
        $sql = "UPDATE {$this->table} SET valor = ? WHERE referencia_tipo = ? AND referencia_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$novoValor, $referenciaTipo, $itemId]);
    }

    /**
     * Remove custo genérico por tipo de referência + id
     */
    public function removerCustoGenerico(int $referenciaId, string $referenciaTipo): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE referencia_tipo = ? AND referencia_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$referenciaTipo, $referenciaId]);
    }

    /**
     * Remove custo de taxa NF de OS/atendimento
     */
    public function removerCustoTaxaNf(string $tipoOrigem, int $origemId): bool
    {
        return $this->removerCustoGenerico($origemId, 'taxa_nf_' . $tipoOrigem);
    }

    /**
     * Obtém relatório de fluxo de caixa por período
     */
    public function getRelatorioPorPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT fc.*, 
                       CASE 
                           WHEN fc.os_id IS NOT NULL THEN os.defeito_relatado
                           WHEN fc.atendimento_externo_id IS NOT NULL THEN ae.descricao_problema
                           ELSE ''
                       END as descricao_origem,
                       CASE 
                           WHEN fc.os_id IS NOT NULL THEN c.nome_completo
                           WHEN fc.atendimento_externo_id IS NOT NULL THEN c_at.nome_completo
                           ELSE ''
                       END as cliente_name,
                       COALESCE(NULLIF(ios_os.descricao, ''), NULLIF(ios_at.descricao, '')) as item_descricao
                FROM {$this->table} fc
                LEFT JOIN ordens_servico os ON fc.os_id = os.id
                LEFT JOIN atendimentos_externos ae ON fc.atendimento_externo_id = ae.id
                LEFT JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN clientes c_at ON ae.cliente_id = c_at.id
                LEFT JOIN pagamentos_transacoes pt 
                    ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                LEFT JOIN itens_ordem_servico ios_os 
                    ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios_os.id
                LEFT JOIN itens_ordem_servico ios_at 
                    ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios_at.id
                WHERE fc.data BETWEEN ? AND ?
                AND (
                    fc.referencia_tipo NOT IN ('pagamento', 'item_os', 'item_atendimento')
                    OR (fc.referencia_tipo = 'pagamento' AND pt.id IS NOT NULL AND pt.ativo = 1)
                    OR (fc.referencia_tipo = 'item_os' AND ios_os.id IS NOT NULL AND ios_os.ativo = 1)
                    OR (fc.referencia_tipo = 'item_atendimento' AND ios_at.id IS NOT NULL AND ios_at.ativo = 1)
                )
                ORDER BY fc.data DESC, fc.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Remove entrada de pagamento da tabela fluxo_caixa (quando pagamento é deletado)
     */
    public function removerEntradaPagamento(int $pagamentoId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE referencia_tipo = 'pagamento' AND referencia_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$pagamentoId]);
    }

    /**
     * Remove custo de item de OS da tabela fluxo_caixa (quando item é deletado)
     */
    public function removerCustoItemOs(int $itemId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE referencia_tipo = 'item_os' AND referencia_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$itemId]);
    }

    /**
     * Remove custo de item de atendimento da tabela fluxo_caixa (quando item é deletado)
     */
    public function removerCustoItemAtendimento(int $itemId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE referencia_tipo = 'item_atendimento' AND referencia_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$itemId]);
    }

    /**
     * Obtém totais por período
     */
    public function getTotaisPorPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    fc.tipo,
                    SUM(fc.valor) as total
                FROM {$this->table} fc
                LEFT JOIN pagamentos_transacoes pt 
                    ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                LEFT JOIN itens_ordem_servico ios_os 
                    ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios_os.id
                LEFT JOIN itens_ordem_servico ios_at 
                    ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios_at.id
                WHERE fc.data BETWEEN ? AND ?
                AND (
                    fc.referencia_tipo NOT IN ('pagamento', 'item_os', 'item_atendimento')
                    OR (fc.referencia_tipo = 'pagamento' AND pt.id IS NOT NULL AND pt.ativo = 1)
                    OR (fc.referencia_tipo = 'item_os' AND ios_os.id IS NOT NULL AND ios_os.ativo = 1)
                    OR (fc.referencia_tipo = 'item_atendimento' AND ios_at.id IS NOT NULL AND ios_at.ativo = 1)
                )
                GROUP BY fc.tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $totais = ['entrada' => 0, 'custo' => 0];
        foreach ($result as $row) {
            $totais[$row['tipo']] = (float)$row['total'];
        }
        
        return $totais;
    }
}
