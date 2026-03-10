    <?php
    session_start();
    if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
    require 'conexao.php';

    if (!isset($_GET['id'])) { header("Location: painel.php"); exit; }
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo = $_POST['titulo'];
        $categoria = $_POST['categoria'];
        $resumo = $_POST['resumo'];
        $conteudo = $_POST['conteudo'];
        $caminhoImagem = $_POST['imagem_atual'];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'avif'])) {
                $caminhoCompleto = 'uploads/' . uniqid() . "." . $extensao;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
                    if (!empty($caminhoImagem) && file_exists($caminhoImagem)) unlink($caminhoImagem);
                    $caminhoImagem = $caminhoCompleto;
                }
            } else {
                $erro = "Formato inválido. Use JPG, PNG, WEBP, GIF, JFIF ou AVIF.";
            }
        }

        if (!isset($erro)) {
            $sql = "UPDATE noticias SET titulo = :titulo, categoria = :categoria, resumo = :resumo, conteudo = :conteudo, imagem = :imagem WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                'titulo' => $titulo,
                'categoria' => $categoria,
                'resumo' => $resumo,
                'conteudo' => $conteudo,
                'imagem' => $caminhoImagem,
                'id' => $id
            ])) {
                $mensagem = "Matéria atualizada com sucesso! <a href='painel.php' style='color: var(--primary); font-weight: bold;'>← Voltar ao painel</a>";
            } else {
                $erro = "Erro ao atualizar no banco de dados.";
            }
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$noticia) { die("Matéria não encontrada."); }
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Editar Matéria</title>
        <link rel="stylesheet" href="style.css">
        <style>
            .painel-container { max-width: 900px; margin: 40px auto; padding: 40px; }
            .form-group { margin-bottom: 25px; }
            .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
            .alert-success { background: #f0fdf4; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;}
            .alert-error { background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;}
        </style>
    </head>
    <body>
        <header class="navbar" style="background: var(--primary-dark);">
            <div style="font-weight: bold; font-size: 1.2em;">Modo Edição</div>
            <div class="nav-links">
                <a href="painel.php">← Voltar ao Painel</a>
            </div>
        </header>

        <main class="card painel-container">
            <h2 style="margin-bottom: 20px; color: var(--primary-dark);">Editando: <?= htmlspecialchars($noticia['titulo']) ?></h2>

            <?php if(isset($mensagem)) echo "<div class='alert-success'>$mensagem</div>"; ?>
            <?php if(isset($erro)) echo "<div class='alert-error'>$erro</div>"; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($noticia['imagem'] ?? '') ?>">

                <div class="form-group">
                    <label>Título da Notícia</label>
                    <input type="text" name="titulo" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
                </div>

                <div class="form-group">
                    <label>Categoria</label>
                    <select name="categoria" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 16px;">
                        <option value="Educacao" <?= ($noticia['categoria'] ?? '') == 'Educacao' ? 'selected' : '' ?>>Educação</option>
                        <option value="Politica" <?= ($noticia['categoria'] ?? '') == 'Politica' ? 'selected' : '' ?>>Política</option>
                        <option value="Tecnologia" <?= ($noticia['categoria'] ?? '') == 'Tecnologia' ? 'selected' : '' ?>>Tecnologia</option>
                        <option value="Esportes" <?= ($noticia['categoria'] ?? '') == 'Esportes' ? 'selected' : '' ?>>Esportes</option>
                        <option value="Clima" <?= ($noticia['categoria'] ?? '') == 'Clima' ? 'selected' : '' ?>>Clima</option>
                        <option value="Cultura" <?= ($noticia['categoria'] ?? '') == 'Cultura' ? 'selected' : '' ?>>Cultura</option>
                        <option value="Geral" <?= ($noticia['categoria'] ?? 'Geral') == 'Geral' ? 'selected' : '' ?>>Geral</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Imagem de Capa (Deixe em branco para manter a atual)</label>
                    <?php if (!empty($noticia['imagem'])): ?>
                        <p style="font-size: 0.8em; color: gray; margin-bottom: 10px;">Imagem atual: <img src="<?= $noticia['imagem'] ?>" style="height: 40px; vertical-align: middle; margin-left: 10px; border-radius: 4px;"></p>
                    <?php endif; ?>
                    <input type="file" name="imagem" accept="image/*" style="padding: 9px;">
                </div>

                <div class="form-group">
                    <label>Resumo (Lead)</label>
                    <textarea name="resumo" required rows="3"><?= htmlspecialchars($noticia['resumo']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Conteúdo Completo</label>
                    <textarea name="conteudo" required rows="10"><?= htmlspecialchars($noticia['conteudo']) ?></textarea>
                </div>

                <button type="submit" class="btn" style="width: 100%; font-size: 1.1em; padding: 15px;">Salvar Alterações</button>
            </form>
        </main>
    </body>
    </html>