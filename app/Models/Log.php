<?php

namespace App\Models;

use App\Core\Model;

class Log extends Model
{
    protected string $table = 'logs';

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
    public function getRecentes(int $limit = 50): array
    {
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM {$this->table} l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findFiltered(?int $usuarioId, ?string $dataInicio, ?string $dataFim, ?string $acaoLike, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM {$this->table} l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                WHERE 1=1";
        $params = [];
        if ($usuarioId) {
            $sql .= " AND l.usuario_id = :usuario_id";
            $params[':usuario_id'] = $usuarioId;
        }
        if ($dataInicio) {
            $sql .= " AND l.created_at >= :data_inicio";
            $params[':data_inicio'] = $dataInicio . " 00:00:00";
        }
        if ($dataFim) {
            $sql .= " AND l.created_at <= :data_fim";
            $params[':data_fim'] = $dataFim . " 23:59:59";
        }
        if ($acaoLike) {
            $sql .= " AND l.acao LIKE :acao";
            $params[':acao'] = "%" . $acaoLike . "%";
        }
        $sql .= " ORDER BY l.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function countFiltered(?int $usuarioId, ?string $dataInicio, ?string $dataFim, ?string $acaoLike): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} l WHERE 1=1";
        $params = [];
        if ($usuarioId) {
            $sql .= " AND l.usuario_id = :usuario_id";
            $params[':usuario_id'] = $usuarioId;
        }
        if ($dataInicio) {
            $sql .= " AND l.created_at >= :data_inicio";
            $params[':data_inicio'] = $dataInicio . " 00:00:00";
        }
        if ($dataFim) {
            $sql .= " AND l.created_at <= :data_fim";
            $params[':data_fim'] = $dataFim . " 23:59:59";
        }
        if ($acaoLike) {
            $sql .= " AND l.acao LIKE :acao";
            $params[':acao'] = "%" . $acaoLike . "%";
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
}
