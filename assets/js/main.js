// Firebase Data Loader
import { db } from './firebase-config.js';
import { collection, query, where, orderBy, getDocs, addDoc } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";

// Utilitários
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Gerenciamento de Tema
function initTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Carregar tema salvo ou usar preferência do sistema
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (themeToggle) {
            themeToggle.checked = savedTheme === 'dark';
        }
    } else {
        const systemTheme = prefersDarkScheme.matches ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', systemTheme);
        if (themeToggle) {
            themeToggle.checked = systemTheme === 'dark';
        }
    }

    updateThemeIcon();

    if (themeToggle) {
        themeToggle.addEventListener('change', () => {
            const newTheme = themeToggle.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        });
    }

    prefersDarkScheme.addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            const systemTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', systemTheme);
            if (themeToggle) {
                themeToggle.checked = systemTheme === 'dark';
            }
            updateThemeIcon();
        }
    });
}

function updateThemeIcon() {
    const themeIcon = document.querySelector('.theme-icon');
    if (themeIcon) {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        themeIcon.textContent = currentTheme === 'dark' ? 'dark_mode' : 'light_mode';
    }
}

// Animações e UI
function initAnimations() {
    const navbar = document.querySelector('.navbar');
    
    // Navbar Scroll Effect
    const handleScroll = debounce(() => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }, 10);
    window.addEventListener('scroll', handleScroll);

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = navbar.offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Navbar Active Link
    const sections = document.querySelectorAll('section[id]');
    const handleNavHighlight = debounce(() => {
        const scrollY = window.pageYOffset;
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - navbar.offsetHeight - 100;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
            
            if (navLink && scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                navLink.classList.add('active');
            }
        });
    }, 100);
    window.addEventListener('scroll', handleNavHighlight);

    // Background Animation
    const spheres = document.querySelectorAll('.gradient-sphere');
    spheres.forEach(sphere => {
        let animationFrame;
        const animate = () => {
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            sphere.style.transform = `translate(${x}%, ${y}%)`;
            animationFrame = setTimeout(animate, 3000);
        };
        animate();
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    clearTimeout(animationFrame);
                } else {
                    animate();
                }
            });
        });
        observer.observe(sphere);
    });

    // Entrada de Elementos
    const elementObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                elementObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });

    document.querySelectorAll('.project-card, .skill-card').forEach(el => {
        el.classList.add('animate-item');
        elementObserver.observe(el);
    });

    // Botão Voltar ao Topo
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        const handleBackToTopVisibility = debounce(() => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        }, 100);

        window.addEventListener('scroll', handleBackToTopVisibility);

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Cleanup
    window.addEventListener('unload', () => {
        window.removeEventListener('scroll', handleScroll);
        window.removeEventListener('scroll', handleNavHighlight);
        window.removeEventListener('scroll', handleBackToTopVisibility);
    });
}

// Firebase Data Loading
async function loadProjects() {
    try {
        const projectsSection = document.querySelector('#projects .row');
        const q = query(collection(db, 'projects'), 
                       where('status', '==', 'active'),
                       orderBy('timestamp', 'desc'));
        
        const snapshot = await getDocs(q);
        projectsSection.innerHTML = '';

        snapshot.forEach(doc => {
            const project = doc.data();
            const projectHtml = `
                <div class="col-md-6 col-lg-4">
                    <article class="card project-card h-100">
                        <div class="card-img-wrapper">
                            <img src="${project.imageUrl}" 
                                 class="card-img-top" 
                                 alt="${project.title}"
                                 width="400"
                                 height="300"
                                 loading="lazy">
                            ${project.featured ? `
                                <span class="badge bg-primary featured-badge">
                                    <span class="material-symbols-rounded">star</span>
                                    Destaque
                                </span>
                            ` : ''}
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">${project.title}</h3>
                            <p class="card-text">${project.description}</p>
                            <div class="technologies-list mb-3">
                                ${project.technologies.map(tech => `
                                    <span class="tech-badge">${tech}</span>
                                `).join('')}
                            </div>
                            ${project.projectUrl ? `
                                <a href="${project.projectUrl}" class="btn btn-primary" target="_blank">
                                    <span class="material-symbols-rounded">link</span>
                                    Ver Projeto
                                </a>
                            ` : ''}
                        </div>
                    </article>
                </div>
            `;
            projectsSection.innerHTML += projectHtml;
        });
    } catch (error) {
        console.error('Erro ao carregar projetos:', error);
    }
}

async function loadSkills() {
    try {
        const skillsSection = document.querySelector('#skills');
        const q = query(collection(db, 'skills'), orderBy('category'));
        const snapshot = await getDocs(q);

        const skillsByCategory = {
            frontend: [],
            backend: [],
            devops: []
        };

        snapshot.forEach(doc => {
            const skill = doc.data();
            if (skillsByCategory[skill.category]) {
                skillsByCategory[skill.category].push(skill);
            }
        });

        Object.entries(skillsByCategory).forEach(([category, skills]) => {
            const categorySection = skillsSection.querySelector(`[data-category="${category}"] .row`);
            if (categorySection) {
                categorySection.innerHTML = '';
                
                skills.forEach(skill => {
                    const skillHtml = `
                        <div class="col-md-6 col-lg-4">
                            <div class="card skill-card">
                                <div class="card-body">
                                    <div class="skill-icon">
                                        <span class="material-symbols-rounded">${skill.icon}</span>
                                    </div>
                                    <h4 class="skill-title">${skill.name}</h4>
                                    <div class="progress" role="progressbar" aria-valuenow="${skill.level}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: ${skill.level}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    categorySection.innerHTML += skillHtml;
                });
            }
        });
    } catch (error) {
        console.error('Erro ao carregar habilidades:', error);
    }
}

async function handleContactForm(event) {
    event.preventDefault();
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    try {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Enviando...
        `;

        const formData = {
            name: form.name.value,
            email: form.email.value,
            message: form.message.value,
            timestamp: new Date(),
            status: 'new'
        };

        await addDoc(collection(db, 'messages'), formData);

        form.reset();
        const alert = document.createElement('div');
        alert.className = 'alert alert-success mt-3';
        alert.textContent = 'Mensagem enviada com sucesso!';
        form.appendChild(alert);

        setTimeout(() => alert.remove(), 5000);

    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger mt-3';
        alert.textContent = 'Erro ao enviar mensagem. Por favor, tente novamente.';
        form.appendChild(alert);
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initAnimations();
    loadProjects();
    loadSkills();
    
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactForm);
    }
}); 