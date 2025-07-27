<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Produto;
use Src\Models\Cliente;
use Src\Models\Compra;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produto = $_POST['id_produto'] ?? null;
    $id_cliente = $_POST['id_cliente'] ?? null;

    if ($id_produto && $id_cliente) {
        if (Compra::registrar($id_produto, $id_usuario, $id_cliente)) {
            $mensagem = "Compra realizada com sucesso!";
        } else {
            $mensagem = "Erro ao realizar a compra ou produto indisponível.";
        }
    } else {
        $mensagem = "Por favor, selecione produto e cliente.";
    }
}

$produtos = Produto::listarPorUsuario($id_usuario);
$clientes = Cliente::listarPorUsuario($id_usuario);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Realizar Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        p.message {
            font-weight: bold;
            margin-top: 20px;
            color: #d9534f; /* vermelho para erros */
        }
        p.message.sucesso {
            color: #28a745; /* verde para sucesso */
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Realizar Compra</h2>

    <?php if ($mensagem): ?>
        <p class="message <?= strpos($mensagem, 'sucesso') !== false ? 'sucesso' : '' ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="post" action="form_compra.php">
        <label for="id_produto">Produto:</label>
        <select name="id_produto" id="id_produto" required>
            <option value="">-- Selecione --</option>
            <?php foreach ($produtos as $produto): ?>
                <option value="<?= $produto['id'] ?>">
                    <?= htmlspecialchars($produto['nome']) ?> (Qtd: <?= $produto['quantidade'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="id_cliente">Cliente:</label>
        <select name="id_cliente" id="id_cliente" required>
            <option value="">-- Selecione --</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>">
                    <?= htmlspecialchars($cliente['nome']) ?> (<?= htmlspecialchars($cliente['email']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Confirmar Compra</button>
    </form>

    <p><a href="dashboard.php">Voltar ao Dashboard</a></p>
</body>
</html>
