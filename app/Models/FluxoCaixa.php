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
                       END as cliente_nome
                FROM {$this->table} fc
                LEFT JOIN ordens_servico os ON fc.os_id = os.id
                LEFT JOIN atendimentos_externos ae ON fc.atendimento_externo_id = ae.id
                LEFT JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN clientes c_at ON ae.cliente_id = c_at.id
                WHERE fc.data BETWEEN ? AND ?
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
     * Obtém totais por período
     */
    public function getTotaisPorPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    tipo,
                    SUM(valor) as total
                FROM {$this->table}
                WHERE data BETWEEN ? AND ?
                GROUP BY tipo";
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
