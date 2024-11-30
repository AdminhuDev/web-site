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

// Função para mapear nomes de habilidades para ícones
function getSkillIcon($skillName) {
    $skillName = strtolower($skillName);
    
    // Mapeamento de habilidades para ícones
    $iconMap = [
        // Linguagens de Programação
        'php' => 'php',
        'javascript' => 'javascript',
        'typescript' => 'code',
        'python' => 'code',
        'java' => 'code',
        'c#' => 'code',
        'ruby' => 'code',
        'go' => 'code',
        'rust' => 'code',
        
        // Frontend
        'html' => 'html',
        'css' => 'css',
        'react' => 'code',
        'vue' => 'code',
        'angular' => 'code',
        'jquery' => 'code',
        'bootstrap' => 'palette',
        'tailwind' => 'palette',
        'sass' => 'palette',
        'less' => 'palette',
        
        // Backend
        'node' => 'nodejs',
        'express' => 'nodejs',
        'laravel' => 'php',
        'symfony' => 'php',
        'django' => 'code',
        'flask' => 'code',
        'spring' => 'code',
        'asp.net' => 'code',
        
        // Banco de Dados
        'mysql' => 'database',
        'postgresql' => 'database',
        'mongodb' => 'database',
        'redis' => 'database',
        'sql' => 'database',
        'oracle' => 'database',
        'sqlite' => 'database',
        
        // DevOps
        'docker' => 'container',
        'kubernetes' => 'container',
        'aws' => 'cloud',
        'azure' => 'cloud',
        'google cloud' => 'cloud',
        'git' => 'merge',
        'github' => 'merge',
        'gitlab' => 'merge',
        'jenkins' => 'build',
        'linux' => 'terminal',
        'nginx' => 'dns',
        'apache' => 'dns',
        
        // Mobile
        'android' => 'android',
        'ios' => 'phone_iphone',
        'react native' => 'phone_android',
        'flutter' => 'phone_android',
        'swift' => 'phone_iphone',
        'kotlin' => 'phone_android',
        
        // Testes
        'jest' => 'bug_report',
        'cypress' => 'bug_report',
        'selenium' => 'bug_report',
        'phpunit' => 'bug_report',
        'junit' => 'bug_report',
        
        // Ferramentas
        'vscode' => 'code',
        'photoshop' => 'image',
        'illustrator' => 'brush',
        'figma' => 'design_services',
        'xd' => 'design_services',
        'sketch' => 'design_services',
        
        // Metodologias
        'scrum' => 'group',
        'kanban' => 'view_kanban',
        'agile' => 'group',
        'jira' => 'task',
        'trello' => 'view_kanban',
        
        // APIs e Protocolos
        'rest' => 'api',
        'graphql' => 'api',
        'soap' => 'api',
        'http' => 'api',
        'websocket' => 'api',
        
        // Segurança
        'oauth' => 'security',
        'jwt' => 'security',
        'ssl' => 'security',
        'encryption' => 'security',
        
        // Outros
        'wordpress' => 'web',
        'seo' => 'search',
        'analytics' => 'analytics',
        'marketing' => 'campaign',
        'ui' => 'design_services',
        'ux' => 'psychology_alt',
        'responsive' => 'devices',
        'web design' => 'design_services',
        'web development' => 'web',
        'mobile development' => 'phone_android',
        'database' => 'database',
        'api' => 'api',
        'cloud' => 'cloud',
        'security' => 'security',
        'testing' => 'bug_report',
        'version control' => 'merge',
        'deployment' => 'rocket_launch',
        'ci/cd' => 'build',
        'monitoring' => 'monitoring',
        'performance' => 'speed',
        'optimization' => 'tune',
        'debugging' => 'bug_report',
        'documentation' => 'description',
        'problem solving' => 'psychology',
        'team work' => 'group',
        'communication' => 'chat',
        'leadership' => 'person',
        'project management' => 'task',
        'agile methodologies' => 'group',
        'devops' => 'build',
        'frontend' => 'web',
        'backend' => 'terminal',
        'fullstack' => 'code'
    ];
    
    // Procurar correspondência exata
    if (isset($iconMap[$skillName])) {
        return $iconMap[$skillName];
    }
    
    // Procurar correspondência parcial
    foreach ($iconMap as $key => $icon) {
        if (strpos($skillName, $key) !== false) {
            return $icon;
        }
    }
    
    // Ícone padrão se nenhuma correspondência for encontrada
    return 'code';
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'add':
            // Validar dados
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
            $proficiency = filter_input(INPUT_POST, 'proficiency', FILTER_VALIDATE_INT);
            
            if (!$name || !$category || $proficiency === false) {
                throw new Exception('Todos os campos são obrigatórios');
            }
            
            if ($proficiency < 0 || $proficiency > 100) {
                throw new Exception('Proficiência deve estar entre 0 e 100');
            }
            
            // Gerar ícone automaticamente
            $icon = getSkillIcon($name);
            
            // Inserir habilidade
            $stmt = $pdo->prepare("
                INSERT INTO skills (name, category, proficiency, icon) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $name,
                $category,
                $proficiency,
                $icon
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Habilidade adicionada com sucesso!'
            ]);
            break;
            
        case 'edit':
            // Validar ID
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('ID da habilidade inválido');
            }
            
            // Validar dados
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
            $proficiency = filter_input(INPUT_POST, 'proficiency', FILTER_VALIDATE_INT);
            
            if (!$name || !$category || $proficiency === false) {
                throw new Exception('Todos os campos são obrigatórios');
            }
            
            if ($proficiency < 0 || $proficiency > 100) {
                throw new Exception('Proficiência deve estar entre 0 e 100');
            }
            
            // Gerar ícone automaticamente
            $icon = getSkillIcon($name);
            
            // Atualizar habilidade
            $stmt = $pdo->prepare("
                UPDATE skills 
                SET name = ?, category = ?, proficiency = ?, icon = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $name,
                $category,
                $proficiency,
                $icon,
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Habilidade não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Habilidade atualizada com sucesso!'
            ]);
            break;
            
        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID da habilidade inválido');
            }
            
            // Excluir habilidade
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Habilidade não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Habilidade excluída com sucesso!'
            ]);
            break;
            
        case 'get':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('ID da habilidade inválido');
            }
            
            // Buscar habilidade
            $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            $skill = $stmt->fetch();
            
            if (!$skill) {
                throw new Exception('Habilidade não encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'skill' => $skill
            ]);
            break;
            
        case 'list':
            // Listar todas as habilidades
            $stmt = $pdo->query("SELECT * FROM skills ORDER BY category, name");
            $skills = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'skills' => $skills
            ]);
            break;
            
        case 'list_categories':
            // Listar categorias únicas
            $stmt = $pdo->query("SELECT DISTINCT category FROM skills ORDER BY category");
            $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode([
                'success' => true,
                'categories' => $categories
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