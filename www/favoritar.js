function appData() {
    return {
        query: "",
        cat: "",
        res: { recentes: [], trending: [], categorias: [] },
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