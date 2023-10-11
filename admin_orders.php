<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

include 'config.php';

$admin_id = $_SESSION['user_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit;
}

// Set up MongoDB connection
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->selectDatabase("medicine_db");
$ordersCollection = $database->selectCollection("orders");

if (isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];

    // Update payment status in MongoDB
    $updateResult = $ordersCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($order_update_id)],
        ['$set' => ['payment_status' => $update_payment]]
    );

    if ($updateResult->getModifiedCount() === 1) {
        $message[] = 'Payment status has been updated!';
    } else {
        $message[] = 'Failed to update payment status.';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Delete the order from MongoDB
    $deleteResult = $ordersCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($delete_id)]);

    if ($deleteResult->getDeletedCount() === 1) {
        header('location:admin_orders.php');
        exit;
    } else {
        $message[] = 'Failed to delete order.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="orders">

   <h1 class="title">placed orders</h1>

   <div class="box-container">
      <?php
      $select_orders = $ordersCollection->find([]);
      foreach ($select_orders as $fetch_orders) {
      ?>
<div class="box">
    <p>User ID: <span><?php echo $fetch_orders['user_id'] ?? 'N/A'; ?></span></p>
    <p>Placed On: <span><?php echo $fetch_orders['placed_on'] ?? 'N/A'; ?></span></p>
    <p>Name: <span><?php echo $fetch_orders['name'] ?? 'N/A'; ?></span></p>
    <p>Number: <span><?php echo $fetch_orders['number'] ?? 'N/A'; ?></span></p>
    <p>Email: <span><?php echo $fetch_orders['email'] ?? 'N/A'; ?></span></p>
    <p>Address: <span><?php echo $fetch_orders['address'] ?? 'N/A'; ?></span></p>
    <p>Total Products: <span><?php echo $fetch_orders['total_products'] ?? 'N/A'; ?></span></p>
    <p>Total Price: <span>$<?php echo isset($fetch_orders['total_price']) ? number_format($fetch_orders['total_price'], 2) : 'N/A'; ?>/-</span></p>
    <p>Payment Method: <span><?php echo $fetch_orders['payment_method'] ?? 'N/A'; ?></span></p>
    <form action="" method="post">
        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['_id']; ?>">
        <select name="update_payment">
            <option value="" selected disabled><?php echo isset($fetch_orders['payment_status']) ? $fetch_orders['payment_status'] : 'N/A'; ?></option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
        </select>
        <input type="submit" value="Update" name="update_order" class="option-btn">
        <a href="admin_orders.php?delete=<?php echo $fetch_orders['_id']; ?>" onclick="return confirm('Delete this order?');" class="delete-btn">Delete</a>
    </form>
</div>

      <?php
      }
      ?>
   </div>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
