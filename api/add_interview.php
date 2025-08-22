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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['department']) || empty($input['position'])) {
        throw new Exception('Department and position are required');
    }
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // Get or create department
    $dept_query = "SELECT id FROM departments WHERE name = :department_name";
    $dept_stmt = $db->prepare($dept_query);
    $dept_stmt->bindValue(':department_name', $input['department']);
    $dept_stmt->execute();
    
    $department = $dept_stmt->fetch();
    
    if (!$department) {
        // Create new department if it doesn't exist
        $create_dept_query = "INSERT INTO departments (name, is_active) VALUES (:department_name, 1)";
        $create_dept_stmt = $db->prepare($create_dept_query);
        $create_dept_stmt->bindValue(':department_name', $input['department']);
        $create_dept_stmt->execute();
        $department_id = $db->lastInsertId();
    } else {
        $department_id = $department['id'];
    }
    
    // Insert interview
    $query = "INSERT INTO interviews (department_id, position, status) VALUES (:department_id, :position, 'scheduled')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':department_id', $department_id);
    $stmt->bindValue(':position', $input['position']);
    
    if ($stmt->execute()) {
        $interview_id = $db->lastInsertId();
        
        // Create corresponding interview application
        $app_query = "INSERT INTO interview_applications (interview_id, application_status) VALUES (:interview_id, 'pending')";
        $app_stmt = $db->prepare($app_query);
        $app_stmt->bindParam(':interview_id', $interview_id);
        $app_stmt->execute();
        
        // Return the new interview data
        $response = [
            'success' => true,
            'message' => 'Interview added successfully',
            'interview' => [
                'id' => $interview_id,
                'department' => $input['department'],
                'position' => $input['position'],
                'status' => 'scheduled'
            ]
        ];
        
        echo json_encode($response);
    } else {
        throw new Exception('Failed to add interview');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
