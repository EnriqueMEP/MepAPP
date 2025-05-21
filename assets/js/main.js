// Funciones principales para MEP-Projects

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    initializeComponents();
    
    // Activar animaciones
    enableAnimations();
});

function initializeComponents() {
    // Ejemplo: Inicializar dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const toggleButton = dropdown.querySelector('button');
        if (toggleButton) {
            toggleButton.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });
        }
    });
    
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function() {
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });
}

function enableAnimations() {
    // Añadir clases de animación a elementos que entran en viewport
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
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
}