<?php
session_start(); // Inicia a sessão para poder manipulá-la
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão
header('Location: index.html'); // Redireciona para a página de login
exit;
?> 