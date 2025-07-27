<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php'); // login está na pasta public
    exit;
}

if ($_SESSION['tipo'] !== 'cliente') {
    // Se não for cliente, redireciona para dashboard vendedor na pasta correta
    header('Location: ../vendedor/dashboard_vendedor.php');
    exit;
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Cliente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        .logo {
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
            background-color: #007bff; /* Azul bootstrap */
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #0056b3; /* Azul escuro */
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
        <h1>Bem-vindo, Cliente!</h1>
        <p>Usuário logado: <strong><?= htmlspecialchars($email) ?></strong></p>

        <nav>
            <a href="produtos_disponiveis.php">Produtos Disponíveis</a>
            <a href="minhas_compras.php">Minhas Compras</a>
            <a href="../lista_usuarios.php">Meu Usuário</a>
        </nav>

        <a href="../logout.php" class="logout">Sair</a>
    </div>
</body>
</html>
