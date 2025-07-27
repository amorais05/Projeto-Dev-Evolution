<?php
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Substitua com o ID do usuário que já existe (ex: 1)
    $id_usuario = 1;

    $sql = "INSERT INTO clientes (id_usuario, nome, email, telefone)
            VALUES (:id_usuario, :nome, :email, :telefone)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindValue(':nome', 'Cliente Teste');
    $stmt->bindValue(':email', 'teste@cliente.com');
    $stmt->bindValue(':telefone', '11999999999');

    $stmt->execute();

    echo "Cliente de teste inserido com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao inserir cliente: " . $e->getMessage();
}

