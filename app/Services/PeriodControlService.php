<?php

namespace App\Services;

use App\Core\Database;

class PeriodControlService
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verifica se um determinado período de competência está fechado
     */
    public function isPeriodClosed(string $date): bool
    {
        if (empty($date)) {
            return false;
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return false;
        }
        
        $mes = (int)date('m', $timestamp);
        $ano = (int)date('Y', $timestamp);

        $sql = "SELECT COUNT(*) FROM fechamentos_mensais WHERE ano = ? AND mes = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano, $mes]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Retorna a lista de todos os fechamentos cadastrados
     */
    public function getClosedPeriods(): array
    {
        $sql = "SELECT fm.*, u.nome as usuario_nome 
                FROM fechamentos_mensais fm
                LEFT JOIN usuarios u ON fm.usuario_id = u.id
                ORDER BY fm.ano DESC, fm.mes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Executa o fechamento de um período
     */
    public function closePeriod(int $ano, int $mes, int $usuarioId, string $obs = null): bool
    {
        $sql = "INSERT INTO fechamentos_mensais (ano, mes, fechado_em, usuario_id, observacoes) 
                VALUES (?, ?, NOW(), ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ano, $mes, $usuarioId, $obs]);
    }

    /**
     * Reabre um período fechado (remove o registro)
     */
    public function reopenPeriod(int $ano, int $mes): bool
    {
        $sql = "DELETE FROM fechamentos_mensais WHERE ano = ? AND mes = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ano, $mes]);
    }
}
