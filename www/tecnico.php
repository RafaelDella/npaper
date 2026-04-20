<?php
// Proteção contra headers já enviados e sessões duplicadas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php'; 
include_once 'header.php';

// Verifica se o usuário tem permissão real ou simulada de técnico
$nivelPermitido = $_SESSION['nivel_original'] ?? $_SESSION['usuario_nivel'];
if ($nivelPermitido !== 'tecnico') {
    header("Location: erro_acesso.php?lvl=" . $_SESSION['usuario_nivel']);
    exit;
}

// --- COLETA DE DADOS DO SISTEMA (LINUX / ACER) ---
$load = sys_getloadavg();
$uptime = shell_exec("uptime -p");
$memoria_uso = shell_exec("free -m | grep Mem | awk '{print $3}'") ?? "0";
$memoria_total = shell_exec("free -m | grep Mem | awk '{print $2}'") ?? "0";
$disco_info = shell_exec("df -h / | awk 'NR==2 {print $3 \" / \" $2 \" (\" $5 \")\"}'") ?? "Indisponível";

// Tenta listar containers se o socket estiver mapeado, senão silencia o erro
$docker_ps = @shell_exec("docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' 2>/dev/null") ?: "# Socket do Docker não acessível via PHP-FPM";

// Lê as últimas 10 linhas de logs de erro para monitorar atividade
// Tenta capturar os últimos erros registrados pelo próprio PHP
$logs = error_get_last();
if ($logs) {
    $log_output = "ERRO: " . $logs['message'] . " em " . $logs['file'] . " linha " . $logs['line'];
} else {
    // Se não houver erros na memória, tenta ler o log do Nginx (se estiver no mesmo container)
    $log_output = @shell_exec("tail -n 5 /var/log/nginx/error.log 2>&1") ?: "Nenhum evento crítico registrado no momento.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>NPaper | Engenharia de Servidor</title>
</head>
<body class="bg-black text-zinc-300 font-mono min-h-screen">
    <main class="max-w-6xl mx-auto p-8">
        <div class="flex items-center gap-4 mb-10 border-b border-zinc-900 pb-6">
            <div class="h-10 w-10 bg-blue-600/10 border border-blue-500/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-14m4 4L14 4M6 16l-4-4 4-4" /></svg>
            </div>
            <div>
                <h1 class="text-white font-bold text-xl tracking-tight uppercase italic">Monitorização do Sistema <span class="text-blue-500">v2.0</span></h1>
                <p class="text-[10px] text-zinc-500 uppercase tracking-widest italic">Hardware: Acer i5 / OS: Linux Kernel</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-30 transition-opacity">
                    <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v4h2V2h-2zm6.2 1.4l-2.8 2.8 1.4 1.4 2.8-2.8-1.4-1.4zM22 11h-4v2h4v-2zM6.8 4.2L4 7l1.4 1.4 2.8-2.8L6.8 4.2zM2 11v2h4v-2H2zm16.4 7.2l2.8 2.8 1.4-1.4-2.8-2.8-1.4 1.4zm-14.8 0l-1.4 1.4 2.8 2.8 1.4-1.4-2.8-2.8zM11 18v4h2v-4h-2z"/></svg>
                </div>
                <h3 class="text-zinc-500 text-[10px] font-bold uppercase mb-4 tracking-widest">Carga CPU (Avg)</h3>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-blue-500"><?php echo $load[0]; ?></span>
                    <span class="text-zinc-700 mb-1">/ <?php echo $load[1]; ?></span>
                </div>
                <div class="mt-4 h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all duration-1000" style="width: <?php echo min($load[0] * 10, 100); ?>%"></div>
                </div>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-2xl relative overflow-hidden group">
                 <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-30 transition-opacity">
                    <svg class="w-12 h-12 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13 13h-2v-2h2v2zm0-4h-2V7h2v2zm0 8h-2v-2h2v2zm4-4h-2v-2h2v2zm0-4h-2V7h2v2zm0 8h-2v-2h2v2zm-8 0H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zM19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                </div>
                <h3 class="text-zinc-500 text-[10px] font-bold uppercase mb-4 tracking-widest">Uso de Memória</h3>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-emerald-500"><?php echo $memoria_uso; ?></span>
                    <span class="text-zinc-700 mb-1">MB</span>
                </div>
                <p class="text-[10px] text-zinc-600 mt-2 uppercase italic">Total disponível: <?php echo $memoria_total; ?> MB</p>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-2xl relative overflow-hidden group">
                 <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-30 transition-opacity">
                    <svg class="w-12 h-12 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7h-8L10 5H4c-1.1 0-1.99.9-1.99 2L2 17c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 10H4V9h16v8z"/></svg>
                </div>
                <h3 class="text-zinc-500 text-[10px] font-bold uppercase mb-4 tracking-widest">Armazenamento SSD</h3>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-orange-500"><?php echo explode(' ', $disco_info)[0]; ?></span>
                    <span class="text-zinc-700 mb-1">EM USO</span>
                </div>
                <p class="text-[10px] text-zinc-600 mt-2 uppercase italic"><?php echo $disco_info; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-zinc-900/20 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl">
                <div class="bg-zinc-900/60 px-6 py-4 flex items-center justify-between border-b border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse shadow-[0_0_8px_#3b82f6]"></div>
                        <span class="text-[10px] font-bold tracking-widest uppercase">Docker Runtime</span>
                    </div>
                </div>
                <div class="p-6">
                    <pre class="text-[10px] font-mono text-blue-400 bg-black/40 p-5 rounded-2xl border border-blue-500/10 min-h-[150px] leading-relaxed"><?php echo $docker_ps; ?></pre>
                </div>
            </div>

            <div class="bg-zinc-900/20 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl">
                <div class="bg-zinc-900/60 px-6 py-4 flex items-center justify-between border-b border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span class="text-[10px] font-bold tracking-widest uppercase text-zinc-400">Activity Logs (Events)</span>
                    </div>
                    <span class="text-[9px] font-mono text-zinc-600 uppercase"><?php echo $uptime; ?></span>
                </div>
                <div class="p-6">
                    <pre class="text-[10px] font-mono text-red-400/70 bg-black/40 p-5 rounded-2xl border border-red-500/10 min-h-[150px] leading-relaxed"><?php echo htmlspecialchars($logs ?? 'nenhum log disponível'); ?></pre>
                </div>
            </div>

        </div>

        <footer class="mt-12 text-center">
             <p class="text-[9px] font-bold text-zinc-800 uppercase tracking-[0.3em]">NPaper Engineering Division • Secure Internal Access</p>
        </footer>
    </main>
</body>
</html>