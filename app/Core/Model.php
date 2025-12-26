<?php

namespace App\Core;

use App\Core\Database;

    abstract class Model
{
    protected $db;
    protected $table;

    public function getConnection()
    {
        return $this->db;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Busca todos os registros da tabela.
     */
    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Busca um registro pelo ID.
     * @param int $id O ID do registro.
     */
    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Insere um novo registro.
     * @param array $data Os dados a serem inseridos (coluna => valor).
     */
    public function create(array $data)
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
     * @param int $id O ID do registro a ser atualizado.
     * @param array $data Os dados a serem atualizados (coluna => valor).
     */
    public function update(int $id, array $data)
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
     * Deleta um registro.
     * @param int $id O ID do registro a ser deletado.
     */
    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Conta o total de registros na tabela, opcionalmente com uma condição WHERE.
     * @param string $whereClause A cláusula WHERE (ex: "nome LIKE :termo").
     * @param array $params Parâmetros para a cláusula WHERE.
     * @return int
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
     * Busca registros com paginação, opcionalmente com uma condição WHERE.
     * @param int $limit Limite de registros por página.
     * @param int $offset Deslocamento (offset).
     * @param string $whereClause A cláusula WHERE (ex: "nome LIKE :termo").
     * @param array $params Parâmetros para a cláusula WHERE.
     * @return array
     */
    public function getPaginated(int $limit, int $offset, string $whereClause = '', array $params = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        // Vincula os parâmetros de busca
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Vincula os parâmetros de paginação
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        // Executa a consulta sem passar o array de parâmetros novamente
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
