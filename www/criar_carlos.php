<?php
/**
 * Script utilitário para criar o usuário 'carlos' (estudante/aluno)
 * no banco de dados se ele já estiver ativo e o volume persistente estiver inicializado.
 */

header('Content-Type: text/html; charset=utf-8');

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=db;dbname=npaper_db;charset=utf8mb4", "root", "root_password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Inicializando criação do usuário 'carlos'...</h3>";

    // Criação da tabela de usuários se por acaso não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        nivel VARCHAR(20) NOT NULL DEFAULT 'aluno'
    )");
    echo "Tabela 'usuarios' verificada/criada com sucesso.<br>";

    // Verifica se o usuário carlos já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute(['carlos']);
    $userExists = $stmt->fetch();

    if ($userExists) {
        echo "<b style='color: orange;'>O usuário 'carlos' já existe no banco de dados.</b><br>";
    } else {
        // Hashing da senha 'carlos123'
        $password = 'carlos123';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $nivel = 'aluno'; // Aluno equivale a estudante no sistema

        $insert = $pdo->prepare("INSERT INTO usuarios (username, password_hash, nivel) VALUES (?, ?, ?)");
        $insert->execute(['carlos', $hash, $nivel]);

        echo "<b style='color: green;'>Usuário 'carlos' criado com sucesso!</b><br>";
        echo "<b>Usuário:</b> carlos<br>";
        echo "<b>Senha:</b> carlos123<br>";
        echo "<b>Nível de acesso:</b> aluno (estudante)<br>";
    }

} catch (Exception $e) {
    echo "<b style='color: red;'>Erro ao conectar ou executar script:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
}
echo "<br><p>Por segurança, delete este arquivo (<code>criar_carlos.php</code>) após o uso em produção.</p>";
