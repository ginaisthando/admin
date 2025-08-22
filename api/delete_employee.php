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
    
    if (empty($input['employee_id'])) {
        throw new Exception('Employee ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if employee exists
    $check_query = "SELECT id, first_name, last_name FROM employees WHERE id = :employee_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':employee_id', $input['employee_id']);
    $check_stmt->execute();
    
    $employee = $check_stmt->fetch();
    
    if (!$employee) {
        throw new Exception('Employee not found');
    }
    
    // Soft delete - update employment status instead of actual deletion
    $query = "UPDATE employees SET employment_status = 'terminated', updated_at = CURRENT_TIMESTAMP WHERE id = :employee_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':employee_id', $input['employee_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee deleted successfully',
            'employee' => $employee
        ]);
    } else {
        throw new Exception('Failed to delete employee');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>