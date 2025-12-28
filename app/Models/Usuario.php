<?php

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    /**
     * Busca um usuário pelo email.
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}
