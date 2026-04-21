<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $livro_id   = $_POST['livro_id'];
    $conteudo   = $_POST['conteudo'];
    $publica    = isset($_POST['publica']) ? 1 : 0;

    try {
        $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
        
        // Tentamos inserir. Se o par usuario_id + livro_id já existir (UNIQUE), atualizamos o conteúdo.
        $sql = "INSERT INTO notas (usuario_id, livro_id, conteudo, publica) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo), publica = VALUES(publica)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $livro_id, $conteudo, $publica]);

        header("Location: index.php?status=nota_salva");
        exit;
    } catch (Exception $e) {
        die("Erro ao salvar nota: " . $e->getMessage());
    }
}