// Theme Toggle
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;
const themeIcon = themeToggle.querySelector('.material-symbols-rounded');

// Carregar tema salvo
const savedTheme = localStorage.getItem('theme') || 'dark';
html.setAttribute('data-bs-theme', savedTheme);
updateThemeIcon(savedTheme);

themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
});

function updateThemeIcon(theme) {
    themeIcon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
}

// Sidebar Toggle para Mobile
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main-content');

function toggleSidebar() {
    if (window.innerWidth <= 991) {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
}

// Fechar sidebar ao clicar em um link (mobile)
document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 991) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    });
});

// Animações de entrada
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

// Confirmação de exclusão
function confirmDelete(message = 'Tem certeza que deseja excluir este item?') {
    return confirm(message);
}

// Toast Notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="material-symbols-rounded">
                ${type === 'success' ? 'check_circle' : 'error'}
            </span>
            <span class="toast-message">${message}</span>
        </div>
        <button class="toast-close">
            <span class="material-symbols-rounded">close</span>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Animação de entrada
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Auto close
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
    
    // Close button
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    });
}

// Form Validation
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
            
            // Criar mensagem de erro se não existir
            if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Este campo é obrigatório';
                field.parentNode.appendChild(feedback);
            }
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Preview de Imagem
function previewImage(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Drag and Drop para Upload
function initDragAndDrop(dropZone, input) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropZone.classList.add('drag-over');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('drag-over');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        input.files = files;
        
        // Trigger change event
        const event = new Event('change');
        input.dispatchEvent(event);
    }
}

// Máscara para campos de telefone
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    input.value = value;
}

// Exportar funções
window.confirmDelete = confirmDelete;
window.showToast = showToast;
window.validateForm = validateForm;
window.previewImage = previewImage;
window.initDragAndDrop = initDragAndDrop;
window.maskPhone = maskPhone; 