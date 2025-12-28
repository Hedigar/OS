<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $host = DB_HOST;
        $db   = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Em produção, não mostramos o erro detalhado para evitar vazamento de informações
            error_log("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
            die("Erro de Conexão: Por favor, tente novamente mais tarde.");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Executa uma consulta preparada.
     * @param string $sql A consulta SQL com placeholders.
     * @param array $params Os parâmetros para a consulta.
     * @return PDOStatement O objeto PDOStatement.
     */
    public function query(string $sql, array $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na Consulta SQL: " . $e->getMessage() . " | SQL: " . $sql);
            die("Erro ao processar a requisição. O administrador foi notificado.");
        }
    }
}
