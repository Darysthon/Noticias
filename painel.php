<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publicar'])) {
    $titulo = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $resumo = $_POST['resumo'];
    $conteudo = $_POST['conteudo'];
    $caminhoImagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $pastaDestino = 'uploads/';
        if (!is_dir($pastaDestino)) mkdir($pastaDestino, 0777, true);

        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'avif'])) {
            $caminhoCompleto = $pastaDestino . uniqid() . "." . $extensao;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
                $caminhoImagem = $caminhoCompleto;
            } else $erro = "Erro ao salvar a imagem na pasta.";
        } else $erro = "Formato inválido. Use JPG, PNG, WEBP, GIF, JFIF ou AVIF.";
    }

    if (!isset($erro)) {
        $sql = "INSERT INTO noticias (titulo, resumo, conteudo, imagem, categoria) VALUES (:titulo, :resumo, :conteudo, :imagem, :categoria)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['titulo' => $titulo, 'resumo' => $resumo, 'conteudo' => $conteudo, 'imagem' => $caminhoImagem, 'categoria' => $categoria])) {
            $mensagem = "Notícia publicada com sucesso!";
        } else $erro = "Erro ao salvar no banco de dados.";
    }
}

$sqlList = "SELECT id, titulo, data_publicacao FROM noticias ORDER BY data_publicacao DESC";
$noticias_lista = $pdo->query($sqlList)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Autor</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .painel-container { max-width: 900px; margin: 40px auto; padding: 40px; }
        .painel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid var(--border-color); padding-bottom: 20px; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        .alert-success { background: #f0fdf4; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; }
        .table-noticias { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-noticias th, .table-noticias td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table-noticias th { background-color: #f1f5f9; color: var(--text-muted); }
        .btn-sm { padding: 6px 12px; font-size: 0.85em; border-radius: 4px; text-decoration: none; color: white; display: inline-block; font-weight: bold; }
        .btn-edit { background-color: #eab308; }
        .btn-edit:hover { background-color: #ca8a04; }
        .btn-delete { background-color: #ef4444; }
        .btn-delete:hover { background-color: #dc2626; }
    </style>
</head>
<body>
    <header class="navbar" style="background: var(--primary-dark);">
        <div style="font-weight: bold; font-size: 1.2em;">Painel de Edição</div>
        <div class="nav-links">
            <span>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
            <a href="index.php">Ver Site</a>
            <a href="logout.php" style="color: #fca5a5;">Sair</a>
        </div>
    </header>

    <main class="card painel-container">

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'excluida') echo "<div class='alert-success'>Notícia excluída com sucesso!</div>"; ?>

        <div class="painel-header">
            <h2>Escrever Nova Matéria</h2>
        </div>

        <?php if(isset($mensagem)) echo "<div class='alert-success'>$mensagem</div>"; ?>
        <?php if(isset($erro)) echo "<div class='alert-error'>$erro</div>"; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="publicar" value="1">
            <div class="form-group">
                <label>Título da Notícia</label>
                <input type="text" name="titulo" required placeholder="Ex: Nova tecnologia revoluciona o mercado">
            </div>

            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 16px;">
                    <option value="Educacao">Educação</option>
                    <option value="Politica">Política</option>
                    <option value="Tecnologia">Tecnologia</option>
                    <option value="Esportes">Esportes</option>
                    <option value="Clima">Clima</option>
                    <option value="Cultura">Cultura</option>
                    <option value="Geral">Geral</option>
                </select>
            </div>

            <div class="form-group">
                <label>Imagem de Capa</label>
                <input type="file" name="imagem" accept="image/*" style="padding: 9px;">
            </div>
            <div class="form-group">
                <label>Resumo (Lead)</label>
                <textarea name="resumo" required rows="2" placeholder="Um breve parágrafo chamativo..."></textarea>
            </div>
            <div class="form-group">
                <label>Conteúdo Completo</label>
                <textarea name="conteudo" required rows="10" placeholder="Desenvolva a notícia aqui..."></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%; font-size: 1.1em; padding: 15px;">Publicar Notícia</button>
        </form>

        <hr style="margin: 50px 0; border: none; border-top: 2px solid var(--border-color);">

        <div class="painel-header" style="border: none; margin-bottom: 10px;">
            <h2>Matérias Publicadas</h2>
        </div>

        <table class="table-noticias">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Título</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias_lista as $n): ?>
                <tr>
                    <td style="width: 120px; color: var(--text-muted); font-size: 0.9em;">
                        <?= date('d/m/Y', strtotime($n['data_publicacao'])) ?>
                    </td>
                    <td><strong><?= htmlspecialchars($n['titulo']) ?></strong></td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="editar.php?id=<?= $n['id'] ?>" class="btn-sm btn-edit">Editar</a>
                        <a href="excluir.php?id=<?= $n['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja apagar esta matéria? Essa ação não pode ser desfeita.');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>
</body>
</html>