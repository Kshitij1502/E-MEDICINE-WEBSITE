<?php
require 'vendor/autoload.php';

$mongoUrl = "mongodb://localhost:27017";
$dbName = "medicine_db";

try {
    $client = new MongoDB\Client($mongoUrl);
    $db = $client->selectDatabase($dbName);
    $usersCollection = $db->selectCollection("users");
    $cartCollection = $db->selectCollection("cart");
    $billCollection = $db->selectCollection("bill");
    // Add more collections and configurations as needed
} catch (Exception $e) {
    echo "MongoDB connection failed: " . $e->getMessage();
    exit;
}

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Retrieve the user's cart items from MongoDB
    try {
        $userCart = $cartCollection->findOne(['user_id' => new MongoDB\BSON\ObjectId($user_id)]);

        if (!$userCart) {
            // Handle the case where the user's cart is empty or doesn't exist
            echo "Error: Your cart is empty.";
            exit;
        }

        // Calculate the bill total
        $totalAmount = 0;
        if (!empty($userCart) && isset($userCart['items'])) {
            foreach ($userCart['items'] as $cartItem) {
                $itemPrice = $cartItem['price'];
                $itemQuantity = $cartItem['quantity'];
                $totalAmount += $itemPrice * $itemQuantity;
            }
        }

        // Create a new bill document
        $billData = [
            'user_id' => new MongoDB\BSON\ObjectId($user_id),
            'items' => $userCart['items'] ?? [],
            'total_amount' => $totalAmount,
            // Add other bill-related data here
        ];

        // Save the bill document to the "bill" collection
        $insertResult = $billCollection->insertOne($billData);

        if ($insertResult->getInsertedCount() === 1) {
            $billId = $insertResult->getInsertedId();
            echo "Bill generated and saved successfully! Bill ID: " . $billId;
        
            // Add JavaScript code to display an alert with the bill ID
            echo "<script>alert('Bill generated successfully! Bill ID: " . $billId . "');</script>";
        } else {
            echo "Error: Failed to generate and save the bill.";
        }

        // Clear the user's cart
        $cartCollection->deleteMany(['user_id' => new MongoDB\BSON\ObjectId($user_id)]);

    } catch (Exception $e) {
        echo "MongoDB query error: " . $e->getMessage();
    }
} else {
    // Redirect the user to the login page or display an error message
    header('Location: login.php');
    exit;
}
?>
