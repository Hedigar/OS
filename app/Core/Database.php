<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe de conexão com o banco de dados utilizando o padrão Singleton.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

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
            // Linha ajustada: Define charset e fuso horário na conexão
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4; SET time_zone = '-03:00'"
        ];

        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
            
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                die("Erro de Conexão: " . $e->getMessage());
            }
            
            die("Erro de Conexão: Por favor, tente novamente mais tarde.");
        }
    }

    /**
     * Retorna a instância única da classe Database.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO.
     */
    public function getConnection(): PDO
    {
        return $this->conn;
    }

    /**
     * Executa uma consulta preparada.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na Consulta SQL: " . $e->getMessage() . " | SQL: " . $sql);
            
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                die("Erro na Consulta SQL: " . $e->getMessage() . "<br>SQL: " . $sql);
            }
            
            die("Erro ao processar a requisição. O administrador foi notificado.");
        }
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Não é possível desserializar um singleton.");
    }
}