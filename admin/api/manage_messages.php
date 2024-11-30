<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'send':
            // Validar dados
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            
            if (!$name || !$email || !$message) {
                throw new Exception('Todos os campos são obrigatórios');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('E-mail inválido');
            }
            
            // Inserir mensagem
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso!'
            ]);
            break;
            
        case 'mark_read':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID da mensagem inválido');
            }
            
            // Marcar mensagem como lida
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = TRUE WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Mensagem não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Mensagem marcada como lida'
            ]);
            break;
            
        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID da mensagem inválido');
            }
            
            // Excluir mensagem
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Mensagem não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Mensagem excluída com sucesso'
            ]);
            break;
            
        case 'get':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID da mensagem inválido');
            }
            
            // Buscar mensagem
            $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = $stmt->fetch();
            
            if (!$message) {
                throw new Exception('Mensagem não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            break;
            
        case 'list':
            // Listar todas as mensagens
            $stmt = $pdo->query("
                SELECT * FROM contact_messages 
                ORDER BY is_read ASC, created_at DESC
            ");
            $messages = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
            break;
            
        case 'count_unread':
            // Contar mensagens não lidas
            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE");
            $count = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            break;
            
        default:
            throw new Exception('Ação inválida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 