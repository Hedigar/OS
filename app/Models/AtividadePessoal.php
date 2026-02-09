<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AtividadePessoal extends Model
{
    protected string $table = 'atividades_pessoais';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS atividades_pessoais (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            data_hora DATETIME NOT NULL,
            tipo VARCHAR(100) NOT NULL,
            descricao VARCHAR(255) DEFAULT NULL,
            tempo_minutos INT NOT NULL,
            local ENUM('Presencial','Home Office','Cliente') DEFAULT 'Presencial',
            categoria ENUM('operacional','supervisao','estrategico') NOT NULL,
            tags VARCHAR(255) DEFAULT NULL,
            observacoes TEXT DEFAULT NULL,
            origem ENUM('manual','log','atendimento','os','orcamento','sistema') DEFAULT 'manual',
            origem_id INT DEFAULT NULL,
            ativo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_usuario_origem (usuario_id, origem, origem_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sql);
    }

    public function upsertByOrigem(int $usuarioId, string $origem, ?int $origemId, array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (usuario_id, data_hora, tipo, descricao, tempo_minutos, local, categoria, tags, observacoes, origem, origem_id, ativo)
                VALUES
                (:usuario_id, :data_hora, :tipo, :descricao, :tempo_minutos, :local, :categoria, :tags, :observacoes, :origem, :origem_id, 1)
                ON DUPLICATE KEY UPDATE
                data_hora = VALUES(data_hora),
                tipo = VALUES(tipo),
                descricao = VALUES(descricao),
                tempo_minutos = VALUES(tempo_minutos),
                local = VALUES(local),
                categoria = VALUES(categoria),
                tags = VALUES(tags),
                observacoes = VALUES(observacoes),
                ativo = 1";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $usuarioId,
            'data_hora' => $data['data_hora'],
            'tipo' => $data['tipo'],
            'descricao' => $data['descricao'] ?? null,
            'tempo_minutos' => (int)$data['tempo_minutos'],
            'local' => $data['local'] ?? 'Presencial',
            'categoria' => $data['categoria'],
            'tags' => $data['tags'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'origem' => $origem,
            'origem_id' => $origemId
        ]);
    }

    public function createManual(int $usuarioId, array $data): int|bool
    {
        $payload = [
            'usuario_id' => $usuarioId,
            'data_hora' => $data['data_hora'],
            'tipo' => $data['tipo'],
            'descricao' => $data['descricao'] ?? null,
            'tempo_minutos' => (int)$data['tempo_minutos'],
            'local' => $data['local'] ?? 'Presencial',
            'categoria' => $data['categoria'],
            'tags' => $data['tags'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'origem' => 'manual',
            'origem_id' => null,
            'ativo' => 1
        ];
        return $this->create($payload);
    }

    public function listarPorPeriodo(int $usuarioId, string $inicio, string $fim): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id
                  AND ativo = 1
                  AND data_hora BETWEEN :inicio AND :fim
                ORDER BY data_hora DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'inicio' => $inicio,
            'fim' => $fim
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function sumPorCategoria(int $usuarioId, string $inicio, string $fim): array
    {
        $sql = "SELECT categoria, SUM(tempo_minutos) as total FROM {$this->table}
                WHERE usuario_id = :usuario_id
                  AND ativo = 1
                  AND data_hora BETWEEN :inicio AND :fim
                GROUP BY categoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'inicio' => $inicio,
            'fim' => $fim
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = ['operacional' => 0, 'supervisao' => 0, 'estrategico' => 0];
        foreach ($rows as $r) {
            $out[$r['categoria']] = (int)$r['total'];
        }
        return $out;
    }
}

