<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
use Src\Models\Produto;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = filter_var($_POST['preco'] ?? 0, FILTER_VALIDATE_FLOAT);
    $quantidade = filter_var($_POST['quantidade'] ?? 0, FILTER_VALIDATE_INT);
    $tipo = trim($_POST['tipo'] ?? '');

    if (!$nome || !$preco || $preco <= 0 || $quantidade === false || $quantidade < 0 || !$tipo) {
        $erro = 'Preencha todos os campos corretamente.';
    } else {
        $id_usuario = $_SESSION['id_usuario'];
        if (Produto::criar($id_usuario, $nome, $descricao, $preco, $quantidade, $tipo)) {
            $sucesso = 'Produto cadastrado com sucesso!';
            // Limpa os valores do formulário após sucesso
            $_POST = [];
        } else {
            $erro = 'Erro ao cadastrar produto.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro de Produto</title>
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
            max-width: 500px;
            width: 100%;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
            color: #007BFF;
            font-weight: 700;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            text-align: left;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #007BFF;
            outline: none;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button.cadastrar {
            background-color: #1a73e8; /* azul mais claro */
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 700;
            width: 100%;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(26, 115, 232, 0.4);
        }
        button.cadastrar:hover {
            background-color: #155ab6;
            box-shadow: 0 6px 12px rgba(21, 90, 182, 0.6);
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        p.link-back {
            margin-top: 30px;
            text-align: center;
        }
        p.link-back a {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
            transition: text-decoration 0.3s ease;
        }
        p.link-back a:hover {
            text-decoration: underline;
        }

        /* Botão extra para cadastro - só um exemplo */
        .btn-cadastro {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            padding: 10px 22px;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }
        .btn-cadastro:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Produto</h2>

        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>

            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" name="preco" required value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>" />

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" required value="<?= htmlspecialchars($_POST['quantidade'] ?? '') ?>" />

            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="produto" <?= (($_POST['tipo'] ?? '') === 'produto') ? 'selected' : '' ?>>Produto</option>
            </select>

            <button type="submit" class="cadastrar">Cadastrar</button>
        </form>

        <p class="link-back"><a href="dashboard_vendedor.php">Voltar ao Dashboard</a></p>
    </div>
</body>
</html>
