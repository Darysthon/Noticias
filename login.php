<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header("Location: painel.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Acesso - Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .auth-card { width: 100%; max-width: 400px; padding: 40px; text-align: center; }
        .auth-card h1 { color: var(--primary-dark); margin-bottom: 30px; font-weight: 800; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9em; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .links { margin-top: 25px; display: flex; flex-direction: column; gap: 10px; font-size: 0.9em; }
        .links a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card auth-card">
        <h1>Entrar no Portal</h1>
        <?php if(isset($erro)) echo "<div class='alert alert-error'>$erro</div>"; ?>
        <form method="POST" action="">
            <div class="input-group">
                <label>E-mail Institucional</label>
                <input type="email" name="email" required placeholder="exemplo@portal.com">
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Acessar Painel</button>
        </form>
        <div class="links">
            <a href="cadastrar.php">Criar uma conta de autor</a>
            <a href="index.php" style="color: var(--text-muted);">← Voltar para Home</a>
        </div>
    </div>
</body>
</html> 