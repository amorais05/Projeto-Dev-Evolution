<?php
namespace Src\Models;

use PDO;
use PDOException;
use Exception;

class Cliente extends Conexao
{
    // Cria cliente e usuário juntos
    public static function criar($nome, $email, $telefone, $senha)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            // Verifica se já existe um usuário com esse email
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Email já cadastrado.");
            }

            // Cria o usuário
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, 'cliente')");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => password_hash($senha, PASSWORD_DEFAULT)
            ]);

            $id_usuario = $pdo->lastInsertId();

            // Cria o cliente vinculado ao usuário
            $stmt = $pdo->prepare("INSERT INTO clientes (id_usuario, nome, email, telefone) VALUES (:id_usuario, :nome, :email, :telefone)");
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone
            ]);

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Você pode logar o erro aqui, lançar ou tratar conforme necessidade:
            // throw $e;
            return false;
        }
    }

    // Lista clientes pelo ID do usuário

   public static function listarPorUsuario($idUsuario)
{
    $pdo = Conexao::conectar();
    $sql = "
        SELECT 
            c.*,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM compras co WHERE co.id_cliente = c.id
                ) THEN 1 ELSE 0 
            END AS realizou_compra
        FROM clientes c
        WHERE c.id_usuario = :id_usuario
        ORDER BY c.nome
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // Edita dados de um cliente
    public static function editar($id, $id_usuario, $nome, $email, $telefone)
    {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Deleta um cliente
    public static function deletar($id, $id_usuario)
    {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
