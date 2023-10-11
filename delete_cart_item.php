<?php
require 'config.php';
require 'vendor/autoload.php'; // Include MongoDB PHP driver

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $cartCollection = $mongoClient->medicine_db->cart;

    $cartId = $_POST['cart_id'];

    // Convert the cart ID to a MongoDB ObjectId
    try {
        $cartObjectId = new MongoDB\BSON\ObjectId($cartId);
    } catch (Exception $e) {
        echo json_encode(['success' => false]);
        exit;
    }

    // Delete the item from the cart collection
    $deleteResult = $cartCollection->deleteOne(['_id' => $cartObjectId]);

    if ($deleteResult->getDeletedCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
