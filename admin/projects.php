<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
requireLogin();

// Buscar projetos
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar projetos: " . $e->getMessage());
    $projects = [];
}

// Renderizar o cabeçalho
require_once 'includes/layout.php';
renderHeader('Projetos - João Fontora');
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Projetos</h1>
    <button class="btn btn-light d-flex align-items-center gap-2" onclick="showProjectModal()">
        <span class="material-symbols-rounded">add</span>
        <span>Novo Projeto</span>
    </button>
</div>

<!-- Projects Grid -->
<div class="row g-4">
    <?php foreach ($projects as $project): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <?php if ($project['image_url']): ?>
            <div class="card-img-wrapper">
                <img src="<?php echo htmlspecialchars($project['image_url']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
                <?php if ($project['featured']): ?>
                <span class="badge bg-primary featured-badge">
                    <span class="material-symbols-rounded">star</span>
                    Destaque
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="card-body">
                <h4 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                <p class="card-text text-secondary">
                    <?php echo htmlspecialchars($project['description']); ?>
                </p>
                <div class="technologies-list mb-3">
                    <?php 
                    $technologies = explode(',', $project['technologies']);
                    foreach ($technologies as $tech): 
                        $tech = trim($tech);
                        if (!empty($tech)):
                    ?>
                    <span class="tech-badge">
                        <?php echo htmlspecialchars($tech); ?>
                    </span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <div class="project-links mb-3">
                    <?php if ($project['github_url']): ?>
                    <a href="<?php echo htmlspecialchars($project['github_url']); ?>" 
                       class="btn btn-light btn-sm d-flex align-items-center gap-2" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                        GitHub
                    </a>
                    <?php endif; ?>
                    <?php if ($project['live_url']): ?>
                    <a href="<?php echo htmlspecialchars($project['live_url']); ?>" 
                       class="btn btn-light btn-sm d-flex align-items-center gap-2" target="_blank">
                        <span class="material-symbols-rounded">launch</span>
                        Ver Projeto
                    </a>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="showProjectModal(<?php echo $project['id']; ?>)" 
                            class="btn btn-light btn-sm d-flex align-items-center gap-2"
                            title="Editar projeto">
                        <span class="material-symbols-rounded text-primary">edit</span>
                        Editar
                    </button>
                    <button onclick="deleteProject(<?php echo $project['id']; ?>)" 
                            class="btn btn-light btn-sm d-flex align-items-center gap-2"
                            title="Excluir projeto">
                        <span class="material-symbols-rounded text-danger">delete</span>
                        Excluir
                    </button>
                </div>
            </div>
            <div class="card-footer text-secondary">
                <small>
                    <span class="material-symbols-rounded">calendar_today</span>
                    <?php echo date('d/m/Y', strtotime($project['created_at'])); ?>
                </small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Projeto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    <input type="hidden" name="id" id="project_id">
                    <input type="hidden" name="action" id="project_action" value="add">
                    <input type="hidden" name="image_url" id="image_url">
                    <input type="hidden" name="technologies" id="technologies">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tecnologias</label>
                        <div class="tags-input-wrapper">
                            <div class="tags-container"></div>
                            <input type="text" id="tag-input" class="form-control" placeholder="Digite uma tecnologia e pressione Enter">
                        </div>
                        <div class="tech-suggestions mt-2">
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('HTML')">HTML</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('CSS')">CSS</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('JavaScript')">JavaScript</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('React')">React</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('PHP')">PHP</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('MySQL')">MySQL</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('Node.js')">Node.js</span>
                            <span class="suggestion-tag btn btn-light btn-sm" onclick="addTag('Bootstrap')">Bootstrap</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="github_url" class="form-label">URL do GitHub</label>
                        <input type="url" id="github_url" name="github_url" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="live_url" class="form-label">URL do Projeto</label>
                        <input type="url" id="live_url" name="live_url" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Selecione uma Imagem</label>
                        <div class="image-grid"></div>
                        <div class="selected-image-preview mt-3" id="selectedImagePreview"></div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" id="featured" name="featured" class="form-check-input">
                        <label class="form-check-label" for="featured">Projeto em Destaque</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light d-flex align-items-center gap-2" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">close</span>
                    Cancelar
                </button>
                <button type="button" class="btn btn-light d-flex align-items-center gap-2" onclick="saveProject()">
                    <span class="material-symbols-rounded text-primary">save</span>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--border);
    background: var(--card-bg);
    backdrop-filter: blur(10px);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 20px;
    background: var(--gradient-primary);
    font-size: 0.875rem;
}

.tech-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    margin: 2px;
    border-radius: 4px;
    background: var(--primary);
    color: white;
    font-size: 0.75rem;
    white-space: nowrap;
}

.project-links {
    display: flex;
    gap: 8px;
}

.project-links .btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.card-footer {
    background: transparent;
    border-top: 1px solid var(--border);
}

.card-footer small {
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-footer .material-symbols-rounded {
    font-size: 1rem;
}

@media (max-width: 768px) {
    .card-img-top {
        height: 160px;
    }
}

.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.image-option {
    position: relative;
    aspect-ratio: 16/9;
    cursor: pointer;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.image-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.image-option.selected {
    border-color: var(--primary);
}

.image-option img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-option:hover img {
    transform: scale(1.05);
}

.selected-image-preview {
    display: none;
    text-align: center;
    padding: 1rem;
    background: var(--dark);
    border-radius: 8px;
    margin-top: 1rem;
}

.selected-image-preview.show {
    display: block;
}

.selected-image-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 4px;
}

.tags-input-wrapper {
    border: 1px solid var(--border);
    border-radius: 0.375rem;
    padding: 0.5rem;
    background: var(--dark);
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    background: var(--primary);
    color: white;
    border-radius: 1rem;
    font-size: 0.875rem;
}

.tag .remove-tag {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    font-size: 0.75rem;
}

.tag .remove-tag:hover {
    background: rgba(255, 255, 255, 0.3);
}

#tag-input {
    border: none;
    padding: 0.375rem 0;
    background: transparent;
}

#tag-input:focus {
    outline: none;
    box-shadow: none;
}

.tech-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.suggestion-tag {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--dark);
    border: 1px solid var(--border);
    border-radius: 1rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.suggestion-tag:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
</style>

<script>
let projectModal;
let techImages = {
    'frontend': [
        'https://cdn.pixabay.com/photo/2016/11/19/14/00/code-1839406_1280.jpg',
        'https://cdn.pixabay.com/photo/2015/04/20/13/17/work-731198_1280.jpg',
        'https://cdn.pixabay.com/photo/2015/04/23/17/41/javascript-736400_1280.png',
        'https://cdn.pixabay.com/photo/2017/08/05/11/16/logo-2582748_1280.png', // CSS
        'https://cdn.pixabay.com/photo/2017/08/05/11/16/logo-2582747_1280.png'  // HTML
    ],
    'backend': [
        'https://cdn.pixabay.com/photo/2016/09/08/04/12/programmer-1653351_1280.png',
        'https://cdn.pixabay.com/photo/2017/11/10/05/24/select-2935439_1280.png',
        'https://cdn.pixabay.com/photo/2016/11/19/22/52/coding-1841550_1280.jpg'
    ],
    'fullstack': [
        'https://cdn.pixabay.com/photo/2016/11/30/20/58/programming-1873854_1280.png',
        'https://cdn.pixabay.com/photo/2018/05/08/08/44/artificial-intelligence-3382507_1280.jpg',
        'https://cdn.pixabay.com/photo/2016/12/28/09/36/web-1935737_1280.png'
    ],
    'design': [
        'https://cdn.pixabay.com/photo/2017/01/29/13/21/mobile-devices-2017978_1280.png',
        'https://cdn.pixabay.com/photo/2017/01/29/13/20/mobile-2017976_1280.png',
        'https://cdn.pixabay.com/photo/2017/01/29/13/21/online-2017979_1280.png'
    ],
    'database': [
        'https://cdn.pixabay.com/photo/2017/06/14/16/20/network-2402637_1280.jpg',
        'https://cdn.pixabay.com/photo/2016/11/27/21/42/stock-1863880_1280.jpg',
        'https://cdn.pixabay.com/photo/2016/10/06/19/59/binary-1719644_1280.jpg'
    ],
    'default': [
        'https://cdn.pixabay.com/photo/2016/11/19/14/00/code-1839406_1280.jpg',
        'https://cdn.pixabay.com/photo/2018/05/08/08/44/artificial-intelligence-3382507_1280.jpg',
        'https://cdn.pixabay.com/photo/2016/11/30/20/58/programming-1873854_1280.png'
    ]
};

// Mapeamento de tecnologias para categorias
let techCategories = {
    frontend: ['html', 'css', 'javascript', 'js', 'react', 'vue', 'angular', 'typescript', 'ts', 'bootstrap', 'tailwind', 'sass', 'less', 'jquery'],
    backend: ['php', 'python', 'java', 'node', 'express', 'django', 'laravel', 'spring', 'ruby', 'go', 'rust'],
    fullstack: ['mern', 'mean', 'lamp', 'fullstack', 'full-stack', 'web'],
    design: ['ui', 'ux', 'figma', 'adobe', 'photoshop', 'illustrator', 'xd', 'design'],
    database: ['sql', 'mysql', 'postgresql', 'mongodb', 'database', 'oracle', 'redis', 'firebase']
};

let tags = new Set();

document.addEventListener('DOMContentLoaded', function() {
    projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
    
    const tagInput = document.getElementById('tag-input');
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag) {
                addTag(tag);
                this.value = '';
            }
        }
    });
    
    // Atualizar imagens quando as tags mudarem
    updateTechnologies();
});

function addTag(tag) {
    if (!tags.has(tag)) {
        tags.add(tag);
        const tagsContainer = document.querySelector('.tags-container');
        const tagElement = document.createElement('span');
        tagElement.className = 'tag';
        tagElement.innerHTML = `
            ${tag}
            <span class="remove-tag" onclick="removeTag('${tag}')">&times;</span>
        `;
        tagsContainer.appendChild(tagElement);
        updateTechnologies();
    }
}

function removeTag(tag) {
    tags.delete(tag);
    updateTechnologies();
    renderTags();
}

function renderTags() {
    const tagsContainer = document.querySelector('.tags-container');
    tagsContainer.innerHTML = '';
    tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.className = 'tag';
        tagElement.innerHTML = `
            ${tag}
            <span class="remove-tag" onclick="removeTag('${tag}')">&times;</span>
        `;
        tagsContainer.appendChild(tagElement);
    });
}

function updateTechnologies() {
    // Atualizar campo hidden com as tags
    document.getElementById('technologies').value = Array.from(tags).join(', ');
    // Atualizar imagens baseadas nas tags
    updateImageOptions();
}

function updateImageOptions() {
    const technologies = Array.from(tags).map(t => t.toLowerCase());
    const imageGrid = document.querySelector('.image-grid');
    imageGrid.innerHTML = ''; // Limpar grid atual
    
    let usedImages = new Set();
    let selectedImages = [];
    let usedCategories = new Set();
    
    // Identificar categorias baseadas nas tecnologias
    technologies.forEach(tech => {
        for (let [category, techList] of Object.entries(techCategories)) {
            if (techList.some(t => tech.includes(t)) && !usedCategories.has(category)) {
                usedCategories.add(category);
                const categoryImages = techImages[category];
                categoryImages.forEach(img => {
                    if (!usedImages.has(img)) {
                        selectedImages.push(img);
                        usedImages.add(img);
                    }
                });
            }
        }
    });
    
    // Se não houver imagens específicas ou tecnologias, usar as padrão
    if (selectedImages.length === 0 || technologies.length === 0) {
        selectedImages = techImages.default;
    }
    
    // Limitar a 6 imagens e adicionar ao grid
    selectedImages.slice(0, 6).forEach(imageUrl => {
        const div = document.createElement('div');
        div.className = 'image-option';
        div.onclick = () => selectImage(imageUrl);
        div.innerHTML = `<img src="${imageUrl}" alt="Imagem do Projeto">`;
        imageGrid.appendChild(div);
    });
}

function selectImage(url) {
    // Remover seleção anterior
    document.querySelectorAll('.image-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Selecionar nova imagem
    const selectedOption = document.querySelector(`.image-option img[src="${url}"]`).parentElement;
    selectedOption.classList.add('selected');
    
    // Atualizar campo hidden
    document.getElementById('image_url').value = url;
    
    // Atualizar preview
    const preview = document.getElementById('selectedImagePreview');
    preview.innerHTML = `
        <p class="mb-2">Imagem selecionada:</p>
        <img src="${url}" alt="Preview">
    `;
    preview.classList.add('show');
}

function showProjectModal(id = null) {
    const form = document.getElementById('projectForm');
    form.reset();
    document.getElementById('project_action').value = id ? 'edit' : 'add';
    
    // Limpar tags
    tags.clear();
    renderTags();
    
    // Limpar seleção de imagem
    document.querySelectorAll('.image-option').forEach(option => {
        option.classList.remove('selected');
    });
    document.getElementById('selectedImagePreview').classList.remove('show');
    document.getElementById('image_url').value = '';
    
    // Atualizar grid de imagens com as padrão
    updateImageOptions();
    
    if (id) {
        // Carregar dados do projeto para edição
        fetch(`api/manage_projects.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const project = data.project;
                    document.getElementById('project_id').value = project.id;
                    document.getElementById('project_action').value = 'edit';
                    document.getElementById('title').value = project.title;
                    document.getElementById('description').value = project.description;
                    document.getElementById('github_url').value = project.github_url;
                    document.getElementById('live_url').value = project.live_url;
                    document.getElementById('featured').checked = project.featured == 1;
                    
                    // Carregar tecnologias
                    const techContainer = document.querySelector('.tags-container');
                    techContainer.innerHTML = '';
                    if (project.technologies) {
                        project.technologies.split(',').forEach(tech => {
                            if (tech.trim()) {
                                addTag(tech.trim());
                            }
                        });
                    }
                    
                    // Mostrar imagem selecionada
                    if (project.image_url) {
                        document.getElementById('image_url').value = project.image_url;
                        const preview = document.getElementById('selectedImagePreview');
                        preview.innerHTML = `<img src="${project.image_url}" class="img-fluid rounded">`;
                    }
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao carregar projeto'
                });
            });
    }
    
    projectModal.show();
}

function saveProject() {
    const form = document.getElementById('projectForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Verificar se uma imagem foi selecionada
    const imageUrl = document.getElementById('image_url').value;
    if (!imageUrl) {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Por favor, selecione uma imagem para o projeto'
        });
        return;
    }
    
    const formData = new FormData(form);
    
    fetch('api/manage_projects.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Projeto salvo com sucesso!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                projectModal.hide();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao salvar projeto'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao salvar projeto'
        });
    });
}

function deleteProject(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('api/manage_projects.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'O projeto foi excluído com sucesso.',
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Erro ao excluir projeto',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'Erro!',
                    'Erro ao excluir projeto',
                    'error'
                );
            });
        }
    });
}
</script>

<!-- Adicionar SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<?php
// Renderizar o rodapé
renderFooter();
?> 