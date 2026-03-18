<?php
session_start();
require 'conexao.php';

// 1. Verificar se o usuário está logado
$usuario_logado_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_logado_id) {
    die("Você precisa estar logado para editar.");
}

// 2. Pegar o ID do comentário
$id_comentario = $_GET['id'] ?? die("Comentário não encontrado.");

// 3. Buscar o comentário no banco, garantindo que pertença ao usuário logado
$sql = "SELECT * FROM comentarios WHERE id = :id AND usuario_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_comentario, 'uid' => $usuario_logado_id]);
$comentario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comentario) {
    die("Comentário não encontrado ou você não tem permissão para editá-lo.");
}

// 4. Lógica para SALVAR a edição
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_edicao'])) {
    $novo_texto = trim($_POST['comentario']);

    if (!empty($novo_texto)) {
        $sqlUpdate = "UPDATE comentarios SET comentario = :texto WHERE id = :id AND usuario_id = :uid";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            'texto' => $novo_texto,
            'id'    => $id_comentario,
            'uid'   => $usuario_logado_id
        ]);

        // Volta para a notícia original
        header("Location: noticia.php?id=" . $comentario['noticia_id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Comentário</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container { max-width: 600px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        textarea { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-save { background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .btn-cancel { color: #64748b; text-decoration: none; margin-left: 15px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Editar seu comentário</h2>
        <form method="POST">
            <textarea name="comentario" rows="5" required><?= htmlspecialchars($comentario['comentario']) ?></textarea>
            <br>
            <button type="submit" name="salvar_edicao" class="btn-save">Salvar Alterações</button>
            <a href="noticia.php?id=<?= $comentario['noticia_id'] ?>" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>