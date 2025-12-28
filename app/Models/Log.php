<?php

namespace App\Models;

use App\Core\Model;

class Log extends Model
{
    protected $table = 'logs';

    /**
     * Registra uma nova ação no sistema.
     * @param int|null $usuario_id ID do usuário que realizou a ação.
     * @param string $acao Descrição da ação realizada.
     * @param string|null $referencia Referência ao objeto afetado (ex: "Cliente #1").
     * @return bool
     */
    public function registrar($usuario_id, string $acao, string $referencia = null, $dados_anteriores = null, $dados_novos = null)
    {
        return $this->create([
            'usuario_id' => $usuario_id,
            'acao' => $acao,
            'referencia' => $referencia,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'dados_anteriores' => $dados_anteriores ? json_encode($dados_anteriores) : null,
            'dados_novos' => $dados_novos ? json_encode($dados_novos) : null
        ]);
    }

    /**
     * Busca os logs mais recentes com informações do usuário.
     * @param int $limit
     * @return array
     */
    public function getRecentes(int $limit = 50)
    {
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM {$this->table} l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
