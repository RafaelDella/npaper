<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Função para proteger páginas por nível
function restringirPara($nivelRequerido) {
    $niveis = ['aluno' => 1, 'bibliotecario' => 2, 'tecnico' => 3];
    
    // Pega o nível da sessão ou define como 0 se não existir
    $nivelAtual = $_SESSION['usuario_nivel'] ?? 'aluno';
    
    if ($niveis[$nivelAtual] < $niveis[$nivelRequerido]) {
        header("Location: erro_acesso.php?lvl=" . $nivelAtual);
        exit;
    }
}