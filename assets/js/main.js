// Funciones principales para MEP-Projects

document.addEventListener('DOMContentLoaded', function () {
    initializeComponents();
    enableAnimations();
    initializeMessageCheck();
});

function initializeComponents() {
    const dropdowns = [
        {
            toggle: document.getElementById('notifications-dropdown'),
            menu: document.getElementById('notifications-menu')
        },
        {
            toggle: document.getElementById('user-dropdown'),
            menu: document.getElementById('user-menu')
        }
    ];

    dropdowns.forEach(dropdown => {
        if (dropdown.toggle && dropdown.menu) {
            dropdown.toggle.addEventListener('click', function (e) {
                e.stopPropagation();

                // Cerrar otros dropdowns
                dropdowns.forEach(d => {
                    if (d.menu !== dropdown.menu && d.menu.classList.contains('block')) {
                        d.menu.classList.remove('block');
                        d.menu.classList.add('hidden');
                    }
                });

                // Alternar visibilidad
                dropdown.menu.classList.toggle('hidden');
                dropdown.menu.classList.toggle('block');
            });
        }
    });

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function () {
        dropdowns.forEach(dropdown => {
            if (dropdown.menu && dropdown.menu.classList.contains('block')) {
                dropdown.menu.classList.remove('block');
                dropdown.menu.classList.add('hidden');
            }
        });
    });
}

function enableAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        });

        animatedElements.forEach(element => {
            observer.observe(element);
        });
    } else {
        // Fallback para navegadores antiguos
        animatedElements.forEach(element => {
            element.classList.add('animated');
        });
    }
}

function initializeMessageCheck() {
    const unreadBadge = document.getElementById('unread-badge');

    if (unreadBadge) {
        setInterval(checkNewMessages, 30000); // cada 30 segundos
    }
}

function checkNewMessages() {
    fetch('index.php?controller=chat&action=check_new&last_id=0')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            const unreadBadge = document.getElementById('unread-badge');

            if (data.unread_count > 0) {
                if (unreadBadge) {
                    unreadBadge.textContent = data.unread_count;
                    unreadBadge.classList.remove('hidden');
                } else {
                    const notificationBtn = document.querySelector('#notifications-dropdown button');
                    if (notificationBtn) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'unread-badge';
                        newBadge.className = 'absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full w-4 h-4 flex items-center justify-center';
                        newBadge.textContent = data.unread_count;
                        notificationBtn.appendChild(newBadge);
                    }
                }
            } else if (unreadBadge) {
                unreadBadge.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error al comprobar nuevos mensajes:', error);
        });
}
