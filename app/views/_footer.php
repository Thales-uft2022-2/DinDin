</main> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. LÓGICA DO BOTÃO DE TEMA ---
            const toggleButton = document.getElementById('theme-toggle-btn');
            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                });
            }

            // --- 2. LÓGICA DO MENU DE PERFIL (DROPDOWN) ---
            const profileBtn = document.getElementById('profile-menu-btn');
            const profileMenu = document.getElementById('profile-menu');
            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); 
                    profileMenu.classList.toggle('show');
                });
            }

            // ▼▼▼ 3. NOVA LÓGICA DO AVATAR (LÁPIS, MENU, BOTÕES) ▼▼▼
            const avatarEditBtn = document.getElementById('avatar-edit-btn');
            const avatarContextMenu = document.getElementById('avatar-context-menu');
            const avatarViewBtn = document.getElementById('avatar-view-btn');
            const avatarChangeBtn = document.getElementById('avatar-change-btn');
            const avatarDeleteBtn = document.getElementById('avatar-delete-btn');
            const avatarFileInput = document.getElementById('avatar-file-input');
            const avatarDeleteForm = document.getElementById('avatar-delete-form');
            const avatarLightbox = document.getElementById('avatar-lightbox');
            const avatarLightboxImg = document.getElementById('avatar-lightbox-img');
            const avatarPreviewImg = document.getElementById('avatar-preview-img');
            const avatarPreviewClickable = document.getElementById('avatar-preview-clickable');
            const avatarLightboxClose = document.querySelector('.avatar-lightbox-close');

            // Abrir/Fechar o menu do lápis
            if (avatarEditBtn && avatarContextMenu) {
                avatarEditBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    avatarContextMenu.classList.toggle('show');
                });
            }

            // Ação: Trocar Imagem (clica no input escondido)
            if (avatarChangeBtn && avatarFileInput) {
                avatarChangeBtn.addEventListener('click', function() {
                    avatarFileInput.click(); // Aciona o <input type="file">
                    if(avatarContextMenu) avatarContextMenu.classList.remove('show');
                });
            }

            // Ação: Apagar Imagem (envia o formulário escondido)
            if (avatarDeleteBtn && avatarDeleteForm) {
                avatarDeleteBtn.addEventListener('click', function() {
                    if (confirm('Tem certeza que deseja apagar sua foto de perfil?')) {
                        avatarDeleteForm.submit();
                    }
                    if(avatarContextMenu) avatarContextMenu.classList.remove('show');
                });
            }

            // Ação: Ver Imagem (Abre o Lightbox)
            const openLightbox = () => {
                // Só abre se tiver uma imagem (não a inicial)
                if (avatarPreviewImg && avatarLightbox && avatarLightboxImg) {
                    avatarLightbox.style.display = 'flex';
                    avatarLightboxImg.src = avatarPreviewImg.src;
                }
            };

            if (avatarViewBtn) {
                avatarViewBtn.addEventListener('click', function() {
                    openLightbox();
                    if(avatarContextMenu) avatarContextMenu.classList.remove('show');
                });
            }
            // Permite clicar na própria imagem para ver
            if (avatarPreviewClickable && avatarPreviewImg) { 
                avatarPreviewClickable.addEventListener('click', openLightbox);
            }

            // Ação: Fechar Lightbox
            if (avatarLightboxClose) {
                avatarLightboxClose.addEventListener('click', function() {
                    if(avatarLightbox) avatarLightbox.style.display = 'none';
                });
            }
            if (avatarLightbox) {
                avatarLightbox.addEventListener('click', function(e) {
                    // Fecha se clicar fora da imagem
                    if (e.target === avatarLightbox) {
                        avatarLightbox.style.display = 'none';
                    }
                });
            }
            
            // --- 4. LÓGICA GLOBAL (FECHAR MENUS) ---
            // Fecha os menus se clicar em qualquer outro lugar
            window.addEventListener('click', function(e) {
                // Fecha o menu de perfil (canto direito)
                if (profileMenu && profileMenu.classList.contains('show')) {
                    if (profileBtn && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.classList.remove('show');
                    }
                }
                
                // Fecha o menu do avatar (lápis)
                if (avatarContextMenu && avatarContextMenu.classList.contains('show')) {
                    if (avatarEditBtn && !avatarEditBtn.contains(e.target) && !avatarContextMenu.contains(e.target)) {
                        avatarContextMenu.classList.remove('show');
                    }
                }
            });
            // ▲▲▲ FIM DOS NOVOS SCRIPTS ▲▲▲
        });
    </script>

    </body>
</html>