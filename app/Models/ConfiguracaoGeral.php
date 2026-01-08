<?php

namespace App\Models;

use App\Core\Model;

class ConfiguracaoGeral extends Model
{
    protected string $table = 'configuracoes_gerais';

    /**
     * Busca o valor de uma configuração pela chave.
     */
    public function getValor(string $chave)
    {
        $stmt = $this->db->prepare("SELECT valor FROM {$this->table} WHERE chave = :chave");
        $stmt->execute(['chave' => $chave]);
        $result = $stmt->fetch();
        return $result ? $result['valor'] : null;
    }

    /**
     * Atualiza ou cria uma configuração.
     */
    public function setValor(string $chave, string $valor, string $descricao = null): bool
    {
        // Usando placeholders distintos para evitar erro de parâmetro no PDO
        $sql = "INSERT INTO {$this->table} (chave, valor, descricao) 
                VALUES (:chave, :valor, :descricao) 
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), descricao = VALUES(descricao)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'chave' => $chave,
            'valor' => $valor,
            'descricao' => $descricao
        ]);
    }
}
