<nav class="border-b border-zinc-900 bg-zinc-950/50 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2 cursor-pointer" onclick="window.location.href='index.php'">
                <div class="bg-orange-600 h-8 w-8 rounded-lg flex items-center justify-center shadow-lg shadow-orange-900/20">
                    <span class="text-white font-black text-xl italic">N</span>
                </div>
                <span class="text-lg font-black tracking-tighter text-white uppercase">Paper<span class="text-orange-500">.</span></span>
            </div>

            <div class="hidden md:flex items-center gap-6 border-l border-zinc-800 pl-8">
                <a href="index.php" class="text-[10px] font-black tracking-widest transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-500' : 'text-zinc-500 hover:text-zinc-300'; ?>">BIBLIOTECA</a>
                
                <?php if ($_SESSION['usuario_nivel'] === 'bibliotecario' || $_SESSION['usuario_nivel'] === 'tecnico'): ?>
                    <a href="bibliotecario.php" class="text-[10px] font-black tracking-widest transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'bibliotecario.php' ? 'text-orange-500' : 'text-zinc-500 hover:text-zinc-300'; ?>">GESTÃO</a>
                    <a href="galeria.php" class="text-[10px] font-black tracking-widest transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'galeria.php' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300'; ?>">GALERIA</a>
                <?php endif; ?>

                <?php if ($_SESSION['usuario_nivel'] === 'tecnico'): ?>
                    <a href="tecnico.php" class="text-[10px] font-black tracking-widest flex items-center gap-1.5 <?php echo basename($_SERVER['PHP_SELF']) == 'tecnico.php' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300'; ?>">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full <?php echo basename($_SERVER['PHP_SELF']) == 'tecnico.php' ? 'animate-pulse' : ''; ?>"></span>
                        SERVER
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-4">
            
            <?php 
            // Se o nível atual for técnico OU se o nível original gravado na sessão for técnico
            if ($_SESSION['usuario_nivel'] === 'tecnico' || (isset($_SESSION['nivel_original']) && $_SESSION['nivel_original'] === 'tecnico')): 
            ?>
            <div class="hidden lg:flex items-center bg-zinc-900 border border-zinc-800 rounded-xl px-3 py-1.5 gap-2 border-orange-500/30">
                <span class="text-[8px] font-black text-zinc-600 uppercase">Dev:</span>
                <select 
                    onchange="window.location.href='switch_role.php?novo_nivel=' + this.value"
                    class="bg-transparent text-[10px] font-black text-orange-500 outline-none cursor-pointer uppercase">
                    <option value="tecnico" <?php echo $_SESSION['usuario_nivel'] == 'tecnico' ? 'selected' : ''; ?>>Técnico</option>
                    <option value="bibliotecario" <?php echo $_SESSION['usuario_nivel'] == 'bibliotecario' ? 'selected' : ''; ?>>Bibliotecário</option>
                    <option value="aluno" <?php echo $_SESSION['usuario_nivel'] == 'aluno' ? 'selected' : ''; ?>>Aluno</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="flex flex-col items-end leading-none">
                <span class="text-xs font-bold text-zinc-100"><?php echo $_SESSION['usuario_nome']; ?></span>
                <span class="text-[9px] text-zinc-500 uppercase tracking-widest font-medium"><?php echo $_SESSION['usuario_nivel']; ?></span>
            </div>

            <a href="logout.php" class="text-zinc-500 hover:text-red-500 transition-colors p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </a>
        </div>
    </div>
</nav>