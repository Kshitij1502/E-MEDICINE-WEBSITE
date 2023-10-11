<?php
require 'vendor/autoload.php';
session_start();

include 'config.php';

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

// Connect to MongoDB
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace with your MongoDB database name
$cartCollection = $mongoDB->selectCollection('cart');

// Define an array to store alert messages
$messages = [];

if (isset($_POST['add_to_cart'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   // Check if the product already exists in the cart
   $existingProduct = $cartCollection->findOne(['user_id' => $user_id, 'name' => $product_name]);

   if ($existingProduct) {
      $messages[] = 'Product already added to cart!';
   } else {
      // Insert product into the cart
      $cartDocument = [
         'user_id' => $user_id,
         'name' => $product_name,
         'price' => $product_price,
         'quantity' => $product_quantity,
         'image' => $product_image,
      ];

      $cartCollection->insertOne($cartDocument);
      $messages[] = 'Product added to cart!';
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>our shop</h3>
   <p> <a href="home.php">home</a> / shop </p>
</div>

<section class="products">
      <h1 class="title">latest products</h1>
      <div class="box-container">
         <?php
         // Assuming you have connected to MongoDB as mentioned earlier

         // Select the products collection in MongoDB
         $productsCollection = $mongoDB->selectCollection('products');

         // Query to retrieve the latest 6 products and convert the cursor to an array
         $cursor = $productsCollection->find([], ['limit' => 20]);
         $products = iterator_to_array($cursor);

         // Check if there are products
         if (!empty($products)) {
            foreach ($products as $product) {
               ?>
               <form action="" method="post" class="box">
                  <img class="image" src="uploaded_img/<?php echo $product['image']; ?>" alt="">
                  <div class="name"><?php echo $product['name']; ?></div>
                  <div class="price">₹<?php echo $product['price']; ?>/-</div>
                  <input type="number" min="1" name="product_quantity" value="1" class="qty">
                  <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
                  <input type="submit" value="add to cart" name="add_to_cart" class="btn">
               </form>
               <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>

         <!-- Display alert messages using JavaScript -->
         <?php foreach ($messages as $message) { ?>
            <script>
               alert("<?php echo $message; ?>");
            </script>
         <?php } ?>

      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>
</html>
