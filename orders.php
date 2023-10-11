<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';

// Replace 'medicine_db' with your actual MongoDB database name
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db');

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
}

$ordersCollection = $mongoDB->selectCollection('orders');
$cursor = $ordersCollection->find(['user_id' => $user_id]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Orders</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Your Orders</h3>
   <p><a href="home.php">Home</a> / Orders</p>
</div>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

      <?php
         // Use iterator_to_array to convert the cursor to an array
         $orders = iterator_to_array($cursor);

         if (!empty($orders)) {
            foreach ($orders as $document) {
      ?>
      <div class="box">
    <p>Placed on: <span><?php echo $document['placed_on']; ?></span></p>
    <p>Name: <span><?php echo $document['name']; ?></span></p>
    <p>Number: <span><?php echo $document['number']; ?></span></p>
    <p>Email: <span><?php echo $document['email']; ?></span></p>
    <p>Address: <span><?php echo $document['address']; ?></span></p>
    <p>Payment Method: <span><?php echo $document['method']; ?></span></p>
    <p>Your Orders: <span><?php echo $document['total_products']; ?></span></p>
    <p>Total Price: <span>$<?php echo $document['total_price']; ?>/-</span></p>
    <p>Payment Status: <span style="color:<?php echo ($document['method'] == 'pending') ? 'red' : 'green'; ?>"><?php echo $document['method']; ?></span></p>
</div>

      <?php
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Custom JavaScript file link -->
<script src="js/script.js"></script>

</body>
</html>
