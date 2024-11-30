<?php
// Função para verificar se o usuário está logado
function requireLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

// Função para verificar se o usuário tem permissão
function checkPermission($permission) {
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }
    
    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            return false;
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao verificar permissão: " . $e->getMessage());
        return false;
    }
}

// Função para fazer logout
function logout() {
    session_start();
    session_destroy();
    header('Location: /admin/login.php');
    exit;
} 