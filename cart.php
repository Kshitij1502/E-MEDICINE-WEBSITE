<?php
session_start();

include 'config.php'; // Include the config.php file to define $cartCollection and other variables.

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

$message = ''; // Variable to store alert messages
$billGenerated = false; // Variable to track if the bill was generated

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST['generate_bill'])) {
      // Fetch cart data for the current user
      $cursor = $cartCollection->find(['user_id' => $user_id]);
      $cartItems = iterator_to_array($cursor);

      if (!empty($cartItems)) {
         // Calculate the total amount in the cart
         $total = 0;
         foreach ($cartItems as $cartItem) {
            $price = floatval($cartItem['price']);
            $quantity = intval($cartItem['quantity']);
            $total += $price * $quantity;
         }

         // Create a bill document
         $billData = [
            'user_id' => $user_id,
            'cart_items' => $cartItems,
            'total' => $total,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
         ];

         // Insert the bill document into the bills collection
         $insertResult = $billsCollection->insertOne($billData);

         // Get the inserted bill ID
         $billId = (string)$insertResult->getInsertedId();

         // Clear the user's cart
         $cartCollection->deleteMany(['user_id' => $user_id]);

         // Set a flag to indicate that the bill was generated
         $billGenerated = true;
      }
   } elseif (isset($_POST['add_to_cart'])) {
      // Handle adding products to the cart
      // ...
   } elseif (isset($_POST['update_cart'])) {
      // Handle updating the cart
      // ...
   } elseif (isset($_POST['delete_cart'])) {
      // Handle deleting items from the cart
      // ...
   }
}

// Fetch cart data for the current user
$cursor = $cartCollection->find(['user_id' => $user_id]);
$cartItems = iterator_to_array($cursor);

// Calculate the total amount in the cart
$total = 0;
foreach ($cartItems as $cartItem) {
   $price = floatval($cartItem['price']);
   $quantity = intval($cartItem['quantity']);
   $total += $price * $quantity;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Cart</title>
   <link rel="stylesheet" href="css/style.css">
   <!-- Add your CSS styles here -->
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>Your Cart</h3>
   <p> <a href="home.php">Home</a> / Cart </p>
</div>

<section class="products">
   <h1 class="title">Cart Items</h1>
   <div class="box-container">
      <?php
      if (!empty($cartItems)) {
         foreach ($cartItems as $cartItem) {
            // Display cart items here
            echo '<div class="box">';
            echo '<img class="image" src="uploaded_img/' . $cartItem['image'] . '" alt="">';
            echo '<div class="name">' . $cartItem['name'] . '</div>';
            echo '<div class="price">₹' . $cartItem['price'] . '/-</div>';
            echo '<div class="quantity">Quantity: ';
            echo '<input type="number" min="1" value="' . $cartItem['quantity'] . '" class="qty" data-cart-id="' . $cartItem['_id'] . '">';
            echo '<button class="update-btn" data-cart-id="' . $cartItem['_id'] . '">Update</button>';
            echo '<button class="delete-btn" data-cart-id="' . $cartItem['_id'] . '">Delete</button>';
            echo '</div>';
            echo '</div>';
         }
         ?>
         <form action="cart.php" method="post">
            <?php if (!empty($cartItems)) { ?>
               <input type="submit" name="generate_bill" value="Generate Bill" class="btn">
               <?php if ($billGenerated) { ?>
                  <p>Bill generated successfully. <a href="generate_bill.php?bill_id=<?php echo $billId; ?>">Download Bill</a></p>
               <?php } ?>
            <?php } else { ?>
               <p class="empty">Your cart is empty</p>
            <?php } ?>
         </form>
      <?php
      } else {
         echo '<p class="empty">Your cart is empty</p>';
      }
      ?>
      <div class="total">
         <strong>Total:</strong> ₹<?php echo number_format($total, 2); ?> /-
      </div>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Include JavaScript to display the alert -->
<script>
   <?php if (!empty($message)) { ?>
      // Check if there's a message to display for "Add to Cart," "Update," or "Delete"
      alert("<?php echo $message; ?>");
   <?php } ?>
   <?php if ($billGenerated) { ?>
      // Check if the bill was generated and display an alert if it was
      alert("Bill generated successfully! Bill ID: <?php echo $billId; ?>");
   <?php } ?>
   // Add JavaScript code to handle quantity update and item deletion here
   const updateButtons = document.querySelectorAll('.update-btn');
   const deleteButtons = document.querySelectorAll('.delete-btn');

   updateButtons.forEach(button => {
      button.addEventListener('click', function() {
         const cartItemId = this.getAttribute('data-cart-id');
         const quantityInput = document.querySelector(`.qty[data-cart-id="${cartItemId}"]`);
         const newQuantity = quantityInput.value;

         // Send an AJAX request to update the quantity in the database
         fetch('update_cart.php', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cartItemId=${cartItemId}&newQuantity=${newQuantity}`,
         })
         .then(response => response.json())
         .then(data => {
            if (data.success) {
               alert(`Updated quantity for item ID ${cartItemId} to ${newQuantity}`);
               // Reload the page to reflect the updated data
               window.location.reload();
            } else {
               alert('Failed to update quantity.');
            }
         })
         .catch(error => {
            console.error('Error:', error);
            alert('Failed to update quantity.');
         });
      });
   });

   deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
         const cartItemId = this.getAttribute('data-cart-id');

         // Send an AJAX request to delete the item from the cart in the database
         fetch('delete_cart.php', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cartItemId=${cartItemId}`,
         })
         .then(response => response.json())
         .then(data => {
            if (data.success) {
               alert(`Deleted item ID ${cartItemId} from cart`);
               // Reload the page to reflect the updated data
               window.location.reload();
            } else {
               alert('Failed to delete item.');
            }
         })
         .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete item.');
         });
      });
   });
</script>

</body>
</html>
