<?php
require_once 'auth.php';
restringirPara('bibliotecario');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $acao = $_POST['acao'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
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
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lógica de Upload do PDF
        if (isset($_FILES['pdf_arquivo']) && $_FILES['pdf_arquivo']['error'] === UPLOAD_ERR_OK) {
            $pdf_name = $_FILES['pdf_arquivo']['name'];
            
            // Sanitiza o nome do arquivo para remover caminhos perigosos e caracteres não permitidos
            $nome_base = pathinfo($pdf_name, PATHINFO_FILENAME);
            $nome_sanitizado = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nome_base);
            
            // Se o usuário digitou um nome de arquivo específico, vamos usá-lo como base
            if (!empty($nome_arquivo)) {
                $nome_base_custom = pathinfo($nome_arquivo, PATHINFO_FILENAME);
                $nome_sanitizado = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nome_base_custom);
            }

            $nome_arquivo_final = $nome_sanitizado . ".pdf";
            $destino_pdf = "/var/www/biblioteca/" . $nome_arquivo_final;

            // Verifica se o arquivo físico já existe no disco ou se já existe no banco (para outro ID)
            $tentativa = 1;
            while (true) {
                // Checa no banco de dados
                if ($acao === 'adicionar') {
                    $stmt_check = $pdo->prepare("SELECT id FROM livros WHERE nome_arquivo = ?");
                    $stmt_check->execute([$nome_arquivo_final]);
                } else {
                    $stmt_check = $pdo->prepare("SELECT id FROM livros WHERE nome_arquivo = ? AND id != ?");
                    $stmt_check->execute([$nome_arquivo_final, $id]);
                }
                $exists_db = $stmt_check->fetch();

                if (file_exists($destino_pdf) || $exists_db) {
                    $nome_arquivo_final = $nome_sanitizado . "_" . uniqid() . ".pdf";
                    $destino_pdf = "/var/www/biblioteca/" . $nome_arquivo_final;
                } else {
                    break;
                }
                
                $tentativa++;
                if ($tentativa > 10) {
                    $nome_arquivo_final = $nome_sanitizado . "_" . time() . "_" . rand(1000, 9999) . ".pdf";
                    $destino_pdf = "/var/www/biblioteca/" . $nome_arquivo_final;
                    break;
                }
            }

            // Move o arquivo temporário para o destino final
            if (!move_uploaded_file($_FILES['pdf_arquivo']['tmp_name'], $destino_pdf)) {
                throw new Exception("Falha ao salvar o arquivo PDF no diretório de destino.");
            }

            $nome_arquivo = $nome_arquivo_final;
        } else {
            // Se não foi feito upload de PDF
            if ($acao === 'adicionar' && empty($nome_arquivo)) {
                throw new Exception("O upload do arquivo PDF é obrigatório para novas obras.");
            }
        }

        if ($acao === 'adicionar') {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, categoria, capa, nome_arquivo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $categoria, $capa_final, $nome_arquivo]);
        } elseif ($acao === 'deletar') {
            // 1. Busca os caminhos dos arquivos físicos para remoção
            $stmt = $pdo->prepare("SELECT nome_arquivo, capa FROM livros WHERE id = ?");
            $stmt->execute([$id]);
            $livro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($livro) {
                // Deleta o PDF físico
                if (!empty($livro['nome_arquivo'])) {
                    $caminho_pdf = "/var/www/biblioteca/" . $livro['nome_arquivo'];
                    if (file_exists($caminho_pdf)) {
                        @unlink($caminho_pdf);
                    }
                }
                
                // Deleta a capa local se for um upload
                if (!empty($livro['capa']) && str_starts_with($livro['capa'], 'thumbs/')) {
                    $caminho_capa = __DIR__ . "/" . $livro['capa'];
                    if (file_exists($caminho_capa)) {
                        @unlink($caminho_capa);
                    }
                }
            }

            // 2. Deleta o registro do livro no banco de dados (favoritos e notas serão deletados em cascata por causa de ON DELETE CASCADE)
            $stmt = $pdo->prepare("DELETE FROM livros WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            // Se no modo de edição um novo arquivo PDF foi carregado ou nome_arquivo foi alterado
            if (!empty($nome_arquivo)) {
                $stmt = $pdo->prepare("UPDATE livros SET titulo = ?, categoria = ?, capa = ?, nome_arquivo = ? WHERE id = ?");
                $stmt->execute([$titulo, $categoria, $capa_final, $nome_arquivo, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE livros SET titulo = ?, categoria = ?, capa = ? WHERE id = ?");
                $stmt->execute([$titulo, $categoria, $capa_final, $id]);
            }
        }
        
        header("Location: bibliotecario.php?status=success");
        exit;
    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
}