<?php
require_once 'auth.php'; 
include_once 'header.php';

// Pegamos os IDs favoritos para o Alpine saber quais estrelas pintar
$pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
$stmt = $pdo->prepare("SELECT livro_id FROM favoritos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$meus_favoritos = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<body class="bg-zinc-950 text-zinc-200 font-sans min-h-screen" x-cloak x-data="appData()" x-init="buscar()">

    <main class="max-w-6xl mx-auto pt-10 px-6 pb-20">
        
        <div class="flex flex-col md:flex-row gap-4 mb-16 items-center">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-4 top-3.5 h-5 w-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    x-model="query" 
                    @input.debounce.300ms="buscar()" 
                    type="text" 
                    placeholder="Comece a digitar para pesquisar..."
                    class="w-full bg-zinc-900/50 border border-zinc-800 rounded-2xl pl-12 pr-4 py-3 text-sm focus:border-orange-500 focus:bg-zinc-900 outline-none transition-all">
            </div>

            <select x-model="cat" @change="buscar()" class="w-full md:w-48 bg-zinc-900/50 border border-zinc-800 rounded-2xl px-4 py-3 text-sm outline-none text-zinc-400 cursor-pointer focus:border-orange-500">
                <option value="">Todas Categorias</option>
                <template x-for="c in res.categorias">
                    <option :value="c" x-text="c"></option>
                </template>
            </select>
        </div>

        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-1 bg-orange-500 rounded-full"></div>
                    <h2 class="text-xl font-bold tracking-tight uppercase" x-text="query || cat ? 'Resultados' : 'Explorar Acervo'"></h2>
                </div>
                <span class="text-[10px] font-black text-zinc-600" x-text="res.recentes.length + ' ITENS ENCONTRADOS'"></span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                <template x-for="livro in res.recentes" :key="livro.id">
                    <div class="group relative bg-zinc-900/20 border border-zinc-800/50 rounded-[2rem] p-4 hover:bg-zinc-900/40 transition-all shadow-xl">
                        
                        <a :href="'favoritar.php?id=' + livro.id" 
                           class="absolute top-6 right-6 z-10 p-2 rounded-full bg-black/50 backdrop-blur-md border border-zinc-700 hover:border-orange-500 transition-all">
                            <svg class="w-4 h-4" :class="meusFavoritos.includes(parseInt(livro.id)) ? 'fill-orange-500 text-orange-500' : 'text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </a>

                        <div class="cursor-pointer" @click="window.location.href='download.php?id=' + livro.id">
                            <img :src="livro.capa" class="w-full aspect-[3/4] object-cover rounded-2xl shadow-2xl mb-4 group-hover:scale-[1.02] transition-transform">
                            <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest" x-text="livro.categoria"></span>
                            <h3 class="text-sm font-bold text-zinc-100 mt-1 truncate" x-text="livro.titulo"></h3>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    </main>

    <script>
    function appData() {
        return {
            query: "",
            cat: "",
            res: { recentes: [], categorias: [] },
            meusFavoritos: <?php echo json_encode($meus_favoritos); ?>,
            
            buscar() {
                // A mágica do debounce acontece no HTML, aqui só disparamos o fetch
                fetch(`api.php?q=${this.query}&cat=${this.cat}`)
                    .then(r => r.json())
                    .then(d => this.res = d);
            }
        }
    }
    </script>
</body>