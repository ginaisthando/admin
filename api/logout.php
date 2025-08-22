<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/auth.php';

logout_user();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);
?>