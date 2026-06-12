-- Script de inicialização do Banco de Dados NPaper
-- Cria as tabelas necessárias e inicializa o usuário 'carlos' como estudante (aluno).

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nivel VARCHAR(20) NOT NULL DEFAULT 'aluno'
);

CREATE TABLE IF NOT EXISTS livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL UNIQUE,
    titulo VARCHAR(255) NOT NULL,
    capa VARCHAR(255) DEFAULT NULL,
    categoria VARCHAR(100) DEFAULT NULL,
    downloads INT DEFAULT 0,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS favoritos (
    usuario_id INT NOT NULL,
    livro_id INT NOT NULL,
    PRIMARY KEY (usuario_id, livro_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notas (
    usuario_id INT NOT NULL,
    livro_id INT NOT NULL,
    conteudo TEXT NOT NULL,
    publica TINYINT(1) DEFAULT 0,
    PRIMARY KEY (usuario_id, livro_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE
);

-- Insere o usuário 'carlos' se ele não existir
-- A senha de carlos será 'carlos123'
INSERT IGNORE INTO usuarios (username, password_hash, nivel) VALUES 
('carlos', '$2y$10$ZdHd7m3rCUnmH0rISPx0KuYiFzG7DTJBt/wnuMk3a.WEowu6mkyEi', 'aluno');
