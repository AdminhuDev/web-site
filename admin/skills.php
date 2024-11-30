<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
requireLogin();

// Buscar habilidades agrupadas por categoria
try {
    $stmt = $pdo->query("SELECT * FROM skills ORDER BY category, name");
    $skills = $stmt->fetchAll();
    
    // Agrupar habilidades por categoria
    $grouped_skills = [];
    foreach ($skills as $skill) {
        $grouped_skills[$skill['category']][] = $skill;
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar habilidades: " . $e->getMessage());
    $grouped_skills = [];
}

// Renderizar o cabeçalho
require_once 'includes/layout.php';
renderHeader('Habilidades - João Fontora');
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Habilidades</h1>
    <button class="btn btn-light d-flex align-items-center gap-2" onclick="showSkillModal()">
        <span class="material-symbols-rounded">add</span>
        <span>Nova Habilidade</span>
    </button>
</div>

<!-- Skills Grid -->
<div class="row g-4">
    <?php foreach ($grouped_skills as $category => $skills): ?>
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center mb-3">
            <h2 class="h4 mb-0"><?php echo htmlspecialchars($category); ?></h2>
            <div class="border-top flex-grow-1 ms-3"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($skills as $skill): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="material-symbols-rounded text-primary fs-1 me-3">
                                <?php echo htmlspecialchars($skill['icon']); ?>
                            </span>
                            <h4 class="card-title mb-0">
                                <?php echo htmlspecialchars($skill['name']); ?>
                            </h4>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" 
                                 style="width: <?php echo $skill['proficiency']; ?>%"></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="showSkillModal(<?php echo $skill['id']; ?>)" 
                                    class="btn btn-light btn-sm d-flex align-items-center"
                                    title="Editar habilidade">
                                <span class="material-symbols-rounded text-primary">edit</span>
                            </button>
                            <button onclick="deleteSkill(<?php echo $skill['id']; ?>)" 
                                    class="btn btn-light btn-sm d-flex align-items-center"
                                    title="Excluir habilidade">
                                <span class="material-symbols-rounded text-danger">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Skill Modal -->
<div class="modal fade" id="skillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Habilidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="skillForm">
                    <input type="hidden" name="id" id="skill_id">
                    <input type="hidden" name="action" id="skill_action" value="add">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" id="name" name="name" 
                               class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoria</label>
                        <input type="text" id="category" name="category" 
                               class="form-control" 
                               list="categories" required>
                        <datalist id="categories">
                            <?php foreach (array_keys($grouped_skills) as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <div class="mb-3">
                        <label for="proficiency" class="form-label">
                            Proficiência (%) - <span id="proficiencyValue">50%</span>
                        </label>
                        <input type="range" id="proficiency" name="proficiency" 
                               class="form-range" min="0" max="100" value="50"
                               oninput="document.getElementById('proficiencyValue').textContent = this.value + '%'">
                    </div>
                    
                    <small class="text-secondary mb-3 d-block">
                        O ícone será definido automaticamente com base no nome da habilidade.
                    </small>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light d-flex align-items-center gap-2" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">close</span>
                    Cancelar
                </button>
                <button type="button" class="btn btn-light d-flex align-items-center gap-2" onclick="saveSkill()">
                    <span class="material-symbols-rounded text-primary">save</span>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let skillModal;

document.addEventListener('DOMContentLoaded', function() {
    skillModal = new bootstrap.Modal(document.getElementById('skillModal'));
});

function showSkillModal(id = null) {
    const form = document.getElementById('skillForm');
    form.reset();
    document.getElementById('skill_action').value = id ? 'edit' : 'add';
    
    if (id) {
        // Carregar dados da habilidade para edição
        fetch(`api/manage_skills.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const skill = data.skill;
                    document.getElementById('skill_id').value = skill.id;
                    document.getElementById('name').value = skill.name;
                    document.getElementById('category').value = skill.category;
                    document.getElementById('proficiency').value = skill.proficiency;
                    document.getElementById('proficiencyValue').textContent = skill.proficiency + '%';
                }
            });
    }
    
    skillModal.show();
}

function saveSkill() {
    const form = document.getElementById('skillForm');
    const formData = new FormData(form);
    
    fetch('api/manage_skills.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Habilidade salva com sucesso!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                skillModal.hide();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao salvar habilidade'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao salvar habilidade'
        });
    });
}

function deleteSkill(id) {
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
            
            fetch('api/manage_skills.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'A habilidade foi excluída com sucesso.',
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Erro ao excluir habilidade',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'Erro!',
                    'Erro ao excluir habilidade',
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