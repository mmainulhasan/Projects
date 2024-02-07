<?php
// Load configuration files
require_once('../config/config.php');
require_once('../config/database.php');

// Define configuration options
$allowedMethods = ['GET', 'POST'];
$maxPosts = 100;

// Query to get all blog posts
$query = "SELECT * FROM blog_posts LIMIT $maxPosts";
$result = mysqli_query($conn, $query);

// Check if query is successful
if (!$result) {
    die('Error querying database: ' . mysqli_error($conn));
}

// Convert query result into an associative array
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($posts);

// Close database connection
mysqli_close($conn);