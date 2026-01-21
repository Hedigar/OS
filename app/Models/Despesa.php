<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Despesa extends Model
{
    protected string $table = 'despesas';

    public function getPaginatedJoin(int $limit, int $offset, array $whereParts = [], array $params = []): array
    {
        $sql = "SELECT d.*, c.nome as categoria_nome, u.nome as usuario_nome
                FROM {$this->table} d
                LEFT JOIN despesas_categorias c ON d.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.ativo = 1";
        if (!empty($whereParts)) {
            $sql .= " AND " . implode(' AND ', $whereParts);
        }
        $sql .= " ORDER BY d.data_despesa DESC, d.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countAllJoin(array $whereParts = [], array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} d LEFT JOIN despesas_categorias c ON d.categoria_id = c.id WHERE d.ativo = 1";
        if (!empty($whereParts)) {
            $sql .= " AND " . implode(' AND ', $whereParts);
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT d.*, c.nome as categoria_nome, u.nome as usuario_nome
                FROM {$this->table} d
                LEFT JOIN despesas_categorias c ON d.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.id = :id AND d.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
}
