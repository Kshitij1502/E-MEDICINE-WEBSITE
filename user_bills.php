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
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Bills</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom user css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- CSS for table -->
   <style>
      table {
         border-collapse: collapse;
         width: 100%;
         margin-top: 20px;
      }

      th, td {
         padding: 22px 50px;
         text-align: left;
         border-bottom: 1px solid #ddd;
         font-size: 1.5rem;
      }

      th {
         background-color: #f2f2f2;
      }

      tr:hover {
         background-color: #f5f5f5;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="user-bills">
   <h1 class="title">Your Bills</h1>
   <table>
      <thead>
         <tr>
            <th>Bill ID</th>
            <th>Total</th>
            <th>Timestamp</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $billsCollection = $mongoDB->selectCollection('bills');
            
            // Retrieve bills for the currently logged-in user
            $cursor = $billsCollection->find(['user_id' => $user_id]);
            
            foreach ($cursor as $bill) {
               $timestamp = $bill['timestamp']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'));
         ?>
         <tr>
            <td><?php echo $bill['_id']; ?></td>
            <td><?php echo '₹' . $bill['total']; ?></td>
            <td><?php echo $timestamp->format('Y-m-d H:i:s'); ?></td>
            <td>
               <!-- Add a link to view the full bill details if needed -->
               <a href="view_bill.php?bill_id=<?php echo $bill['_id']; ?>" class="view-btn">View Bill</a>
            </td>
         </tr>
         <?php
            }
         ?>
      </tbody>
   </table>
</section>

<!-- custom user js file link  -->
<script src="js/script.js"></script>

</body>
</html>
