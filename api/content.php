<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$section = $_GET['section'] ?? '';

if (empty($section)) {
    // Return all content
    try {
        $query = "SELECT section_name, content FROM content_sections";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = [];
        foreach ($results as $row) {
            $content[$row['section_name']] = json_decode($row['content'], true);
        }
        
        echo json_encode($content);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    // Return specific section content
    try {
        $query = "SELECT content FROM content_sections WHERE section_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$section]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo $result['content'];
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Section not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}
?>