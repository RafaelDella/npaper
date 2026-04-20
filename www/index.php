<?php
require_once 'auth.php'; 
include_once 'header.php';

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
    x-data='{ 
        nivel: "<?php echo $_SESSION["usuario_nivel"]; ?>",
        nomeUsuario: "<?php echo $_SESSION["usuario_nome"]; ?>",
        res: { recentes: [], trending: [], categorias: [] }, 
        query: "", cat: "",
        buscar() { 
            fetch(`api.php?q=${this.query}&cat=${this.cat}`)
                .then(r => r.json())
                .then(d => this.res = d) 
        } 
    }' x-init="buscar()">

    <main class="max-w-6xl mx-auto pt-10 px-6 pb-20">

        <div class="flex flex-col md:flex-row gap-4 mb-16 items-center">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-4 top-3.5 h-5 w-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input x-model="query" @keyup.enter="buscar()" type="text" placeholder="Pesquisar por título ou autor..."
                    class="w-full bg-zinc-900/50 border border-zinc-800 rounded-2xl pl-12 pr-4 py-3 text-sm focus:border-orange-500 focus:bg-zinc-900 outline-none transition-all">
            </div>

            <select x-model="cat" @change="buscar()" class="w-full md:w-48 bg-zinc-900/50 border border-zinc-800 rounded-2xl px-4 py-3 text-sm outline-none text-zinc-400 appearance-none cursor-pointer focus:border-orange-500">
                <option value="">Todas Categorias</option>
                <template x-for="c in res.categorias">
                    <option :value="c" x-text="c"></option>
                </template>
            </select>

            <button @click="buscar()" class="w-full md:w-auto bg-zinc-100 hover:bg-white text-black px-8 py-3 rounded-2xl font-bold text-sm transition-all">
                FILTRAR
            </button>
        </div>

        <section class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1 bg-orange-500 rounded-full"></div>
                <h2 class="text-xl font-bold tracking-tight uppercase">Recém Adicionados</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="livro in res.recentes" :key="livro.id">
                    <div class="bg-zinc-900/40 border border-zinc-900 rounded-2xl overflow-hidden hover:border-zinc-700 transition-all group relative">
                        <template x-if="nivel === 'bibliotecario' || nivel === 'tecnico'">
                            <div class="absolute top-2 left-2 z-10">
                                <a :href="'bibliotecario.php?editar=' + livro.id" class="bg-zinc-900/80 backdrop-blur-md p-2 rounded-lg border border-zinc-800 hover:border-orange-500 transition-colors block">
                                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </template>

                        <div class="aspect-[3/4] bg-zinc-800 relative overflow-hidden">
                            <img :src="livro.capa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                @error="$el.src='https://images.unsplash.com/photo-1543004629-1420704621c6?q=80&w=400'">
                        </div>
                        <div class="p-5">
                            <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest" x-text="livro.categoria"></span>
                            <h3 class="text-sm font-bold mt-1 line-clamp-1 text-zinc-100" x-text="livro.titulo"></h3>
                            <div class="flex gap-2 mt-4">
                                <a :href="'/acervo/' + livro.nome_arquivo" target="_blank" class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-[10px] font-bold py-2 rounded-lg text-center transition-colors">VISUALIZAR</a>
                                <a :href="'download.php?id=' + livro.id" class="flex-1 bg-zinc-100 hover:bg-white text-black text-[10px] font-bold py-2 rounded-lg text-center transition-colors">DOWNLOAD</a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <section>
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1 bg-red-600 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.5)]"></div>
                <h2 class="text-xl font-bold tracking-tight uppercase flex items-center gap-2">
                    Mais Baixados <span class="text-red-500 text-sm animate-pulse italic">ON FIRE 🔥</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="livro in res.trending" :key="livro.id">
                    <div class="bg-zinc-900/40 border border-zinc-900 rounded-2xl overflow-hidden hover:border-red-900/30 transition-all group">
                        <div class="aspect-[3/4] bg-zinc-800 relative overflow-hidden">
                            <img :src="livro.capa" class="w-full h-full object-cover grayscale-[40%] group-hover:grayscale-0 transition-all duration-500"
                                @error="$el.src='https://images.unsplash.com/photo-1543004629-1420704621c6?q=80&w=400'">
                            <div class="absolute top-3 right-3 bg-red-600 text-[10px] font-black px-2 py-1 rounded-md shadow-lg" x-text="livro.downloads + ' DOWNLOADS'"></div>
                        </div>
                        <div class="p-5">
                            <h3 class="text-sm font-bold line-clamp-1 text-zinc-100" x-text="livro.titulo"></h3>
                            <div class="flex gap-2 mt-4">
                                <a :href="'/acervo/' + livro.nome_arquivo" target="_blank" class="flex-1 border border-zinc-800 hover:bg-zinc-800 text-[10px] font-bold py-2 rounded-lg text-center transition-colors">VISUALIZAR</a>
                                <a :href="'download.php?id=' + livro.id" class="flex-1 bg-red-600 hover:bg-red-500 text-white text-[10px] font-bold py-2 rounded-lg text-center transition-colors">DOWNLOAD</a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </section>
        

    </main>
    
</body>

</html>

