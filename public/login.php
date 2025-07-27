<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Models\Usuario;

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Preencha todos os campos corretamente.';
    } else {
        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['tipo'] = $usuario['tipo'];

            if ($usuario['tipo'] === 'vendedor') {
                header('Location: vendedor/dashboard_vendedor.php');
            } else {
                header('Location: cliente/dashboard_cliente.php');
            }
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}
$loginPath = '../public/criar_login.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
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

        .login-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container img.logo {
            width: 100px;
            margin-bottom: 20px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 6px;
            font-weight: 500;
            color: #444;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container button:hover {
            background: #0056b3;
        }

        .checkbox-container {
            text-align: left;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="../public/img/logo.png" alt="Logo do Sistema" class="logo">

        <h2>Entrar</h2>

        <?php if ($erro): ?>
            <p class="error-message"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" required />

            <div class="checkbox-container">
                <input type="checkbox" id="mostrarSenha" onclick="toggleSenha()" />
                <label for="mostrarSenha">Mostrar senha</label>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p style="margin-top: 20px;">Ainda não é usuário? <a href="<?= $loginPath ?>">Cadastrar novo login</a></p>
    </div>

    <script>
        function toggleSenha() {
            const senhaInput = document.getElementById('senha');
            senhaInput.type = senhaInput.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>
