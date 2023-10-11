<?php
require 'vendor/autoload.php';

$mongoUrl = "mongodb://localhost:27017";
$dbName = "medicine_db";

try {
    $client = new MongoDB\Client($mongoUrl);
    $db = $client->selectDatabase($dbName);
    $usersCollection = $db->selectCollection("users");
    $productsCollection = $db->selectCollection("products");
    $ordersCollection = $db->selectCollection("orders");
} catch (Exception $e) {
    echo "MongoDB connection failed: " . $e->getMessage();
    exit;
}

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['order_btn'])) {
    // Retrieve user's cart items from MongoDB
    $userCart = $usersCollection->findOne(['user_id' => $user_id]);

    if (!empty($userCart) && isset($userCart['cart'])) {
        $name = $_POST['name'];
        $number = $_POST['number'];
        $email = $_POST['email'];
        $method = $_POST['method'];
        $address = 'flat no. ' . $_POST['flat'] . ' ' . $_POST['street'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['country'] . ' - ' . $_POST['pin_code'];
        $placed_on = date('d-M-Y');

        $cart_grand_total = 0; // Moved this line here

        $cart_products = []; // Moved this line here

        foreach ($userCart['cart'] as $cart_item) {
            $cart_products[] = $cart_item['name'] . ' ( ' . $cart_item['quantity'] . ' )';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_grand_total += $sub_total; // Calculate grand total here
        }

        $total_products = implode(', ', $cart_products);

        $orderExist = $ordersCollection->findOne([
            'name' => $name,
            'number' => $number,
            'email' => $email,
            'method' => $method,
            'address' => $address,
            'total_products' => $total_products,
            'total_price' => $cart_grand_total, // Use the calculated grand total here
        ]);

        if ($cart_grand_total == 0) { // Check the grand total
            $message[] = 'Your cart is empty';
        } elseif ($orderExist) {
            $message[] = 'Order placed already!';
        } else {
            $insert_order = $ordersCollection->insertOne([
                'user_id' => $user_id,
                'name' => $name,
                'number' => $number,
                'email' => $email,
                'method' => $method,
                'address' => $address,
                'total_products' => $total_products,
                'total_price' => $cart_grand_total, // Use the calculated grand total here
                'placed_on' => $placed_on,
            ]);

            if ($insert_order->getInsertedCount() > 0) {
                // Clear user's cart
                $usersCollection->updateOne(['user_id' => $user_id], ['$set' => ['cart' => []]]);
                $message[] = 'Order placed successfully!';
            }
        }
    } else {
        $message[] = 'Your cart is empty';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>checkout</h3>
   <p> <a href="home.php">home</a> / checkout </p>
</div>

<section class="display-order">

<?php
$cartCollection = $db->selectCollection("cart_items");

// Debugging: Dump user_id to verify it is set correctly
var_dump($user_id);

$userCart = $cartCollection->findOne(['user_id' => new MongoDB\BSON\ObjectId($user_id)]);

// Debugging: Dump userCart to see if it contains cart items
var_dump($userCart);

$cart_grand_total = 0;

if (!empty($userCart) && isset($userCart['items'])) {
    foreach ($userCart['items'] as $cart_item) {
        $cart_total_price = ($cart_item['price'] * $cart_item['quantity']);
        $cart_grand_total += $cart_total_price;
        ?>
        <p><?= $cart_item['name']; ?> <span>(<?= '$' . $cart_item['price'] . '/- x ' . $cart_item['quantity']; ?>)</span></p>
        <?php
    }
} else {
    echo '<p class="empty">Your cart is empty!</p>';
}



// Debugging: Dump cart_grand_total to verify total calculation
var_dump($cart_grand_total);

?>

   <div class="grand-total">Grand total : <span>$<?= $cart_grand_total; ?>/-</span></div>
</section>


<section class="checkout">

   <form action="" method="post">
      <h3>place your order</h3>
      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" required placeholder="enter your name">
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" required placeholder="enter your number">
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" required placeholder="enter your email">
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method">
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paypal">paypal</option>
               <option value="paytm">paytm</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="number" min="0" name="flat" required placeholder="e.g. flat no.">
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="street" required placeholder="e.g. street name">
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" required placeholder="e.g. mumbai">
         </div>
         <div class="inputBox">
            <span>state :</span>
            <input type="text" name="state" required placeholder="e.g. maharashtra">
         </div>
         <div class="inputBox">
            <span>country :</span>
            <input type="text" name="country" required placeholder="e.g. india">
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" required placeholder="e.g. 123456">
         </div>
      </div>
      <input type="submit" value="order now" class="btn" name="order_btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
