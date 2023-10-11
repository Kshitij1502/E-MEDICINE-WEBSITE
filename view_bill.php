<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace 'your_db_name' with your actual MongoDB database name

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

if (isset($_GET['bill_id'])) {
   $bill_id = $_GET['bill_id'];
   $billsCollection = $mongoDB->selectCollection('bills');
   $bill = $billsCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($bill_id)]);

   if (!$bill) {
      // Bill not found, handle accordingly (e.g., show an error message)
      header('location:user_bills.php');
      exit;
   }
} else {
   // Bill ID not provided, handle accordingly (e.g., show an error message)
   header('location:user_bills.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Bill</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom user css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- CSS for bill details -->
   <style>
      .bill-details {
         margin-top: 20px;
         padding: 20px;
         border: 1px solid #ccc;
         border-radius: 5px;
      }

      .bill-header {
         font-size: 24px;
         margin-bottom: 10px;
      }

      .bill-info {
         margin-top: 10px;
      }

      .bill-info span {
         font-weight: bold;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="view-bill">
   <div class="bill-details">
      <h1 class="bill-header">Bill Details</h1>
      <div class="bill-info">
         <p><span>Bill ID:</span> <?php echo $bill['_id']; ?></p>
         <p><span>Total Amount:</span> ₹<?php echo $bill['total']; ?></p>
         <p><span>Timestamp:</span> <?php echo $bill['timestamp']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('Y-m-d H:i:s'); ?></p>
         <!-- Add more bill details here as needed -->
      </div>
      <!-- Add a table or list to display the products/items in the bill -->
      <table>
         <thead>
            <tr>
               <th>Product Name</th>
               <th>Quantity</th>
               <th>Price</th>
               <!-- Add more columns if needed (e.g., Subtotal) -->
            </tr>
         </thead>
         <tbody>
            <?php
               // Loop through the cart items in the bill and display them
               foreach ($bill['cart_items'] as $cartItem) {
            ?>
            <tr>
               <td><?php echo $cartItem['name']; ?></td>
               <td><?php echo $cartItem['quantity']; ?></td>
               <td>₹<?php echo $cartItem['price']; ?></td>
               <!-- Add more columns and calculations if needed -->
            </tr>
            <?php
               }
            ?>
         </tbody>
      </table>
   </div>
</section>

<!-- custom user js file link  -->
<script src="js/script.js"></script>

</body>
</html>
