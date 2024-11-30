<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = null;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Preencha todos os campos";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Usuário ou senha inválidos";
            }
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            $error = "Erro ao fazer login. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AdminhuDev</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Personalizado -->
    <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h4 class="logo-text mb-1">ADMINHU<span class="text-primary">DEV</span></h4>
                            <p class="text-secondary mb-0">Painel Administrativo</p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded">error</span>
                                <?php echo $error; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="post" action="login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuário</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-rounded">person</span>
                                    </span>
                                    <input type="text" class="form-control" 
                                           id="username" name="username" 
                                           value="<?php echo htmlspecialchars($username); ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-rounded">lock</span>
                                    </span>
                                    <input type="password" class="form-control" 
                                           id="password" name="password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-light d-flex align-items-center justify-content-center gap-2 w-100">
                                <span class="material-symbols-rounded text-primary">login</span>
                                Entrar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
