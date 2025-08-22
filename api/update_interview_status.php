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
    if (empty($input['interview_id']) || empty($input['status'])) {
        throw new Exception('Interview ID and status are required');
    }
    
    if (!in_array($input['status'], ['accepted', 'rejected'])) {
        throw new Exception('Status must be either "accepted" or "rejected"');
    }
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // Update interview application status
    $query = "UPDATE interview_applications 
              SET application_status = :status, 
                  decision_date = CURRENT_TIMESTAMP,
                  decision_notes = :notes
              WHERE interview_id = :interview_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':status', $input['status']);
    $stmt->bindValue(':interview_id', $input['interview_id']);
    $stmt->bindValue(':notes', $input['notes'] ?? '');
    
    if ($stmt->execute()) {
        // If accepted, you might want to update the interview status too
        if ($input['status'] === 'accepted') {
            $update_interview = "UPDATE interviews SET status = 'completed' WHERE id = :interview_id";
            $update_stmt = $db->prepare($update_interview);
            $update_stmt->bindValue(':interview_id', $input['interview_id']);
            $update_stmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Interview status updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update interview status');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
