<?php
require_once('../config/config.php');
require_once('../config/database.php');

// Retrieve the request body as a string
$request_body = file_get_contents('php://input');

// Decode the JSON data into a PHP array
$data = json_decode($request_body, true);

// Validate input fields
if (!isset($data['title']) || !isset($data['content']) || !isset($data['author'])) {
    http_response_code(400);
    die(json_encode(['message' => 'Error: Missing required parameter']));
}

// Sanitize input
$title = filter_var($data['title'], FILTER_SANITIZE_STRING);
$author = filter_var($data['author'], FILTER_SANITIZE_STRING);
$content = filter_var($data['content'], FILTER_SANITIZE_STRING);

// Prepare statement
$stmt = $conn->prepare('INSERT INTO blog_posts (title, content, author) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $data['title'], $data['content'], $data['author']);

// Execute statement
if ($stmt->execute()) {
    // Get the ID of the newly created post
    $id = $stmt->insert_id;

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return success response
    http_response_code(201);
    echo json_encode(['message' => 'Post created', 'id' => $id]);
} else {
    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return error response
    http_response_code(500);
    die(json_encode(['message' => 'Error creating post']));
}

