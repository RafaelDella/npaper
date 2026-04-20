<?php
require_once 'auth.php';
restringirPara('bibliotecario');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $acao = $_POST['acao'];
    $titulo = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $nome_arquivo = $_POST['nome_arquivo'] ?? '';
    $capa_final = $_POST['capa_url'] ?? '';

    // Lógica de Upload (Pasta Thumbs já existente no seu www)
    if (isset($_FILES['capa_arquivo']) && $_FILES['capa_arquivo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['capa_arquivo']['name'], PATHINFO_EXTENSION);
        $nome_seguro = uniqid("capa_") . "." . $ext;
        $destino = __DIR__ . "/thumbs/" . $nome_seguro;

        if (move_uploaded_file($_FILES['capa_arquivo']['tmp_name'], $destino)) {
            $capa_final = "thumbs/" . $nome_seguro;
        }
    }

    try {
        $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
        
        if ($acao === 'adicionar') {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, categoria, capa, nome_arquivo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $categoria, $capa_final, $nome_arquivo]);
        } else {
            $stmt = $pdo->prepare("UPDATE livros SET titulo = ?, categoria = ?, capa = ? WHERE id = ?");
            $stmt->execute([$titulo, $categoria, $capa_final, $id]);
        }
        
        header("Location: bibliotecario.php?status=success");
        exit;
    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
}