<?php

namespace App\Core;

use PDO;

/**
 * Classe base para todos os modelos do sistema.
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retorna a conexão PDO.
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }

    /**
     * Retorna o nome da tabela associada ao modelo.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Busca todos os registros ativos da tabela.
     * 
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE ativo = 1");
        return $stmt->fetchAll();
    }

    /**
     * Busca um registro pelo ID.
     * 
     * @param int $id O ID do registro.
     * @return mixed
     */
    public function find(int $id): mixed
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND ativo = 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Insere um novo registro.
     * 
     * @param array $data Os dados a serem inseridos (coluna => valor).
     * @return int|bool O ID inserido ou false em caso de falha.
     */
    public function create(array $data): int|bool
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";

        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($data)) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Atualiza um registro existente.
     * 
     * @param int $id O ID do registro a ser atualizado.
     * @param array $data Os dados a serem atualizados (coluna => valor).
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setClauses = [];
        foreach (array_keys($data) as $field) {
            $setClauses[] = "{$field} = :{$field}";
        }
        $setClause = implode(', ', $setClauses);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Realiza a exclusão lógica de um registro.
     * 
     * @param int $id O ID do registro.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ativo = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Conta o total de registros ativos, opcionalmente com uma condição.
     * 
     * @param string $whereClause Cláusula WHERE adicional.
     * @param array $params Parâmetros para a cláusula.
     * @return int
     */
    public function countAll(string $whereClause = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE ativo = 1";
        if (!empty($whereClause)) {
            $sql .= " AND (" . $whereClause . ")";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca registros com paginação.
     * 
     * @param int $limit
     * @param int $offset
     * @param string $whereClause
     * @param array $params
     * @return array
     */
    public function getPaginated(int $limit, int $offset, string $whereClause = '', array $params = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1";
        if (!empty($whereClause)) {
            $sql .= " AND (" . $whereClause . ")";
        }
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
