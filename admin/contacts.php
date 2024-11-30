<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
requireLogin();

// Buscar contatos
try {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar contatos: " . $e->getMessage());
    $contacts = [];
}

// Renderizar o cabeçalho
require_once 'includes/layout.php';
renderHeader('Contatos - João Fontora');
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Contatos</h1>
</div>

<!-- Contacts List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Mensagem</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td>
                                    <span class="badge <?php echo $contact['is_read'] ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $contact['is_read'] ? 'Lido' : 'Não Lido'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>">
                                        <?php echo htmlspecialchars($contact['email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-link text-secondary p-0 view-message" 
                                            data-message="<?php echo htmlspecialchars($contact['message']); ?>">
                                        <?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . '...'; ?>
                                    </button>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-light btn-sm view-message d-flex align-items-center" 
                                                title="Visualizar mensagem"
                                                data-message="<?php echo htmlspecialchars($contact['message']); ?>">
                                            <span class="material-symbols-rounded fs-5">visibility</span>
                                        </button>
                                        <button onclick="toggleRead(<?php echo $contact['id']; ?>)" 
                                                class="btn btn-light btn-sm d-flex align-items-center"
                                                title="<?php echo $contact['is_read'] ? 'Marcar como não lido' : 'Marcar como lido'; ?>">
                                            <span class="material-symbols-rounded fs-5 <?php echo $contact['is_read'] ? 'text-primary' : 'text-warning'; ?>">
                                                <?php echo $contact['is_read'] ? 'mark_email_unread' : 'mark_email_read'; ?>
                                            </span>
                                        </button>
                                        <button onclick="deleteContact(<?php echo $contact['id']; ?>)" 
                                                class="btn btn-light btn-sm d-flex align-items-center"
                                                title="Excluir mensagem">
                                            <span class="material-symbols-rounded fs-5 text-danger">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mensagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="messageContent"></p>
            </div>
        </div>
    </div>
</div>

<script>
let messageModal;

document.addEventListener('DOMContentLoaded', function() {
    messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    
    // Adicionar listeners para os botões de visualizar mensagem
    document.querySelectorAll('.view-message').forEach(button => {
        button.addEventListener('click', function() {
            const message = this.getAttribute('data-message');
            document.getElementById('messageContent').textContent = message;
            messageModal.show();
        });
    });
});

function toggleRead(id) {
    fetch('api/contact.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle_read&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao atualizar status'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao atualizar status'
        });
    });
}

function deleteContact(id) {
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
            fetch('api/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'A mensagem foi excluída com sucesso.',
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire(
                        'Erro!',
                        data.message || 'Erro ao excluir mensagem',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'Erro!',
                    'Erro ao excluir mensagem',
                    'error'
                );
            });
        }
    });
}
</script>

<?php
// Renderizar o rodapé
renderFooter();
?> 