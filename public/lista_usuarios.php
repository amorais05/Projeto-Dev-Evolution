<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Models\Usuario;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

$tipo = $_SESSION['tipo'] ?? null;

if ($tipo !== 'vendedor' && $tipo !== 'cliente') {
    echo "<p style='color: red; font-weight: bold;'>Tipo de usuário inválido.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$usuario = Usuario::buscarPorId($id_usuario);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Caminhos relativos para links
$dashboardPath = ($tipo === 'vendedor') ? 'vendedor/dashboard_vendedor.php' : 'cliente/dashboard_cliente.php';
$usuarioPath = 'editar_usuario.php';
$logoutPath = 'logout.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Meu Perfil - <?= htmlspecialchars($usuario['nome']) ?></title>
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .perfil-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 12px;
            font-size: 28px;
            font-weight: 700;
        }

        p {
            margin-bottom: 14px;
            font-size: 16px;
            color: #444;
        }

        strong {
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            margin: 10px 8px 0 8px;
            border-radius: 8px;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
            border: none;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: 2px solid #007bff;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #0056b3;
            border-color: #0056b3;
            outline: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            border: 2px solid #6c757d;
        }
        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: #545b62;
            border-color: #545b62;
            outline: none;
        }

        .logout {
            margin-top: 24px;
            background-color: #dc3545;
            border: 2px solid #dc3545;
            color: white;
        }

        .logout:hover,
        .logout:focus {
            background-color: #a71d2a;
            border-color: #a71d2a;
            outline: none;
        }

        /* Responsivo */
        @media (max-width: 480px) {
            .perfil-container {
                padding: 25px 20px;
            }

            h2 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            .btn {
                font-size: 14px;
                padding: 10px 15px;
                margin: 8px 5px 0 5px;
            }
        }
    </style>
</head>
<body>
    <div class="perfil-container" role="main">
        <h2>Meu Perfil</h2>

        <p><strong>ID:</strong> <?= htmlspecialchars($usuario['id']) ?></p>
        <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>

        <div>
            <a href="<?= $dashboardPath ?>" class="btn btn-primary" aria-label="Voltar ao Dashboard">← Voltar ao Dashboard</a>
            <a href="<?= $usuarioPath ?>" class="btn btn-secondary" aria-label="Editar meus dados">✏️ Editar Meus Dados</a>
        </div>

        <form method="POST" action="<?= $logoutPath ?>" style="margin-top: 30px;">
            <button type="submit" class="btn logout" aria-label="Sair da conta">Sair</button>
        </form>
    </div>
</body>
</html>
