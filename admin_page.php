<?php
session_start();


require 'vendor/autoload.php'; // Include MongoDB library

$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->selectDatabase('medicine_db');
$ordersCollection = $database->selectCollection('orders');
$productsCollection = $database->selectCollection('products');
$usersCollection = $database->selectCollection('users');
$messagesCollection = $database->selectCollection('message');

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit; // End script execution
}

$userId = $_SESSION['user_id'];
$user = $usersCollection->findOne(['_id' => $userId]);

if (!$user || $user['user_type'] !== 'admin') {
    header('location:home.php'); // Redirect to login page for non-admin users
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">


   <!-- Your existing CSS styles -->
   <link rel="stylesheet" href="css/style.css">
   

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<div class="home-bg">

   <section class="home">

      <div class="content">
         <span>Admin Dashboard</span>
         <h3>Welcome to the Admin Dashboard</h3>
         <p>you can manage various aspects of your website, including products, users, messages, and bills.</p>
      </div>

   </section>

</div>

<section class="dashboard">

   <h1 class="title">Admin Actions</h1>

   <div class="box-container">
 <div class="box">

        
         <h3>Manage Products</h3>
         <p>Add, edit, and delete products in the store.</p>
         <a href="admin_products.php" class="btn">Manage Products</a>
      </div>

      <div class="box">
         
         <h3>Manage Users</h3>
         <p>View and delete Users data.</p>
         <a href="admin_users.php" class="btn">Manage Users</a>
      </div>     

      <div class="box">
        
         <h3>Manage Messages</h3>
         <p>View and delete Users Messages.</p>
         <a href="admin_contacts.php" class="btn">Manage Messages</a>
      </div>

      <div class="box">
        
         <h3>Manage Bills</h3>
         <p>View and delete Users Bills.</p>
         <a href="admin_bills.php" class="btn">Manage Bills</a>
      </div>

      <!-- Add more admin actions here... -->

   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>
