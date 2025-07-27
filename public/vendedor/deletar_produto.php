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

if ($id) {
    Produto::deletar($id, $id_usuario);
}

header('Location: lista_produtos.php');
exit;
