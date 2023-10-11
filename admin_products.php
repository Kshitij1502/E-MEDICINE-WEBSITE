<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['user_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

// Set up MongoDB connection
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->selectDatabase("medicine_db");
$productsCollection = $database->selectCollection("products");

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];

    // Check if the product with the same name already exists
    $existingProduct = $productsCollection->findOne(['name' => $name]);
    if ($existingProduct) {
        $message[] = 'Product name already exists';
    } else {
        // Handle file upload
        $image = $_FILES['image'];
        $imageFileName = $image['name'];
        $imageSize = $image['size'];
        $imageTmpName = $image['tmp_name'];
        $imageFolder = 'uploaded_img/' . $imageFileName;

        if ($imageSize > 2000000) {
            $message[] = 'Image size is too large';
        } else {
            move_uploaded_file($imageTmpName, $imageFolder);

            // Insert product into MongoDB
            $productDocument = [
                'name' => $name,
                'price' => $price,
                'image' => $imageFileName,
            ];

            $insertResult = $productsCollection->insertOne($productDocument);

            if ($insertResult->getInsertedCount() === 1) {
                $message[] = 'Product added successfully!';
            } else {
                $message[] = 'Product could not be added!';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $productToDelete = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($delete_id)]);

    if ($productToDelete) {
        // Remove the image file
        unlink('uploaded_img/' . $productToDelete['image']);

        // Delete the product from MongoDB
        $productsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($delete_id)]);
    }

    header('location:admin_products.php');
    exit;
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = (float)$_POST['update_price'];
    $update_old_image = $_POST['update_old_image'];

    // Handle file upload
    $update_image = $_FILES['update_image'];
    $update_imageFileName = $update_image['name'];
    $update_imageSize = $update_image['size'];
    $update_imageTmpName = $update_image['tmp_name'];
    $update_imageFolder = 'uploaded_img/' . $update_imageFileName;

    if (!empty($update_imageFileName)) {
        if ($update_imageSize > 2000000) {
            $message[] = 'Image file size is too large';
        } else {
            move_uploaded_file($update_imageTmpName, $update_imageFolder);

            // Update product in MongoDB
            $updateResult = $productsCollection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($update_p_id)],
                [
                    '$set' => [
                        'name' => $update_name,
                        'price' => $update_price,
                        'image' => $update_imageFileName,
                    ],
                ]
            );

            if ($updateResult->getModifiedCount() === 1) {
                // Remove the old image file
                unlink('uploaded_img/' . $update_old_image);
                $message[] = 'Product updated successfully!';
            } else {
                $message[] = 'Product could not be updated!';
            }
        }
    } else {
        // Update product data (without image change) in MongoDB
        $updateResult = $productsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($update_p_id)],
            [
                '$set' => [
                    'name' => $update_name,
                    'price' => $update_price,
                ],
            ]
        );

        if ($updateResult->getModifiedCount() === 1) {
            $message[] = 'Product updated successfully!';
        } else {
            $message[] = 'Product could not be updated!';
        }
    }

    header('location:admin_products.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">shop products</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>add product</h3>
      <input type="text" name="name" class="box" placeholder="enter product name" required>
      <input type="number" min="0" name="price" class="box" placeholder="enter product price" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="add product" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = $productsCollection->find([]);
         foreach ($select_products as $fetch_products) {
      ?>
      <div class="box">
         <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="price">₹<?php echo $fetch_products['price']; ?>/-</div>
         <a href="admin_products.php?update=<?php echo $fetch_products['_id']; ?>" class="option-btn">update</a>
         <a href="admin_products.php?delete=<?php echo $fetch_products['_id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">delete</a>
      </div>
      <?php
         }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if (isset($_GET['update'])) {
         $update_id = $_GET['update'];
         $update_product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($update_id)]);

         if ($update_product) {
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $update_id; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $update_product['image']; ?>">
      <img src="uploaded_img/<?php echo $update_product['image']; ?>" alt="">
      <input type="text" name="update_name" value="<?php echo $update_product['name']; ?>" class="box" required placeholder="enter product name">
      <input type="number" name="update_price" value="<?php echo $update_product['price']; ?>" min="0" class="box" required placeholder="enter product price">
      <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="update" name="update_product" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      } else {
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
