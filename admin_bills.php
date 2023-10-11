<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace 'your_db_name' with your actual MongoDB database name

session_start();

$admin_id = $_SESSION['user_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $billsCollection = $mongoDB->selectCollection('bills');
   $result = $billsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($delete_id)]);

   if ($result->getDeletedCount() > 0) {
       header('location:admin_bills.php');
       exit;
   } else {
       echo "Failed to delete bill.";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Bills</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

   <!-- CSS for table -->
   <style>
      table {
         border-collapse: collapse;
         width: 100%;
         margin-top: 20px;
      }

      th, td {
         padding: 8px 12px;
         text-align: left;
         border-bottom: 1px solid #ddd;
      }

      th {
         background-color: #f2f2f2;
      }

      tr:hover {
         background-color: #f5f5f5;
      }

      .delete-btn {
         background-color: #ff3333;
         color: #fff;
         padding: 6px 12px;
         border: none;
         cursor: pointer;
         font-size: 14px;
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="bills">
   <h1 class="title">Bills List</h1>
   <table>
      <thead>
         <tr>
            <th>Bill ID</th>
            <th>Username</th>
            <th>Total</th>
            <th>Timestamp</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $billsCollection = $mongoDB->selectCollection('bills');
            $usersCollection = $mongoDB->selectCollection('users');
            
            $cursor = $billsCollection->find();

            foreach ($cursor as $bill) {
               $user_id = $bill['user_id'];
               $user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($user_id)]);
               $timestamp = $bill['timestamp']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'));
         ?>
         <tr>
            <td><?php echo $bill['_id']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo '₹' . $bill['total']; ?></td>
            <td><?php echo $timestamp->format('Y-m-d H:i:s'); ?></td>
            <td>
               <a href="admin_bills.php?delete=<?php echo $bill['_id']; ?>" onclick="return confirm('Delete this bill?');" class="delete-btn">Delete</a>
            </td>
         </tr>
         <?php
            }
         ?>
      </tbody>
   </table>
</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
