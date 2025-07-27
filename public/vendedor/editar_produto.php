<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
use Src\Models\Produto;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: lista_produtos.php');
    exit;
}

// Buscar produto para editar e garantir que pertence ao usuário
$produtos = Produto::listarPorUsuario($id_usuario);
$produto = null;
foreach ($produtos as $p) {
    if ($p['id'] == $id) {
        $produto = $p;
        break;
    }
}

if (!$produto) {
    header('Location: lista_produtos.php');
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
        if (Produto::editar($id, $id_usuario, $nome, $descricao, $preco, $quantidade, $tipo)) {
            $sucesso = 'Produto atualizado com sucesso!';
            // Atualizar dados para mostrar no formulário
            $produto = [
                'id' => $id,
                'nome' => $nome,
                'descricao' => $descricao,
                'preco' => $preco,
                'quantidade' => $quantidade,
                'tipo' => $tipo,
            ];
        } else {
            $erro = 'Erro ao atualizar produto.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Produto</title>
</head>
<body>
    <h2>Editar Produto</h2>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nome:<br />
            <input type="text" name="nome" required value="<?= htmlspecialchars($produto['nome']) ?>" />
        </label><br /><br />
        <label>Descrição:<br />
            <textarea name="descricao"><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </label><br /><br />
        <label>Preço:<br />
            <input type="number" step="0.01" name="preco" required value="<?= htmlspecialchars($produto['preco']) ?>" />
        </label><br /><br />
        <label>Quantidade:<br />
            <input type="number" name="quantidade" required value="<?= htmlspecialchars($produto['quantidade']) ?>" />
        </label><br /><br />
        <label>Tipo:<br />
            <select name="tipo" required>
                <option value="ingresso" <?= ($produto['tipo'] === 'ingresso') ? 'selected' : '' ?>>Ingresso</option>
                <option value="produto" <?= ($produto['tipo'] === 'produto') ? 'selected' : '' ?>>Produto</option>
            </select>
        </label><br /><br />
        <button type="submit">Atualizar</button>
    </form>

    <p><a href="lista_produtos.php">Voltar à lista</a></p>
</body>
</html>
