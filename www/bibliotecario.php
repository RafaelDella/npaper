<?php
require_once 'auth.php';
restringirPara('bibliotecario');

try {
    $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
    $stmt = $pdo->query("SELECT * FROM livros ORDER BY id DESC");
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao carregar acervo: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>NPaper | Gestão de Acervo</title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-zinc-950 text-zinc-300 min-h-screen" x-data="{ modal: false, modoEdicao: false, editLivro: {} }">

    <?php include_once 'header.php'; ?>

    <main class="max-w-6xl mx-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight italic uppercase">Gestão de Acervo</h1>
                <p class="text-[10px] text-zinc-500 uppercase tracking-[0.2em] mt-1">Engenharia de Dados / Thumbs</p>
            </div>
            <button @click="editLivro = {titulo:'', categoria:'', capa:''}; modoEdicao = false; modal = true"
                class="bg-orange-600 hover:bg-orange-500 text-white px-6 py-2.5 rounded-xl font-black text-[10px] tracking-widest transition-all shadow-lg shadow-orange-900/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                ADICIONAR OBRA
            </button>
        </div>

        <div class="bg-zinc-900/40 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-900/60 border-b border-zinc-800 text-[10px] uppercase tracking-widest font-black text-zinc-500">
                        <th class="px-6 py-4">Capa</th>
                        <th class="px-6 py-4">Título / Nome do Arquivo</th>
                        <th class="px-6 py-4">Categoria</th>
                        <th class="px-6 py-4 text-center">Downloads</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($livros as $l): ?>
                        <tr class="border-b border-zinc-800/50 hover:bg-zinc-800/30 transition-all group">
                            <td class="px-6 py-4">
                                <img src="<?php echo htmlspecialchars($l['capa'] ?? ''); ?>"
                                    class="w-10 h-14 object-cover rounded-lg shadow-lg border border-zinc-700 bg-zinc-800"
                                    onerror="this.src='https://via.placeholder.com/450x600?text=Sem+Capa'">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-zinc-100"><?php echo htmlspecialchars($l['titulo']); ?></div>
                                <div class="text-[10px] text-zinc-500 font-mono italic truncate max-w-xs"><?php echo $l['nome_arquivo']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-zinc-950 text-orange-500 text-[9px] font-black px-2.5 py-1 rounded-md border border-orange-500/20 uppercase tracking-tighter">
                                    <?php echo htmlspecialchars($l['categoria']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-zinc-400"><?php echo $l['downloads'] ?? 0; ?></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="editLivro = <?php echo htmlspecialchars(json_encode($l)); ?>; modoEdicao = true; modal = true"
                                    class="p-2.5 bg-zinc-800 hover:bg-orange-600 rounded-xl text-white transition-all shadow-lg group-hover:scale-105 border border-zinc-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div x-show="modal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/90 backdrop-blur-md">
            <div @click.away="modal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">

                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-600 to-orange-400"></div>

                <h2 class="text-xl font-bold text-white mb-8 uppercase italic tracking-tight" x-text="modoEdicao ? 'Editar Registro' : 'Novo Registro'"></h2>

                <form action="atualizar_livro.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="id" :value="editLivro.id">
                    <input type="hidden" name="acao" :value="modoEdicao ? 'editar' : 'adicionar'">

                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 text-zinc-500">Título da Obra</label>
                        <input type="text" name="titulo" :value="editLivro.titulo" required
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-4 py-3.5 mt-1.5 focus:border-orange-500 outline-none text-sm text-zinc-100 transition-all">
                    </div>

                    <div x-show="!modoEdicao">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 text-zinc-500">Nome do Arquivo Físico (Ex: tcc.pdf)</label>
                        <input type="text" name="nome_arquivo" :value="editLivro.nome_arquivo" :required="!modoEdicao"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-4 py-3.5 mt-1.5 focus:border-orange-500 outline-none text-sm text-zinc-400 font-mono">
                    </div>

                    <div class="bg-zinc-950/50 p-5 rounded-3xl border border-zinc-800/50 border-dashed space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black text-orange-500 uppercase tracking-widest ml-1">Capa da Obra</label>

                            <a href="galeria.php" target="_blank" class="group flex items-center gap-2 text-[9px] font-black bg-zinc-900 border border-zinc-800 hover:border-orange-500/50 text-zinc-400 hover:text-white px-3 py-1.5 rounded-lg transition-all">
                                <svg class="w-3 h-3 text-zinc-600 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                ABRIR GALERIA
                            </a>
                        </div>

                        <input type="file" name="capa_arquivo" accept="image/*"
                            class="text-[10px] text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[9px] file:font-black file:bg-zinc-800 file:text-white hover:file:bg-orange-600 cursor-pointer w-full transition-all">

                        <div class="relative group">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <svg class="w-3 h-3 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <input type="text" name="capa_url" :value="editLivro.capa" readonly
                                class="w-full bg-zinc-900/30 border border-zinc-800 rounded-xl pl-9 pr-4 py-2.5 text-[9px] font-mono text-zinc-600 cursor-not-allowed select-all"
                                placeholder="Caminho automático do servidor...">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 text-zinc-500">Categoria</label>
                        <input type="text" name="categoria" :value="editLivro.categoria" required
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-4 py-3.5 mt-1.5 focus:border-orange-500 outline-none text-sm text-zinc-100 transition-all">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="modal = false"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 py-4 rounded-2xl font-black text-[10px] tracking-widest uppercase transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-orange-600 hover:bg-orange-500 text-white py-4 rounded-2xl font-black text-[10px] tracking-widest uppercase shadow-lg shadow-orange-900/40 transition-all border-t border-orange-400/20">
                            Salvar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>