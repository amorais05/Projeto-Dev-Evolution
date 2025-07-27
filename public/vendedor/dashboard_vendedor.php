<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php'); // sobe um nível para acessar login.php
    exit;
}

if ($_SESSION['tipo'] !== 'vendedor') {
    // Se não for vendedor, redireciona para dashboard cliente na pasta correta
    header('Location: ../cliente/dashboard_cliente.php');
    exit;
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Vendedor</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 600px;
            width: 100%;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        img.logo {
            max-width: 180px;
            margin-bottom: 25px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 700;
        }
        p {
            margin-bottom: 30px;
            font-size: 16px;
            color: #444;
        }
        nav a {
            display: inline-block;
            margin: 10px 15px;
            padding: 12px 25px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #0056b3;
        }
        .logout {
            margin-top: 40px;
            display: inline-block;
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .logout:hover {
            color: #a71d2a;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="/Projeto/public/img/logo.png" alt="Logo do Sistema" class="logo" />

        <h2>Bem-vindo, Vendedor!</h2>
        <p>Usuário logado: <strong><?= htmlspecialchars($email) ?></strong></p>

        <nav>
            <a href="lista_produtos.php">Meus Produtos</a>
            <a href="minhas_compras_vendedor.php">Compras realizadas</a>
            <a href="../lista_usuarios.php">Meu Usuário</a>
            <a href="cadastro_produto.php">Cadastrar novo protudo</a>

        </nav>

        <a href="../logout.php" class="logout">Sair</a>
    </div>
</body>
</html>
