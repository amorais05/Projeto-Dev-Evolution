<?php
namespace Src\Models;

use PDO;
use PDOException;

class Produto {
    private static $pdo;

    private static function conectar() {
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO('sqlite:' . __DIR__ . '/../../db.sqlite');
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro ao conectar com o banco: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function criar($id_usuario, $nome, $descricao, $preco, $quantidade, $tipo) {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("INSERT INTO produtos (id_usuario, nome, descricao, preco, quantidade, reservado, data_reserva, tipo) VALUES (:id_usuario, :nome, :descricao, :preco, :quantidade, 0, NULL, :tipo)");
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':preco', $preco);
        $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function listarPorUsuario($id_usuario) {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_usuario = :id_usuario");
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function editar($id, $id_usuario, $nome, $descricao, $preco, $quantidade, $tipo) {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, quantidade = :quantidade, tipo = :tipo WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':preco', $preco);
        $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function deletar($id, $id_usuario) {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function listarProdutosDisponiveis() {
        $pdo = self::conectar();
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE quantidade > 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
    