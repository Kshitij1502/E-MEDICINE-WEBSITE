<?php
require 'config.php';
require 'vendor/autoload.php'; // Include MongoDB PHP driver

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
    exit;
}

$mongoClient = new MongoDB\Client("mongodb://localhost:27017"); // Connect to MongoDB
$cartCollection = $mongoClient->medicine_db->cart; // Replace 'your_database_name' with your actual database name

$message = []; // Initialize the $message array

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $cartQuery = [
        'name' => $product_name,
        'user_id' => $user_id,
    ];

    $existingCartItem = $cartCollection->findOne($cartQuery);

    if ($existingCartItem) {
        $message[] = 'Already added to cart!';
    } else {
        $cartDocument = [
            'user_id' => $user_id,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => $product_quantity,
            'image' => $product_image,
        ];

        $cartCollection->insertOne($cartDocument);
        $message[] = 'Product added to cart!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">
   <div class="content">
      <h3>Hand-picked health solutions delivered to your doorstep.</h3>
      <p>"Just like you deserve. Experience the convenience of E-Medicine today."</p>
      <a href="about.php" class="white-btn">discover more</a>
   </div>
</section>
<section class="products">
   <h1 class="title">latest products</h1>
   <div class="box-container">
   <?php
   // Assuming you have connected to MongoDB as mentioned earlier

   // Establish MongoDB connection
   $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
   
   // Select the products collection in MongoDB
   $productsCollection = $mongoClient->medicine_db->selectCollection('products');

   // Query to retrieve the latest 6 products and convert the cursor to an array
   $cursor = $productsCollection->find([], ['limit' => 6]);
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
       echo '<p class="empty">No products added yet!</p>';
   }
   ?>

      <!-- Remove this part of your code since it's referencing $fetch_products which is not defined -->
      
   </div>
   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">load more</a>
   </div>
</section>


<section class="about">
   <div class="flex">
      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>
      <div class="content">
         <h3>about us</h3>
         <p>E-Medicine is dedicated to providing accessible and reliable healthcare solutions. Our mission is to empower individuals with easy access to quality medicines, ensuring a healthier and happier community.</p>
         <a href="about.php" class="btn">read more</a>
      </div>
   </div>
</section>

<section class="home-contact">
   <div class="content">
      <h3>have any questions?</h3>
      <p>Have any questions about our services or products? Feel free to reach out to our dedicated support team.</p>
      <a href="contact.php" class="white-btn">contact us</a>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Display alert messages using JavaScript -->
<?php foreach ($message as $msg) { ?>
   <script>
      alert("<?php echo $msg; ?>");
   </script>
<?php } ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
