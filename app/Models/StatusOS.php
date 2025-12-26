<?php

namespace App\Models;

use App\Core\Model;

class StatusOS extends Model
{
    protected $table = 'status_os';

    /**
     * Retorna todos os status ordenados.
     * @return array
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY ordem ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
