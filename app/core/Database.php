<?php
class Database {
    private static $instance = null;

    public static function getConnection() {
        if (!self::$instance) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Erro na conexão com o banco de dados: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}