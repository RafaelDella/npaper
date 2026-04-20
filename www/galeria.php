<?php 
require_once 'auth.php'; 
restringirPara('bibliotecario'); 

// Escaneia a pasta thumbs por extensões de imagem
$caminho = 'thumbs/';
$imagens = glob($caminho . "{*.jpg,*.jpeg,*.png,*.webp}", GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>NPaper | Galeria de Capas</title>
</head>
<body class="bg-zinc-950 text-zinc-300 min-h-screen">

    <?php include_once 'header.php'; ?>

    <main class="max-w-6xl mx-auto p-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight uppercase italic">Banco de Imagens</h1>
                <p class="text-[10px] text-zinc-500 uppercase tracking-widest mt-1">Recursos em /www/thumbs/</p>
            </div>
            <a href="bibliotecario.php" class="text-[10px] font-black bg-zinc-900 border border-zinc-800 px-6 py-2 rounded-xl hover:bg-zinc-800 transition-all">VOLTAR</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach ($imagens as $img): ?>
                <div class="group relative bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-orange-500/50 transition-all shadow-xl">
                    <img src="<?php echo $img; ?>" class="w-full aspect-[3/4] object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <p class="text-[9px] font-mono text-orange-400 truncate"><?php echo basename($img); ?></p>
                        <button onclick="copiarCaminho('<?php echo $img; ?>')" class="mt-2 text-[8px] font-black bg-orange-600 text-white py-1.5 rounded-lg uppercase tracking-tighter">Copiar Link</button>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(empty($imagens)): ?>
                <div class="col-span-full py-20 text-center border-2 border-dashed border-zinc-800 rounded-3xl">
                    <p class="text-zinc-600 uppercase font-black tracking-widest">Nenhuma capa encontrada no diretório.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function copiarCaminho(caminho) {
            navigator.clipboard.writeText(caminho);
            alert('Caminho copiado: ' + caminho);
        }
    </script>
</body>
</html>