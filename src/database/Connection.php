<?php

namespace EZQuery\Database;

class Connection {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new \PDO("mysql:host=localhost;dbname=meubanco", "usuario", "senha", [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]);
            } catch (\PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
