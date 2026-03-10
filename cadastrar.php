<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Criptografa a senha antes de salvar (ótima prática que você já tinha implementado!)
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senhaCriptografada
        ]);
        $mensagem = "Cadastro realizado com sucesso! <a href='login.php' style='color: #166534; text-decoration: underline;'>Faça login aqui</a>.";
    } catch (PDOException $e) {
        $erro = "Erro: Este e-mail já está cadastrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .auth-card { width: 100%; max-width: 400px; padding: 40px; text-align: center; }
        .auth-card h1 { color: var(--primary-dark); margin-bottom: 30px; font-weight: 800; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9em; }

        /* Estilos para os alertas de erro e sucesso */
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

        .links { margin-top: 25px; display: flex; flex-direction: column; gap: 10px; font-size: 0.9em; }
        .links a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="card auth-card">
        <h1>Novo Autor</h1>

        <?php if(isset($erro)) echo "<div class='alert alert-error'>$erro</div>"; ?>
        <?php if(isset($mensagem)) echo "<div class='alert alert-success'>$mensagem</div>"; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required placeholder="Digite seu nome">
            </div>

            <div class="input-group">
                <label>E-mail institucional</label>
                <input type="email" name="email" required placeholder="exemplo@portal.com">
            </div>

            <div class="input-group">
                <label>Criar Senha</label>
                <input type="password" name="senha" required placeholder="Crie uma senha segura">
            </div>

            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Cadastrar Autor</button>
        </form>

        <div class="links">
            <a href="login.php">Já tenho uma conta (Fazer Login)</a>
            <a href="index.php" style="color: var(--text-muted);">← Voltar para a Home</a>
        </div>
    </div>

</body>
</html>