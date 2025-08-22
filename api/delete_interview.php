<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/auth.php';

// Check authentication
require_authentication();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['interview_id'])) {
        throw new Exception('Interview ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if interview exists
    $check_query = "SELECT i.id, i.position, d.name as department_name 
                    FROM interviews i 
                    JOIN departments d ON i.department_id = d.id 
                    WHERE i.id = :interview_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':interview_id', $input['interview_id']);
    $check_stmt->execute();
    
    $interview = $check_stmt->fetch();
    
    if (!$interview) {
        throw new Exception('Interview not found');
    }
    
    // Delete interview applications first (due to foreign key constraint)
    $delete_apps_query = "DELETE FROM interview_applications WHERE interview_id = :interview_id";
    $delete_apps_stmt = $db->prepare($delete_apps_query);
    $delete_apps_stmt->bindParam(':interview_id', $input['interview_id']);
    $delete_apps_stmt->execute();
    
    // Delete interview
    $query = "DELETE FROM interviews WHERE id = :interview_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':interview_id', $input['interview_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Interview deleted successfully',
            'interview' => $interview
        ]);
    } else {
        throw new Exception('Failed to delete interview');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>