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
    $required_fields = ['first_name', 'last_name', 'position', 'department', 'email', 'salary'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // First, get or create department
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
    
    // Generate employee number
    $emp_num_query = "SELECT COUNT(*) as count FROM employees";
    $emp_num_stmt = $db->prepare($emp_num_query);
    $emp_num_stmt->execute();
    $count = $emp_num_stmt->fetch()['count'];
    $employee_number = 'EMP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    
    // Insert employee
    $query = "INSERT INTO employees (employee_number, first_name, last_name, email, position, department_id, salary, hire_date, profile_image) 
              VALUES (:employee_number, :first_name, :last_name, :email, :position, :department_id, :salary, CURDATE(), 'default.png')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':employee_number', $employee_number);
    $stmt->bindValue(':first_name', $input['first_name']);
    $stmt->bindValue(':last_name', $input['last_name']);
    $stmt->bindValue(':email', $input['email']);
    $stmt->bindValue(':position', $input['position']);
    $stmt->bindParam(':department_id', $department_id);
    $stmt->bindValue(':salary', $input['salary']);
    
    if ($stmt->execute()) {
        $employee_id = $db->lastInsertId();
        
        // Return the new employee data
        $response = [
            'success' => true,
            'message' => 'Employee added successfully',
            'employee' => [
                'id' => $employee_id,
                'employee_number' => $employee_number,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'position' => $input['position'],
                'department' => $input['department'],
                'salary' => $input['salary'],
                'profile_image' => 'default.png'
            ]
        ];
        
        echo json_encode($response);
    } else {
        throw new Exception('Failed to add employee');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
