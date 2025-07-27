<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Compra;

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../login.php');
    exit;
}

$id_usuario_logado = $_SESSION['id_usuario'];
$id_produto = $_POST['id_produto'] ?? null;

if (!$id_produto) {
    $_SESSION['msg_erro'] = "Produto não informado.";
    header('Location: produtos_disponiveis.php');
    exit;
}

try {
    if (Compra::registrar($id_produto, $id_usuario_logado)) {
        $_SESSION['msg_sucesso'] = "Compra realizada com sucesso!";
    } else {
        $_SESSION['msg_erro'] = "Erro ao realizar a compra ou produto indisponível.";
    }
} catch (Exception $e) {
    $_SESSION['msg_erro'] = $e->getMessage();
}

header('Location: produtos_disponiveis.php');
exit;
