<?php
namespace Src\Models;

use PDO;
use PDOException;
use Src\Models\Conexao;

class Cupom {
    private static $cupons = [
        'CUPOM5' => 5,
        'CUPOM10' => 10,
        'CUPOM15' => 15
    ];

    // Método para conectar ao banco usando a classe Conexao
    private static function conectar() {
        return Conexao::conectar();
    }

    public static function validarCupom(string $codigo, int $id_cliente): int {
        $codigo = strtoupper(trim($codigo));
        if (!isset(self::$cupons[$codigo])) {
            throw new \Exception("Cupom inválido.");
        }

        $pdo = self::conectar();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM uso_cupons WHERE id_cliente = :id_cliente AND cupom = :cupom");
        $stmt->execute([':id_cliente' => $id_cliente, ':cupom' => $codigo]);

        if ($stmt->fetchColumn() > 0) {
            throw new \Exception("Você já utilizou esse cupom.");
        }

        return self::$cupons[$codigo];
    }

    public static function registrarUso(string $codigo, int $id_cliente): bool {
        $pdo = self::conectar();
        $stmt = $pdo->prepare("INSERT INTO uso_cupons (id_cliente, cupom) VALUES (:id_cliente, :cupom)");
        return $stmt->execute([':id_cliente' => $id_cliente, ':cupom' => strtoupper(trim($codigo))]);
    }
}
