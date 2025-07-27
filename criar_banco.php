<?php
try {
    // Cria ou abre o arquivo db.sqlite na raiz do projeto
    $pdo = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria a tabela usuarios
    $sqlUsuarios = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            senha TEXT NOT NULL
        );
    ";

    $pdo->exec($sqlUsuarios);

    echo "Banco e tabela 'usuarios' criados com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar banco ou tabela: " . $e->getMessage();
}
