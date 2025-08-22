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
    
    // Get all employees with department names
    $query = "SELECT e.*, d.name as department_name 
              FROM employees e 
              JOIN departments d ON e.department_id = d.id 
              WHERE e.employment_status = 'active' 
              ORDER BY e.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $employees = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'employees' => $employees
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
