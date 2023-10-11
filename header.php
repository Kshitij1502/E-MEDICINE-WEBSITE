<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Add your HTML head content here -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Website Title</title>
   <!-- Add your CSS and other head content here -->
</head>
<body>
<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace 'your_db_name' with your actual MongoDB database name

//session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

// Define the $usersCollection variable to access the "users" collection
$usersCollection = $mongoDB->selectCollection('users');
?>

<header class="header">
   <div class="header-2">
      <div class="flex">
         <a href="home.php" class="logo">E-MEDICINE</a>

         <nav class="navbar">
            <a href="home.php">HOME</a>
            <a href="shop.php">SHOP</a>
            <a href="contact.php">CONTACT</a>
            <a href="about.php">ABOUT</a>
            <!-- Add a link to view user's bills -->
            <a href="user_bills.php">VIEW BILLS</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>

            <div id="user-btn" class="fas fa-user"></div>
            <?php
               // Replace with your actual cart count logic
               $cart_count = 0;
            ?>
            <a href="cart.php"> <i class="fas fa-shopping-cart"></i> </a>
         </div>

         <div class="user-box">
            <?php
               // Get user profile information from MongoDB
               $userProfile = $usersCollection->findOne(['_id' => $user_id]);
               ?>
               <p><?= $userProfile['name']; ?></p>
               <a href="logout.php" class="delete-btn">Logout</a>
         </div>
      </div>
   </div>
</header>
<!-- Add the rest of your HTML content here -->
<!-- Add your JavaScript and other footer content here -->
</body>
</html>
