<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Models\Usuario;

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'cliente';

    if (!$nome || !$email || !$senha || !$confirmaSenha) {
        $erro = 'Preencha todos os campos.';
    } elseif ($senha !== $confirmaSenha) {
        $erro = 'As senhas não conferem.';
    } else {
        $usuarioExistente = Usuario::buscarPorEmail($email);
        if ($usuarioExistente) {
            $erro = 'Email já cadastrado.';
        } else {
            if (Usuario::criar($nome, $email, $senha, $tipo)) {
                $sucesso = 'Usuário criado com sucesso!';
            } else {
                $erro = 'Erro ao criar usuário.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Criar Usuário</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 10px;
            font-weight: 500;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .senha-toggle {
            font-size: 12px;
            color: #007bff;
            cursor: pointer;
            display: inline-block;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .mensagem {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .mensagem.erro {
            color: red;
        }

        .mensagem.sucesso {
            color: green;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Criar Usuário</h2>

        <?php if ($erro): ?>
            <p class="mensagem erro"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
            <p class="mensagem sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" required />
            <span class="senha-toggle" onclick="toggleSenha('senha')">👁 Mostrar senha</span>

            <label for="confirma_senha">Confirmar Senha</label>
            <input type="password" name="confirma_senha" id="confirma_senha" required />
            <span class="senha-toggle" onclick="toggleSenha('confirma_senha')">👁 Mostrar senha</span>

            <label for="tipo">Tipo de Usuário</label>
            <select name="tipo" id="tipo" required>
                <option value="cliente" <?= (($_POST['tipo'] ?? '') === 'cliente') ? 'selected' : '' ?>>Cliente</option>
                <option value="vendedor" <?= (($_POST['tipo'] ?? '') === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
            </select>

            <button type="submit">Criar Usuário</button>
        </form>

        <a href="login.php">← Voltar para Login</a>
    </div>

    <script>
        function toggleSenha(id) {
            const campo = document.getElementById(id);
            campo.type = campo.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>
