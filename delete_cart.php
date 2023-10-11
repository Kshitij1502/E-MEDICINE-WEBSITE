<?php
session_start();

include 'config.php';

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $cartItemId = $_POST["cartItemId"];

   try {
      // Perform the delete operation in the MongoDB collection
      $deleteResult = $cartCollection->deleteOne(
         ["_id" => new MongoDB\BSON\ObjectID($cartItemId)]
      );

      if ($deleteResult->getDeletedCount() > 0) {
         echo json_encode(["success" => true]);
      } else {
         echo json_encode(["success" => false]);
      }
   } catch (Exception $e) {
      // Handle any exceptions that might occur during the delete operation
      echo json_encode(["success" => false, "error" => $e->getMessage()]);
   }
} else {
   echo json_encode(["success" => false]);
}
?>
