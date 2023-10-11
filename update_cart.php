<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace 'your_db_name' with your actual MongoDB database name

// Check if the user is logged in
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   echo json_encode(['success' => false]);
   exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $cartItemId = $_POST["cartItemId"];
   $newQuantity = (int)$_POST["newQuantity"];

   // Select the cart collection
   $cartCollection = $mongoDB->selectCollection('cart');

   // Perform the update in the MongoDB collection
   $updateResult = $cartCollection->updateOne(
      ["_id" => new MongoDB\BSON\ObjectID($cartItemId)],
      ['$set' => ["quantity" => $newQuantity]]
   );

   if ($updateResult->getModifiedCount() > 0) {
      echo json_encode(["success" => true]);
   } else {
      echo json_encode(["success" => false]);
   }
} else {
   // If the request is not a POST request, return an error
   echo json_encode(['success' => false]);
}
?>
