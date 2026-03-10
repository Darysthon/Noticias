<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) die("Notícia não encontrada!");
$id = $_GET['id'];

$sql = "SELECT * FROM noticias WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) die("A notícia que você procura não existe.");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_comentario'])) {
    $nome = trim($_POST['nome']);
    $comentario = trim($_POST['comentario']);

    if (!empty($nome) && !empty($comentario)) {
        $sqlInsert = "INSERT INTO comentarios (noticia_id, nome, comentario) VALUES (:noticia_id, :nome, :comentario)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute(['noticia_id' => $id, 'nome' => $nome, 'comentario' => $comentario]);
        header("Location: noticia.php?id=$id");
        exit;
    }
}

$sqlComentarios = "SELECT * FROM comentarios WHERE noticia_id = :noticia_id ORDER BY data_comentario DESC";
$stmtComentarios = $pdo->prepare($sqlComentarios);
$stmtComentarios->execute(['noticia_id' => $id]);
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .article-container { max-width: 800px; margin: 50px auto; background: var(--card-bg); padding: 50px; border-radius: var(--radius); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .voltar { display: inline-block; margin-bottom: 25px; color: var(--primary); font-weight: 600; text-decoration: none; }
        .voltar:hover { text-decoration: underline; }
        .article-title { font-size: 2.5em; font-weight: 800; line-height: 1.2; margin-bottom: 15px; }
        .article-meta { color: var(--text-muted); font-size: 0.95em; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; }
        .article-image { width: 100%; max-height: 500px; object-fit: cover; border-radius: var(--radius); margin-bottom: 30px; }
        .article-content { font-size: 1.15em; line-height: 1.8; color: #334155; margin-bottom: 50px; }
        .comments-section { background: var(--bg-color); padding: 30px; border-radius: var(--radius); }
        .comment-box { background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 15px; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9em; }
        .comment-author { font-weight: bold; color: var(--text-main); }
        .comment-date { color: var(--text-muted); }
    </style>
</head>
<body>
    <main class="article-container">
        <a href="index.php" class="voltar">← Voltar para o portal</a>

        <h1 class="article-title"><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <div class="article-meta">
            Publicado em <?= date('d/m/Y \à\s H:i', strtotime($noticia['data_publicacao'])) ?>
        </div>

        <?php if (!empty($noticia['imagem'])): ?>
            <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Capa" class="article-image">
        <?php endif; ?>

        <div class="article-content">
            <?= nl2br(htmlspecialchars($noticia['conteudo'])) ?>
        </div>

        <section class="comments-section">
            <h3 style="margin-bottom: 20px;">Deixe seu comentário (<?= count($comentarios) ?>)</h3>

            <form method="POST" action="" style="margin-bottom: 40px; display: grid; gap: 15px;">
                <input type="text" name="nome" placeholder="Seu nome" required>
                <textarea name="comentario" rows="3" placeholder="Participe da discussão..." required></textarea>
                <button type="submit" name="enviar_comentario" class="btn" style="justify-self: start;">Enviar Comentário</button>
            </form>

            <div>
                <?php foreach ($comentarios as $c): ?>
                    <div class="comment-box">
                        <div class="comment-header">
                            <span class="comment-author"><?= htmlspecialchars($c['nome']) ?></span>
                            <span class="comment-date"><?= date('d/m/Y H:i', strtotime($c['data_comentario'])) ?></span>
                        </div>
                        <div style="color: #475569;"><?= nl2br(htmlspecialchars($c['comentario'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>