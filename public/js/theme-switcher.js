document.addEventListener('DOMContentLoaded', () => {
    const themeSwitcher = document.getElementById('theme-switcher');
    const currentTheme = localStorage.getItem('theme');

    // 1. Ao carregar a página, verifica se já existe um tema salvo
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeSwitcher.textContent = '☀️'; // Mostra o sol no tema escuro
    } else {
        themeSwitcher.textContent = '🌙'; // Mostra a lua no tema claro
    }

    // 2. Adiciona o evento de clique no botão
    themeSwitcher.addEventListener('click', () => {
        // Adiciona ou remove a classe do body
        document.body.classList.toggle('dark-mode');

        // 3. Salva a nova preferência no LocalStorage
        let theme = 'light';
        if (document.body.classList.contains('dark-mode')) {
            theme = 'dark';
            themeSwitcher.textContent = '☀️';
        } else {
            themeSwitcher.textContent = '🌙';
        }
        localStorage.setItem('theme', theme);
    });
});

