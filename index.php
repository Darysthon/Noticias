<?php
session_start();
require 'conexao.php';

$termoBusca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$categoriaBusca = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

// Isso aqui ajuda a mostrar o nome bonito na tela, mesmo que no link não tenha acento
$nomesCategorias = [
    'Educacao' => 'Educação',
    'Politica' => 'Política',
    'Tecnologia' => 'Tecnologia',
    'Esportes' => 'Esportes',
    'Clima' => 'Clima',
    'Cultura' => 'Cultura',
    'Geral' => 'Geral'
];

if ($termoBusca != '') {
    $sql = "SELECT id, titulo, resumo, imagem, data_publicacao, categoria FROM noticias
            WHERE titulo LIKE :busca OR conteudo LIKE :busca OR resumo LIKE :busca
            ORDER BY data_publicacao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['busca' => "%$termoBusca%"]);

} elseif ($categoriaBusca != '') {
    $sql = "SELECT id, titulo, resumo, imagem, data_publicacao, categoria FROM noticias
            WHERE categoria = :categoria
            ORDER BY data_publicacao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['categoria' => $categoriaBusca]);

} else {
    $sql = "SELECT id, titulo, resumo, imagem, data_publicacao, categoria FROM noticias ORDER BY data_publicacao DESC";
    $stmt = $pdo->query($sql);
}

$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Notícias Premium</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .titulo-secao { font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: var(--text-main); border-bottom: 3px solid var(--primary); display: inline-block; padding-bottom: 5px; }
        .noticias-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }

        .card { position: relative; }

        .badge-categoria {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .card-imagem { width: 100%; height: 220px; object-fit: cover; }
        .card-conteudo { padding: 20px; display: flex; flex-direction: column; height: calc(100% - 220px); }
        .card-data { font-size: 0.85em; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
        .card-titulo { font-size: 1.25em; margin-bottom: 12px; color: var(--text-main); line-height: 1.3; }
        .card-resumo { font-size: 0.95em; color: var(--text-muted); margin-bottom: 20px; flex-grow: 1; }
        .search-box { display: flex; background: rgba(255,255,255,0.1); border-radius: 30px; padding: 5px 15px; margin-right: 20px; }
        .search-box input { border: none; background: transparent; color: white; width: 200px; outline: none; box-shadow: none; padding: 5px; }
        .search-box input::placeholder { color: #cbd5e1; }
        .search-box button { background: transparent; border: none; cursor: pointer; color: white; }
        .nav-right { display: flex; align-items: center; }
        .user-menu a { color: white; text-decoration: none; font-weight: 600; background: var(--primary); padding: 8px 15px; border-radius: 20px; transition: 0.3s; }
        .user-menu a:hover { background: var(--primary-dark); }
    </style>
</head>
<body>

    <header class="navbar">
        <nav class="nav-links">
            <a href="index.php">Início</a>
            <a href="index.php?categoria=Educacao">Educação</a>
            <a href="index.php?categoria=Politica">Política</a>
            <a href="index.php?categoria=Tecnologia">Tecnologia</a>
            <a href="index.php?categoria=Esportes">Esportes</a>
            <a href="index.php?categoria=Clima">Clima</a>
            <a href="index.php?categoria=Cultura">Cultura</a>
        </nav>
        <div class="nav-right">
            <form action="index.php" method="GET" class="search-box">
                <input type="text" name="busca" placeholder="Pesquisar matéria..." value="<?= htmlspecialchars($termoBusca) ?>">
                <button type="submit">🔍</button>
            </form>
            <div class="user-menu">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="painel.php">Painel do Autor</a>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if ($termoBusca != ''): ?>
            <h1 class="titulo-secao">Resultados para: "<?= htmlspecialchars($termoBusca) ?>"</h1>
            <p><a href="index.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">← Ver todas as notícias</a></p><br>
        <?php elseif ($categoriaBusca != ''): ?>
            <h1 class="titulo-secao">Notícias sobre: <?= htmlspecialchars($nomesCategorias[$categoriaBusca] ?? $categoriaBusca) ?></h1>
            <p><a href="index.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">← Ver todas as notícias</a></p><br>
        <?php else: ?>
            <h1 class="titulo-secao">Últimas Notícias</h1>
        <?php endif; ?>

        <div class="noticias-grid">
            <?php if (count($noticias) > 0): ?>
                <?php foreach ($noticias as $noticia): ?>
                    <article class="card">

                        <span class="badge-categoria">
                            <?= htmlspecialchars($nomesCategorias[$noticia['categoria']] ?? 'Geral') ?>
                        </span>

                        <?php if (!empty($noticia['imagem'])): ?>
                            <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Capa" class="card-imagem">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=400&auto=format&fit=crop" alt="Sem Capa" class="card-imagem">
                        <?php endif; ?>

                        <div class="card-conteudo">
                            <span class="card-data"><?= date('d M Y • H:i', strtotime($noticia['data_publicacao'])) ?></span>
                            <h2 class="card-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h2>
                            <p class="card-resumo"><?= htmlspecialchars($noticia['resumo']) ?></p>
                            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn" style="width: 100%;">Leia Mais</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); font-size: 1.2em; padding: 50px 0;">Nenhuma notícia encontrada nesta categoria.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>