<?php
// Verifica se a sessão já não foi iniciada por outro arquivo (como o auth.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';

// Apenas o técnico (ou quem tem o backup de nível original) pode trocar
$nivelReal = $_SESSION['nivel_original'] ?? $_SESSION['usuario_nivel'];

if ($nivelReal !== 'tecnico') {
    die("Acesso negado: Apenas o administrador do sistema pode simular cargos.");
}

if (isset($_GET['novo_nivel'])) {
    // Salva o nível original para você nunca perder o acesso de Técnico
    if (!isset($_SESSION['nivel_original'])) {
        $_SESSION['nivel_original'] = $_SESSION['usuario_nivel'];
    }

    $_SESSION['usuario_nivel'] = $_GET['novo_nivel'];
    
    // Redireciona de volta
    $voltar = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: " . $voltar);
    exit;
}