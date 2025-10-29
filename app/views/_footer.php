</main> <!-- Fecha o <main> aberto no _header.php -->

    <!-- Scripts (Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 
      SCRIPT DE LÓGICA DO TEMA
      Colocado no final para rodar após a página carregar.
    -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('theme-toggle-btn');
            
            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    // 1. Pega o tema atual (do <html>)
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    
                    // 2. Define o novo tema
                    const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
                    
                    // 3. Aplica o novo tema no <html>
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    
                    // 4. Salva a preferência no localStorage
                    localStorage.setItem('theme', newTheme);
                });
            }
        });
    </script>

    </body>
</html>
