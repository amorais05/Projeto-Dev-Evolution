<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Models\Usuario;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_SESSION['id_usuario'];
$usuario = Usuario::buscarPorId($id);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    $usuarioValido = Usuario::buscarPorId($id);

    if (!$usuarioValido) {
        $erro = "Usuário não encontrado.";
    } else {
        Usuario::atualizar($id, $nome, $email);

        if (!empty($novaSenha) || !empty($confirmarSenha)) {
            if (strlen($novaSenha) < 6) {
                $erro = "A nova senha deve ter pelo menos 6 caracteres.";
            } elseif ($novaSenha !== $confirmarSenha) {
                $erro = "Nova senha e confirmação não coincidem.";
            } else {
                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
                $senhaAlterada = Usuario::atualizarSenha($id, $senhaHash);

                if (!$senhaAlterada) {
                    $erro = "Erro ao alterar a senha.";
                }
            }
        }

        if (!isset($erro)) {
            header('Location: lista_usuarios.php');
            exit;
        }
    }
}

$tipo = $_SESSION['tipo'] ?? null;
$dashboardPath = ($tipo === 'vendedor') ? '../public/vendedor/dashboard_vendedor.php' : '../public/cliente/dashboard_cliente.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Perfil</title>
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
        input[type="password"] {
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
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
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

        .checkbox-container {
            text-align: left;
            margin-bottom: 15px;
            font-size: 14px;
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
        <h2>Editar Meus Dados</h2>

        <?php if (isset($erro)): ?>
            <p class="mensagem erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label for="nova_senha">Nova Senha</label>
            <input type="password" name="nova_senha" id="nova_senha">

            <label for="confirmar_senha">Confirmar Nova Senha</label>
            <input type="password" name="confirmar_senha" id="confirmar_senha">

            <div class="checkbox-container">
                <input type="checkbox" id="mostrarSenha" onclick="toggleSenha()" />
                <label for="mostrarSenha">Mostrar senha</label>
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="<?= $dashboardPath ?>">← Cancelar</a>
    </div>

    <script>
        function toggleSenha() {
            ['nova_senha', 'confirmar_senha'].forEach(id => {
                const campo = document.getElementById(id);
                campo.type = campo.type === 'password' ? 'text' : 'password';
            });
        }
    </script>

</body>
</html>
