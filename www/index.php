<?php
require_once 'auth.php';
include_once 'header.php';

// Pegamos os favoritos do banco para marcar as estrelas
$pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
$stmt = $pdo->prepare("SELECT livro_id FROM favoritos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$meus_favoritos = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>NPaper | Acervo Digital</title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-zinc-950 text-zinc-200 font-sans min-h-screen"
    x-cloak
    x-data="appData()"
    x-init="buscar()">

    <main class="max-w-6xl mx-auto pt-10 px-6 pb-20">

        <div class="flex flex-col md:flex-row gap-4 mb-16 items-center">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-4 top-3.5 h-5 w-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input x-model="query" @keyup.enter="buscar()" type="text" placeholder="Pesquisar por título ou categoria..."
                    class="w-full bg-zinc-900/50 border border-zinc-800 rounded-2xl pl-12 pr-4 py-3 text-sm focus:border-orange-500 focus:bg-zinc-900 outline-none transition-all">
            </div>

            <select x-model="cat" @change="buscar()" class="w-full md:w-48 bg-zinc-900/50 border border-zinc-800 rounded-2xl px-4 py-3 text-sm outline-none text-zinc-400 cursor-pointer focus:border-orange-500">
                <option value="">Todas Categorias</option>
                <template x-for="c in res.categorias">
                    <option :value="c" x-text="c"></option>
                </template>
            </select>
        </div>

        <template x-if="recentes.length > 0 && !query && !cat">
            <section class="mb-16">
                <h2 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    Continuar Lendo
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <template x-for="item in recentes" :key="item.id">
                        <div class="relative group">
                            <button @click="removerRecente(item.id)"
                                class="absolute -top-2 -right-2 z-20 bg-zinc-800 text-zinc-400 hover:text-white border border-zinc-700 rounded-full p-1 shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <a :href="'/acervo/' + item.nome_arquivo" target="_blank"
                                @click="registrarLeitura(item)"
                                class="bg-zinc-900/40 border border-zinc-800 p-3 rounded-2xl flex items-center gap-3 hover:border-orange-500/30 transition-all">
                                <img :src="item.capa" class="w-10 h-14 object-cover rounded-lg shadow-md">
                                <div class="truncate">
                                    <p class="text-xs font-bold text-zinc-200 truncate" x-text="item.titulo"></p>
                                    <p class="text-[9px] text-zinc-500 uppercase tracking-tighter">Visualizar PDF</p>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
            </section>
        </template>

        <section class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1 bg-orange-500 rounded-full"></div>
                <h2 class="text-xl font-bold tracking-tight uppercase" x-text="query || cat ? 'Resultados da Busca' : 'Recém Adicionados'"></h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <template x-for="livro in res.recentes" :key="livro.id">
                    <div class="group relative bg-zinc-900/20 border border-zinc-800/50 rounded-[2rem] p-4 hover:bg-zinc-900/40 transition-all shadow-xl">

                        <a :href="'favoritar.php?id=' + livro.id" class="absolute top-6 right-6 z-10 p-2 rounded-full bg-black/50 backdrop-blur-md border border-zinc-700 text-zinc-500 hover:text-orange-500 transition-colors">
                            <svg class="w-4 h-4" :class="meusFavoritos.includes(parseInt(livro.id)) ? 'fill-orange-500 text-orange-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </a>

                        <div class="cursor-pointer" @click="registrarLeitura(livro); window.location.href='download.php?id=' + livro.id">
                            <img :src="livro.capa" class="w-full aspect-[3/4] object-cover rounded-2xl shadow-2xl mb-4 group-hover:scale-[1.02] transition-transform" @error="$el.src='https://via.placeholder.com/450x600?text=Sem+Capa'">
                            <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest" x-text="livro.categoria"></span>
                            <h3 class="text-sm font-bold text-zinc-100 mt-1 truncate" x-text="livro.titulo"></h3>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <template x-if="!query && !cat">
            <section>
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-6 w-1 bg-red-600 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.5)]"></div>
                    <h2 class="text-xl font-bold tracking-tight uppercase flex items-center gap-2">
                        Mais Baixados <span class="text-red-500 text-sm animate-pulse italic">ON FIRE 🔥</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="livro in res.trending" :key="livro.id">
                        <div class="bg-zinc-900/40 border border-zinc-900 rounded-2xl overflow-hidden hover:border-red-900/30 transition-all group p-4" @click="registrarLeitura(livro); window.location.href='download.php?id=' + livro.id">
                            <div class="aspect-[3/4] bg-zinc-800 relative overflow-hidden rounded-xl">
                                <img :src="livro.capa" class="w-full h-full object-cover grayscale-[40%] group-hover:grayscale-0 transition-all duration-500">
                                <div class="absolute top-3 right-3 bg-red-600 text-[10px] font-black px-2 py-1 rounded-md shadow-lg" x-text="livro.downloads + ' DOWNLOADS'"></div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-bold line-clamp-1 text-zinc-100" x-text="livro.titulo"></h3>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </template>

    </main>

    <script>
        function appData() {
            return {
                query: "",
                cat: "",
                res: {
                    recentes: [],
                    trending: [],
                    categorias: []
                },
                recentes: JSON.parse(localStorage.getItem('npaper_recentes') || '[]'),
                meusFavoritos: <?php echo json_encode($meus_favoritos); ?>,

                buscar() {
                    fetch(`api.php?q=${this.query}&cat=${this.cat}`)
                        .then(r => r.json())
                        .then(d => this.res = d);
                },

                registrarLeitura(livro) {
                    // Filtra para não repetir o mesmo livro na lista
                    let lista = this.recentes.filter(l => l.id !== livro.id);

                    // Adiciona o novo (ou o atualizado) no topo da lista
                    lista.unshift({
                        id: livro.id,
                        titulo: livro.titulo,
                        capa: livro.capa,
                        nome_arquivo: livro.nome_arquivo // Guardamos o nome para abrir direto
                    });

                    // Mantém apenas os 5 últimos e salva
                    this.recentes = lista.slice(0, 5);
                    localStorage.setItem('npaper_recentes', JSON.stringify(this.recentes));
                },

                removerRecente(id) {
                    // Filtra a lista removendo o ID clicado
                    this.recentes = this.recentes.filter(l => l.id !== id);
                    localStorage.setItem('npaper_recentes', JSON.stringify(this.recentes));
                }
            }
        }
    </script>
</body>

</html>