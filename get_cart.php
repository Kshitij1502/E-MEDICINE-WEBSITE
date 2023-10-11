<?php
session_start();
require_once('config.php');
// Connect to MongoDB
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db');
$cartCollection = $mongoDB->selectCollection('cart');

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    http_response_code(401); // Unauthorized
    exit;
}

// Fetch cart data for the current user
$cursor = $cartCollection->find(['user_id' => $user_id]);
$cartItems = iterator_to_array($cursor);

header('Content-Type: application/json');
echo json_encode($cartItems);
?>
