<?php

namespace App\Models;

use App\Core\Model;

class AtendimentoExterno extends Model
{
    protected string $table = 'atendimentos_externos';

    /**
     * Busca atendimentos externos por cliente.
     * @param int $clienteId
     * @return array
     */
    public function findByClienteId(int $clienteId): array
    {
        $sql = "SELECT ae.*, u.nome as tecnico_nome 
                FROM {$this->table} ae 
                LEFT JOIN usuarios u ON ae.usuario_id = u.id 
                WHERE ae.cliente_id = :cliente_id 
                ORDER BY ae.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sobrescreve o find para incluir dados relacionados.
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT ae.*, c.nome_completo as cliente_nome, u.nome as tecnico_nome 
                FROM {$this->table} ae 
                JOIN clientes c ON ae.cliente_id = c.id 
                LEFT JOIN usuarios u ON ae.usuario_id = u.id 
                WHERE ae.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Sobrescreve o delete pois esta tabela não tem coluna 'ativo'.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Sobrescreve o countAll pois esta tabela não tem coluna 'ativo'.
     */
    public function countAll(string $whereClause = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Sobrescreve o getPaginated pois esta tabela não tem coluna 'ativo'.
     */
    public function getPaginated(int $limit, int $offset, string $whereClause = '', array $params = []): array
    {
        $sql = "SELECT ae.*, c.nome_completo as cliente_nome 
                FROM {$this->table} ae 
                JOIN clientes c ON ae.cliente_id = c.id";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        
        $sql .= " ORDER BY ae.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
