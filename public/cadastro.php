<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Src\Models\Usuario;

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$email || !$senha) {
        $erro = 'Por favor, preencha todos os campos corretamente.';
    } else {
        // Verifica se já existe usuário com esse email
        if (Usuario::buscarPorEmail($email)) {
            $erro = 'Email já cadastrado.';
        } else {
            if (Usuario::criar($nome, $email, $senha)) {
                $sucesso = 'Usuário criado com sucesso! <a href="login.php">Faça login</a>';
            } else {
                $erro = 'Erro ao cadastrar usuário.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p style="color: green;"><?= $sucesso ?></p>
    <?php else: ?>
        <form method="POST" action="">
            <label>Nome:<br />
                <input type="text" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />
            </label><br /><br />
            <label>Email:<br />
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
            </label><br /><br />
            <label>Senha:<br />
                <input type="password" name="senha" required />
            </label><br /><br />
            <button type="submit">Cadastrar</button>
        </form>
    <?php endif; ?>
</body>
</html>
