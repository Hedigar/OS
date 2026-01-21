<?php

namespace App\Models;

use App\Core\Model;

class PagamentoTransacao extends Model
{
    protected string $table = 'pagamentos_transacoes';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS pagamentos_transacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_origem ENUM('os','atendimento') NOT NULL,
            origem_id INT NOT NULL,
            maquina VARCHAR(100) DEFAULT NULL,
            forma VARCHAR(50) NOT NULL,
            bandeira VARCHAR(50) DEFAULT NULL,
            parcelas INT DEFAULT 1,
            taxa_percentual DECIMAL(10,2) DEFAULT 0.00,
            valor_bruto DECIMAL(10,2) NOT NULL,
            valor_taxa DECIMAL(10,2) DEFAULT 0.00,
            valor_liquido DECIMAL(10,2) NOT NULL,
            usuario_id INT DEFAULT NULL,
            ativo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sql);
    }

    public function sumByOrigem(string $tipo, int $origemId): float
    {
        $stmt = $this->db->prepare("SELECT SUM(valor_bruto) as total FROM {$this->table} WHERE tipo_origem = :tipo AND origem_id = :id AND ativo = 1");
        $stmt->execute(['tipo' => $tipo, 'id' => $origemId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (float)($row['total'] ?? 0);
    }

    public function findByOrigem(string $tipo, int $origemId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tipo_origem = :tipo AND origem_id = :id AND ativo = 1 ORDER BY id DESC");
        $stmt->execute(['tipo' => $tipo, 'id' => $origemId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ativo = 0 WHERE id = :id AND ativo = 1");
        return $stmt->execute(['id' => $id]);
    }
}
