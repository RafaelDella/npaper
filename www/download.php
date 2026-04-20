<?php
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
        
        // 1. Busca o nome do arquivo e incrementa o contador
        $stmt = $pdo->prepare("SELECT nome_arquivo FROM livros WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $livro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($livro) {
            // 2. Incrementa o contador no banco
            $update = $pdo->prepare("UPDATE livros SET downloads = downloads + 1 WHERE id = ?");
            $update->execute([$_GET['id']]);

            // 3. Força o download do arquivo físico
            $caminho_arquivo = "/var/www/biblioteca/" . $livro['nome_arquivo'];
            
            if (file_exists($caminho_arquivo)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($caminho_arquivo) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($caminho_arquivo));
                readfile($caminho_arquivo);
                exit;
            }
        }
    } catch (Exception $e) {
        die("Erro ao processar download.");
    }
}