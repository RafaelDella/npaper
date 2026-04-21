<div x-show="notaModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div @click.away="notaModal = false" class="bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[2rem] p-8 shadow-2xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-bold text-white italic uppercase" x-text="'Notas: ' + livroSelecionado?.titulo"></h2>
                <p class="text-[10px] text-zinc-500 uppercase tracking-widest mt-1">Área de Pensamento Crítico</p>
            </div>
            <button @click="notaModal = false" class="text-zinc-500 hover:text-white">✕</button>
        </div>

        <form action="salvar_nota.php" method="POST" class="space-y-4">
            <input type="hidden" name="livro_id" :value="livroSelecionado?.id">

            <div class="relative">
                <textarea name="conteudo" x-model="minhaNota" :disabled="carregandoNota"
                    placeholder="Escreva suas percepções sobre esta obra..."
                    class="w-full h-40 bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-sm text-zinc-300 outline-none focus:border-orange-500 transition-all disabled:opacity-50"></textarea>

                <div x-show="carregandoNota" class="absolute inset-0 flex items-center justify-center bg-zinc-950/20 backdrop-blur-[1px] rounded-2xl">
                    <div class="w-6 h-6 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-zinc-950/50 p-4 rounded-2xl border border-zinc-800">
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="publica" x-model="isPublica" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                    </label>
                    <span class="text-[10px] font-black uppercase tracking-widest" :class="isPublica ? 'text-orange-500' : 'text-zinc-500'" x-text="isPublica ? 'Nota Pública' : 'Nota Privada'"></span>
                </div>

                <button type="submit" class="bg-white text-black px-6 py-2 rounded-xl font-black text-[10px] tracking-widest hover:bg-orange-500 hover:text-white transition-all">
                    GUARDAR ANOTAÇÃO
                </button>
            </div>
        </form>
    </div>
</div>