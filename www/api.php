<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");

// Sincronização básica (simplificada)
$arquivos = array_diff(scandir('/var/www/biblioteca'), array('.', '..'));
foreach ($arquivos as $item) {
    if (pathinfo($item, PATHINFO_EXTENSION) === 'pdf') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO livros (nome_arquivo, titulo, capa) VALUES (?, ?, ?)");
        $stmt->execute([$item, basename($item, ".pdf"), "/thumbs/".basename($item, ".pdf").".jpg"]);
    }
}

$busca = $_GET['q'] ?? '';
$categoria = $_GET['cat'] ?? '';

// Construção da Query de Busca
$where = "WHERE titulo LIKE :q";
$params = [':q' => "%$busca%"];
if ($categoria) {
    $where .= " AND categoria = :cat";
    $params[':cat'] = $categoria;
}

// 1. Últimas Adições
$recentes = $pdo->prepare("SELECT * FROM livros $where ORDER BY data_upload DESC LIMIT 4");
$recentes->execute($params);

// 2. Mais Baixados (Trending)
$trending = $pdo->prepare("SELECT * FROM livros $where ORDER BY downloads DESC LIMIT 4");
$trending->execute($params);

echo json_encode([
    'recentes' => $recentes->fetchAll(PDO::FETCH_ASSOC),
    'trending' => $trending->fetchAll(PDO::FETCH_ASSOC),
    'categorias' => $pdo->query("SELECT DISTINCT categoria FROM livros")->fetchAll(PDO::FETCH_COLUMN)
]);