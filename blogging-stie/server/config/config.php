<?php
// Define configuration options
$allowedOrigins = ['http://localhost:3000'];
$allowedHeaders = ['Content-Type'];

// Set headers for CORS
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && in_array($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'], $allowedMethods)) {
    header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
}
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']) && in_array($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'], $allowedHeaders)) {
    header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
}
