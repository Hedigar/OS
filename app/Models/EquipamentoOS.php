<?php

namespace App\Models;

use App\Core\Model;

class EquipamentoOS extends Model
{
    protected string $table = 'equipamentos_os';

    /**
     * Busca todos os equipamentos de OSs anteriores de um cliente específico.
     *
     * @param int $clienteId ID do cliente.
     * @return array Lista de equipamentos únicos.
     */
    public function findPreviousEquipmentsByClient(int $clienteId): array
    {
        // Esta query busca equipamentos de OSs que pertencem ao cliente,
        // e agrupa por serial para evitar duplicidade, pegando o registro mais recente.
        $sql = "SELECT
                    e.tipo_equipamento,
                    e.marca_equipamento,
                    e.modelo_equipamento,
                    e.serial_equipamento,
                    e.senha_equipamento,
                    e.acessorios_equipamento,
                    e.fonte_equipamento,
                    e.defeito_relatado,
                    e.laudo_tecnico,
                    os.id as ultima_os_id,
                    os.created_at as ultima_os_data
                FROM
                    {$this->table} e
                JOIN
                    ordens_servico os ON e.os_id = os.id
                WHERE
                    os.cliente_id = :cliente_id
                GROUP BY
                    e.serial_equipamento, e.modelo_equipamento
                ORDER BY
                    os.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca todos os equipamentos de uma OS.
     *
     * @param int $osId ID da Ordem de Serviço.
     * @return array Lista de equipamentos.
     */
    public function findByOsId(int $osId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE os_id = :os_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['os_id' => $osId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
