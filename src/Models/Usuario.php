<?php
namespace Src\Models;

use PDO;
use PDOException;

class Usuario {
    private static function conectar() {
        try {
            $pdo = new PDO('sqlite:' . __DIR__ . '/../../db.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro ao conectar com o banco: " . $e->getMessage());
        }
    }

    public static function criar($nome, $email, $senha, $tipo = 'cliente') {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
        return $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':tipo'  => $tipo
        ]);
    }

    public static function buscarPorEmail($email) {
        $pdo = self::conectar();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id) {
        $pdo = self::conectar();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function atualizar($id, $nome, $email)
{
    $pdo = Conexao::conectar();
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public static function atualizarSenha($id, $novaSenhaHash)
{
    $pdo = Conexao::conectar();
    $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':senha', $novaSenhaHash);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}


}
