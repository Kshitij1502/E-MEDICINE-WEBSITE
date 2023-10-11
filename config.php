<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$mongoUrl = "mongodb://localhost:27017";
$dbName = "medicine_db";

try {
    $client = new Client($mongoUrl);
    $db = $client->medicine_db; // Assuming 'medicine_db' is your MongoDB database name
    $usersCollection = $db->users; // Assuming 'users' is your MongoDB collection name
    $cartCollection = $db->cart; // Define the cart collection
    $billsCollection = $db->bills; // Define the bills collection
    // echo "Connected to MongoDB successfully!";
} catch (Exception $e) {
    echo "MongoDB connection failed: " . $e->getMessage();
}
?>
