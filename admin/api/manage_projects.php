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
        case 'add':
            // Validar dados
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_STRING);
            $github_url = filter_input(INPUT_POST, 'github_url', FILTER_SANITIZE_URL);
            $live_url = filter_input(INPUT_POST, 'live_url', FILTER_SANITIZE_URL);
            $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            if (!$title || !$description || !$technologies) {
                throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
            }
            
            // Inserir projeto
            $stmt = $pdo->prepare("
                INSERT INTO projects (title, description, technologies, github_url, live_url, image_url, featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $title,
                $description,
                $technologies,
                $github_url,
                $live_url,
                $image_url,
                $featured
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projeto adicionado com sucesso!'
            ]);
            break;
            
        case 'edit':
            // Validar ID
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('ID do projeto inválido');
            }
            
            // Validar dados
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_STRING);
            $github_url = filter_input(INPUT_POST, 'github_url', FILTER_SANITIZE_URL);
            $live_url = filter_input(INPUT_POST, 'live_url', FILTER_SANITIZE_URL);
            $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            if (!$title || !$description || !$technologies) {
                throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
            }
            
            // Atualizar projeto
            $stmt = $pdo->prepare("
                UPDATE projects 
                SET title = ?, description = ?, technologies = ?, 
                    github_url = ?, live_url = ?, image_url = ?, featured = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $title,
                $description,
                $technologies,
                $github_url,
                $live_url,
                $image_url,
                $featured,
                $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projeto atualizado com sucesso!'
            ]);
            break;
            
        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID do projeto inválido');
            }
            
            // Excluir projeto
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Projeto não encontrado');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Projeto excluído com sucesso!'
            ]);
            break;
            
        case 'get':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID do projeto inválido');
            }
            
            // Buscar projeto
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch();
            
            if (!$project) {
                throw new Exception('Projeto não encontrado');
            }
            
            echo json_encode([
                'success' => true,
                'project' => $project
            ]);
            break;
            
        case 'list':
            // Listar todos os projetos
            $stmt = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, created_at DESC");
            $projects = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'projects' => $projects
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