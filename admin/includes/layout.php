<?php
function renderHeader($title) {
    global $pdo;
    
    // Contar mensagens não lidas
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as unread FROM contacts WHERE is_read = 0");
        $unreadCount = $stmt->fetch()['unread'];
    } catch (PDOException $e) {
        error_log("Erro ao contar mensagens não lidas: " . $e->getMessage());
        $unreadCount = 0;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 Dark Theme -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    
    <!-- CSS Personalizado -->
    <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">ADMINHU<span class="navbar-brand">DEV</span></h4>
        </div>
        
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="/admin/dashboard.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/admin/projects.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded">code</span>
                    <span>Projetos</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/admin/skills.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'skills.php' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded">psychology</span>
                    <span>Habilidades</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/admin/contacts.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded">mail</span>
                    <span>Contatos</span>
                    <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-danger ms-auto"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="sidebar-item mt-auto">
                <a href="/admin/logout.php" class="sidebar-link text-danger">
                    <span class="material-symbols-rounded">logout</span>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main">
        <div class="container-fluid py-4">
<?php
}

function renderFooter() {
?>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</body>
</html>
<?php
} 