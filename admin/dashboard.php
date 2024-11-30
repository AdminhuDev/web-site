<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
requireLogin();

// Buscar estatísticas
try {
    // Total de projetos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
    $projectsCount = $stmt->fetch()['total'];
    
    // Total de habilidades
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM skills");
    $skillsCount = $stmt->fetch()['total'];
    
    // Total de mensagens não lidas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contacts WHERE is_read = 0");
    $unreadMessages = $stmt->fetch()['total'];
    
    // Projetos recentes
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
    $recentProjects = $stmt->fetchAll();
    
    // Mensagens recentes
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
    $recentMessages = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
}

// Renderizar o cabeçalho
require_once 'includes/layout.php';
renderHeader('Dashboard - AdminhuDev');
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- Projetos -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon">
                        <span class="material-symbols-rounded">code</span>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-subtitle text-secondary mb-1">Projetos</h6>
                        <h2 class="card-title mb-0"><?php echo $projectsCount; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Habilidades -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon">
                        <span class="material-symbols-rounded">psychology</span>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-subtitle text-secondary mb-1">Habilidades</h6>
                        <h2 class="card-title mb-0"><?php echo $skillsCount; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mensagens -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon">
                        <span class="material-symbols-rounded">mail</span>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-subtitle text-secondary mb-1">Mensagens não lidas</h6>
                        <h2 class="card-title mb-0"><?php echo $unreadMessages; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row g-4">
    <!-- Recent Projects -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Projetos Recentes</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($recentProjects as $project): ?>
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="project-icon">
                            <span class="material-symbols-rounded">code</span>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1"><?php echo htmlspecialchars($project['title']); ?></h6>
                            <small class="text-secondary">
                                <?php echo date('d/m/Y', strtotime($project['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Messages -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Mensagens Recentes</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($recentMessages as $message): ?>
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="message-icon">
                            <span class="material-symbols-rounded">
                                <?php echo $message['is_read'] ? 'mark_email_read' : 'mark_email_unread'; ?>
                            </span>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1"><?php echo htmlspecialchars($message['name']); ?></h6>
                            <small class="text-secondary">
                                <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Renderizar o rodapé
renderFooter();
?> 