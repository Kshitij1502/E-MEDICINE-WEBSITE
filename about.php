<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>About Us</h3>
   <p> <a href="home.php">Home</a> / About </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>Why Choose Us?</h3>
         <p>Quality Assurance: We prioritize your well-being by ensuring all our products meet the highest quality standards.</p>
         <p>Convenience Redefined: With our user-friendly platform, you can easily browse, order, and have your medications delivered right to your doorstep, saving you time and hassle.</p>
         <P>Expert Guidance: Our team of experienced healthcare professionals is here to provide personalized guidance and support.</P>
         <a href="contact.php" class="btn">Contact Us</a>
      </div>

   </div>

</section>


<?php include 'footer.php'; ?>

<!-- Custom JavaScript file link -->
<script src="js/script.js"></script>

</body>
</html>
