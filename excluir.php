<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
require 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Pega o caminho da imagem para apagar o arquivo do servidor
    $stmt = $pdo->prepare("SELECT imagem FROM noticias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($noticia && !empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
        unlink($noticia['imagem']); // Função do PHP que apaga o arquivo físico
    }

    // 2. Apaga do Banco de Dados (Os comentários são apagados automaticamente por causa do "ON DELETE CASCADE" no SQL)
    $stmtDel = $pdo->prepare("DELETE FROM noticias WHERE id = :id");
    $stmtDel->execute(['id' => $id]);
}

// Redireciona de volta para o painel com uma mensagem
header("Location: painel.php?msg=excluida");
exit;
?>