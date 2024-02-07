<?php
// Load configuration files
require_once('../config/config.php');
require_once('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the request URI
    $request_uri = $_SERVER['REQUEST_URI'];

    // Parse the URI to get the post ID
    $parts = explode('/', $request_uri);
    $id = end($parts);

    // Query to get the post by ID along with its like and dislike counts
    $query = "SELECT bp.*, 
                     (SELECT COUNT(*) FROM post_votes WHERE post_id = bp.id AND vote_type = 'like') AS num_likes,
                     (SELECT COUNT(*) FROM post_votes WHERE post_id = bp.id AND vote_type = 'dislike') AS num_dislikes
              FROM blog_posts AS bp WHERE bp.id = ?";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the post with the given ID exists
    if ($result->num_rows === 1) {
        $post = $result->fetch_assoc();

        // Construct the response data
        $response = [
            'status' => 'success',
            'data' => [
                'id' => $post['id'],
                'title' => $post['title'],
                'content' => $post['content'],
                'author' => $post['author'],
                'date' => date("l jS \of F Y", strtotime($post['publish_date'])),
                'likes' => $post['num_likes'],
                'dislikes' => $post['num_dislikes']
            ]
        ];

        // Set the response header as JSON
        header('Content-Type: application/json');

        // Return the response JSON
        echo json_encode($response);
    } else {
        // Post with the given ID not found
        $response = [
            'status' => 'error',
            'message' => 'Post not found'
        ];

        // Set the response header as JSON
        header('Content-Type: application/json');

        // Return the response JSON
        echo json_encode($response);
    }

    $stmt->close();
    $conn->close();
}

// Check if the user has already liked the post
function check_like($conn, $post_id, $ip_address) {
    $query = "SELECT * FROM post_votes WHERE post_id=? AND user_ip=? AND vote_type='like'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

// Insert like into database
function insert_like($conn, $post_id, $ip_address) {
    if (!check_like($conn, $post_id, $ip_address)) {
        $query = "INSERT INTO post_votes (post_id, user_ip, vote_type) VALUES (?, ?, 'like')";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
    return false; // Already liked
}

// Remove like from database
function remove_like($conn, $post_id, $ip_address) {
    if (check_like($conn, $post_id, $ip_address)) {
        $query = "DELETE FROM post_votes WHERE post_id=? AND user_ip=? AND vote_type='like'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
    return false; // No like found to remove
}
// Check if the user has already liked the post
function check_dislike($conn, $post_id, $ip_address) {
    $query = "SELECT * FROM post_votes WHERE post_id=? AND user_ip=? AND vote_type='dislike'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

// Insert dislike into database
function insert_dislike($conn, $post_id, $ip_address) {
    if (!check_dislike($conn, $post_id, $ip_address)) {
        $query = "INSERT INTO post_votes (post_id, user_ip, vote_type) VALUES (?, ?, 'dislike')";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
    return false; // Already disliked
}

// Remove dislike from database
function remove_dislike($conn, $post_id, $ip_address) {
    if (check_dislike($conn, $post_id, $ip_address)) {
        $query = "DELETE FROM post_votes WHERE post_id=? AND user_ip=? AND vote_type='dislike'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $post_id, $ip_address);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
    return false; // No dislike found to remove
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the request URI
    $request_uri = $_SERVER['REQUEST_URI'];

    // Split the URI into segments
    $segments = explode('/', $request_uri);

    // Assuming the segment structure is "/api/post/{id}/{action}/{ip_address}"
    // Adjust the indices according to your actual URI structure
    $post_id = $segments[6];
    $action = $segments[7];
    $ip_address = $segments[8];

    if ($action == 'like') {
        if (check_like($conn, $post_id, $ip_address)) {
            // If the user has already liked the post, remove the like
            if (remove_like($conn, $post_id, $ip_address)) {
                http_response_code(200);
                echo json_encode(['message' => 'Like removed successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to remove like.']);
            }
        } else {
            // If the user hasn't liked the post yet, add a new like
            if (insert_like($conn, $post_id, $ip_address)) {
                http_response_code(201);
                echo json_encode(['message' => 'Like added successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to add like.']);
            }
        }
    } elseif ($action == 'dislike') {
        if (check_dislike($conn, $post_id, $ip_address)) {
            // If the user has already disliked the post, remove the dislike
            if (remove_dislike($conn, $post_id, $ip_address)) {
                http_response_code(200);
                echo json_encode(['message' => 'Dislike removed successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to remove dislike.']);
            }
        } else {
            // If the user hasn't disliked the post yet, add a new dislike
            if (insert_dislike($conn, $post_id, $ip_address)) {
                http_response_code(201);
                echo json_encode(['message' => 'Dislike added successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to add dislike.']);
            }
        }
    }
}