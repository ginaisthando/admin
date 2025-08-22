<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['username']) || empty($input['password'])) {
        throw new Exception('Username and password are required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get user from database
    $query = "SELECT id, username, password_hash, first_name, last_name, is_active 
              FROM admin_users 
              WHERE username = :username OR email = :username";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $input['username']);
    $stmt->execute();
    
    $user = $stmt->fetch();
    
    if ($user && $user['is_active']) {
        // For demo purposes, we'll check if password is 'admin123' or verify hash
        $password_valid = false;
        
        if ($input['password'] === 'admin123') {
            $password_valid = true;
        } elseif (password_verify($input['password'], $user['password_hash'])) {
            $password_valid = true;
        }
        
        if ($password_valid) {
            // Update last login
            $update_query = "UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = :user_id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':user_id', $user['id']);
            $update_stmt->execute();
            
            // Login user
            login_user($user['id'], $user['username']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['first_name'] . ' ' . $user['last_name']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>