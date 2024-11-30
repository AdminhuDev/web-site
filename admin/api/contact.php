<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Função para salvar uma nova mensagem
function saveMessage($name, $email, $message) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $message]);
        return ['success' => true];
    } catch (PDOException $e) {
        error_log("Erro ao salvar mensagem: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao salvar mensagem'];
    }
}

// Função para buscar todos os contatos
function getContacts() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
        $contacts = $stmt->fetchAll();
        return ['success' => true, 'contacts' => $contacts];
    } catch (PDOException $e) {
        error_log("Erro ao buscar contatos: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao buscar contatos'];
    }
}

// Função para buscar um contato específico
function getContact($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $contact = $stmt->fetch();
        
        if ($contact) {
            return ['success' => true, 'contact' => $contact];
        } else {
            return ['success' => false, 'message' => 'Contato não encontrado'];
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar contato: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao buscar contato'];
    }
}

// Função para marcar contato como lido/não lido
function toggleRead($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE contacts SET is_read = NOT is_read WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        error_log("Erro ao atualizar status do contato: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao atualizar status do contato'];
    }
}

// Função para excluir contato
function deleteContact($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        error_log("Erro ao excluir contato: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao excluir contato'];
    }
}

// Processar a requisição
$action = $_REQUEST['action'] ?? '';
$id = $_REQUEST['id'] ?? null;

// Se for um POST sem action, assume que é uma nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($action)) {
    // Validar campos obrigatórios
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit;
    }
    
    // Validar email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'E-mail inválido']);
        exit;
    }
    
    echo json_encode(saveMessage(
        $_POST['name'],
        $_POST['email'],
        $_POST['message']
    ));
    exit;
}

// Para outras ações, requer autenticação
if ($action !== '') {
    requireLogin();
}

switch ($action) {
    case 'get':
        if ($id) {
            echo json_encode(getContact($id));
        } else {
            echo json_encode(getContacts());
        }
        break;
        
    case 'toggle_read':
        if ($id) {
            echo json_encode(toggleRead($id));
        } else {
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
        }
        break;
        
    case 'delete':
        if ($id) {
            echo json_encode(deleteContact($id));
        } else {
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
        }
        break;
        
    default:
        if (empty($action)) {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        }
} 