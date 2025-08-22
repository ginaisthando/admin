<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';
require_once '../config/auth.php';

// Check authentication
require_authentication();

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // Get all interviews with department names and application status
    $query = "SELECT i.*, d.name as department_name, ia.application_status 
              FROM interviews i 
              JOIN departments d ON i.department_id = d.id 
              LEFT JOIN interview_applications ia ON i.id = ia.interview_id
              WHERE i.status = 'scheduled' 
              ORDER BY i.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $interviews = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'interviews' => $interviews
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
