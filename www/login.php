<?php
session_start();
$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    try {
        $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$user]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($pass, $usuario['password_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['username'];
            $_SESSION['usuario_nivel'] = $usuario['nivel'];

            header("Location: index.php");
            exit;
        } else {
            $erro = "Usuário ou senha inválidos!";
        }
    } catch (Exception $e) {
        $erro = "Erro de conexão com o banco.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | NPaper</title>
</head>

<body class="bg-zinc-950 text-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm px-6">
        <form method="POST" class="bg-zinc-900 p-8 rounded-3xl border border-zinc-800 shadow-2xl">
            <div class="flex justify-center mb-8">
                <div class="bg-orange-600 h-12 w-12 rounded-xl flex items-center justify-center shadow-lg shadow-orange-900/40">
                    <span class="text-white font-black text-2xl italic">N</span>
                </div>
            </div>

            <h2 class="text-xl font-bold mb-6 text-center tracking-tight">Acesse o Acervo</h2>

            <?php if ($erro): ?>
                <div class="text-red-500 text-xs mb-4 bg-red-500/10 p-3 rounded-xl border border-red-500/20 text-center">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <div class="space-y-4">
                <input type="text" name="username" placeholder="Usuário" required
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm">

                <input type="password" name="password" placeholder="Senha" required
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm">

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 py-3 rounded-xl font-bold text-sm transition-all shadow-lg shadow-orange-900/20">
                    ENTRAR NO SISTEMA
                </button>
            </div>
        </form>
    </div>
</body>

</html>