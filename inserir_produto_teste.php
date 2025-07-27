<?php
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Substitua com o ID do usuário que já existe na sua tabela de usuários
    $id_usuario = 1;

    $sql = "INSERT INTO produtos (id_usuario, nome, descricao, preco, quantidade, reservado, data_reserva, tipo)
            VALUES (:id_usuario, :nome, :descricao, :preco, :quantidade, 0, NULL, :tipo)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindValue(':nome', 'Produto Teste');
    $stmt->bindValue(':descricao', 'Produto de teste manual');
    $stmt->bindValue(':preco', 49.90);
    $stmt->bindValue(':quantidade', 1); // só 1 unidade para testar reserva
    $stmt->bindValue(':tipo', 'produto');

    $stmt->execute();

    echo "Produto de teste inserido com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao inserir produto: " . $e->getMessage();
}
