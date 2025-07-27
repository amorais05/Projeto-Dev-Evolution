<?php
namespace Src\Models;

use PDO;
use PDOException;

class Conexao
{
    private static $pdo;

    // Método usado pelos modelos para conexão única
    public static function conectar()
    {
        if (!self::$pdo) {
            try {
                $caminhoBanco = __DIR__ . '/../../db.sqlite';
                self::$pdo = new PDO('sqlite:' . $caminhoBanco);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Configurar timeout para evitar "database is locked"
                self::$pdo->setAttribute(PDO::ATTR_TIMEOUT, 5);
            } catch (PDOException $e) {
                die("Erro na conexão com o banco: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
