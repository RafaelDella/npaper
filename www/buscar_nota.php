<?php
require_once 'auth.php';
// Removemos qualquer erro visual para não quebrar o JSON
error_reporting(0);
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
    
    $livro_id = $_GET['livro_id'] ?? 0;
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $pdo->prepare("SELECT conteudo, publica FROM notas WHERE usuario_id = ? AND livro_id = ?");
    $stmt->execute([$usuario_id, $livro_id]);
    $nota = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nota) {
        echo json_encode([
            'sucesso' => true,
            'conteudo' => $nota['conteudo'],
            'publica' => (int)$nota['publica']
        ]);
    } else {
        echo json_encode(['sucesso' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
exit;