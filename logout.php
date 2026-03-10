<?php
session_start(); // Inicia a sessão para o PHP saber qual usuário está logado
session_unset(); // Limpa todas as variáveis da sessão
session_destroy(); // Destrói a sessão completamente

// Redireciona de volta para a página inicial
header("Location: index.php");
exit;
?>