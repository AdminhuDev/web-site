// Theme System
class ThemeManager {
    constructor() {
        this.themeToggle = document.getElementById('theme-toggle');
        this.html = document.documentElement;
        this.darkTheme = {
            primary: '#6366F1',
            primaryDark: '#4F46E5',
            dark: '#111827',
            darker: '#0A0F1D',
            textPrimary: '#F8FAFC',
            textSecondary: '#94A3B8',
            border: 'rgba(255, 255, 255, 0.1)',
            cardBg: 'rgba(17, 24, 39, 0.7)',
            navbarBg: 'rgba(17, 24, 39, 0.8)'
        };
        this.lightTheme = {
            primary: '#4F46E5',
            primaryDark: '#3730A3',
            dark: '#FFFFFF',
            darker: '#F8FAFC',
            textPrimary: '#111827',
            textSecondary: '#4B5563',
            border: 'rgba(0, 0, 0, 0.1)',
            cardBg: 'rgba(255, 255, 255, 0.7)',
            navbarBg: 'rgba(255, 255, 255, 0.8)'
        };
        this.init();
    }

    init() {
        if (this.themeToggle) {
            // Carregar tema salvo ou usar tema do sistema
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const initialTheme = savedTheme || systemTheme;
            
            this.applyTheme(initialTheme);
            
            // Adicionar evento de clique
            this.themeToggle.addEventListener('click', () => {
                const newTheme = this.html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                this.applyTheme(newTheme);
            });

            // Observar mudanças no tema do sistema
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    this.applyTheme(e.matches ? 'dark' : 'light');
                }
            });
        }
    }

    applyTheme(theme) {
        const themeIcon = this.themeToggle.querySelector('.material-symbols-rounded');
        const colors = theme === 'dark' ? this.darkTheme : this.lightTheme;
        
        // Aplicar tema ao HTML
        this.html.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Atualizar ícone
        if (themeIcon) {
            themeIcon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
        }
        
        // Atualizar variáveis CSS
        Object.entries(colors).forEach(([key, value]) => {
            const cssKey = key.replace(/[A-Z]/g, m => `-${m.toLowerCase()}`);
            document.documentElement.style.setProperty(`--${cssKey}`, value);
        });
        
        // Atualizar gradientes
        document.documentElement.style.setProperty(
            '--gradient-primary',
            `linear-gradient(135deg, ${colors.primaryDark}, ${colors.primary})`
        );
        
        // Atualizar background dos cards
        document.documentElement.style.setProperty(
            '--card-bg',
            theme === 'dark' ? 'rgba(17, 24, 39, 0.7)' : 'rgba(255, 255, 255, 0.7)'
        );
        
        // Atualizar background da navbar
        document.documentElement.style.setProperty(
            '--navbar-bg',
            theme === 'dark' ? 'rgba(17, 24, 39, 0.8)' : 'rgba(255, 255, 255, 0.8)'
        );
    }
}

// Inicializar sistema de tema
document.addEventListener('DOMContentLoaded', () => {
    window.themeManager = new ThemeManager();
    
    // Botão Voltar ao Topo
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        // Mostrar/ocultar botão baseado no scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        // Ação de clique no botão
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}); 