<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Acesso Negado | NPaper</title>
</head>
<body class="bg-zinc-950 text-white flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center">
        <div class="mb-6 flex justify-center">
            <div class="bg-red-500/10 p-5 rounded-full border border-red-500/20">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-2 uppercase tracking-tight">Área Restrita</h1>
        <p class="text-zinc-500 text-sm mb-8">
            Seu nível de acesso atual não permite visualizar esta página de infraestrutura. 
            Se você é o técnico, tente refazer o login para atualizar suas permissões.
        </p>

        <div class="flex flex-col gap-3">
            <a href="index.php" class="bg-zinc-100 hover:bg-white text-black font-bold py-3 rounded-xl transition-all text-sm">
                VOLTAR PARA O ACERVO
            </a>
            <a href="logout.php" class="border border-zinc-800 hover:bg-zinc-900 text-zinc-400 font-bold py-3 rounded-xl transition-all text-sm">
                REFAZER LOGIN
            </a>
        </div>
        
        <p class="mt-8 text-[10px] text-zinc-700 font-mono">ERR_CODE: INSUFFICIENT_PERMISSIONS_LEVEL_<?php echo $_GET['lvl'] ?? 'UNK'; ?></p>
    </div>
</body>
</html>