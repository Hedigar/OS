<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modelo para a tabela de usuários.
 */
class Usuario extends Model
{
    protected string $table = 'usuarios';

    /**
     * Busca um usuário pelo email.
     * 
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Busca um usuário pelo ID.
     * Sobrescreve o método find da classe base para garantir o retorno correto.
     */
    public function find(int $id): mixed
    {
        return parent::find($id);
    }
}
