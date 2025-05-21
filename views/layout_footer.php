</main>
    
    <!-- Footer con los nuevos colores corporativos -->
    <footer class="bg-white py-4 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600">
                    © <?php echo date('Y'); ?> MEP-Projects. Todos los derechos reservados.
                </div>
                <div class="text-sm text-gray-600 mt-2 md:mt-0 flex items-center space-x-4">
                    <span>v1.1.0</span>
                    <a href="#" class="text-mep-primary hover:text-mep-primary-dark transition-colors">Soporte</a>
                    <a href="#" class="text-mep-primary hover:text-mep-primary-dark transition-colors">Documentación</a>
                    <a href="#" class="text-mep-primary hover:text-mep-primary-dark transition-colors">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Toast de notificaciones -->
    <div id="toast-container"></div>
    
    <script>
        // Inicializar los iconos de Lucide
        lucide.createIcons();
        
        // Sistema de notificaciones toast
        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="${type === 'success' ? 'check-circle' : type === 'warning' ? 'alert-triangle' : type === 'error' ? 'x-circle' : 'info'}" 
                       class="w-5 h-5 mr-3 ${type === 'success' ? 'text-green-500' : type === 'warning' ? 'text-yellow-500' : type === 'error' ? 'text-red-500' : 'text-blue-500'}"></i>
                    <div>${message}</div>
                </div>
                <button class="ml-4 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            
            container.appendChild(toast);
            lucide.createIcons({ icons: toast.querySelectorAll('[data-lucide]') });
            
            // Añadir evento para cerrar el toast
            const closeBtn = toast.querySelector('button');
            closeBtn.addEventListener('click', () => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
            });
            
            // Auto-eliminar después de la duración
            setTimeout(() => {
                if (container.contains(toast)) {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (container.contains(toast)) {
                            container.removeChild(toast);
                        }
                    }, 300);
                }
            }, duration);
        }
        
        // Detectar mensajes de sesión para mostrar toasts
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['success'])): ?>
                showToast("<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>", 'success');
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                showToast("<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>", 'error');
            <?php endif; ?>
            
            <?php if (isset($_SESSION['warning'])): ?>
                showToast("<?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>", 'warning');
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info'])): ?>
                showToast("<?php echo $_SESSION['info']; unset($_SESSION['info']); ?>", 'info');
            <?php endif; ?>
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>