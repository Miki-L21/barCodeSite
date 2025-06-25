<?php
session_start(); // Inicia a sessão

// Destrói a sessão
session_unset(); // Limpa as variáveis de sessão
session_destroy(); // Destroi a sessão

// Redireciona de volta para index.php
header("Location: index.php");
exit;
?>
