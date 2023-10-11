<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['send'])) {
   // Get form data
   $name = $_POST['name'];
   $email = $_POST['email'];
   $number = $_POST['number'];
   $msg = $_POST['message'];

   // Set up MongoDB connection
   $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
   $mongoDB = $mongoClient->selectDatabase('medicine_db');
   $messageCollection = $mongoDB->selectCollection('message');

   // Check if the message already exists
   $existingMessage = $messageCollection->findOne([
      'name' => $name,
      'email' => $email,
      'number' => $number,
      'message' => $msg
   ]);

   if ($existingMessage) {
      $message[] = 'Message sent already!';
   } else {
      // Insert message into MongoDB
      $messageDocument = [
         'user_id' => $user_id,
         'name' => $name,
         'email' => $email,
         'number' => $number,
         'message' => $msg
      ];

      $messageCollection->insertOne($messageDocument);
      $message[] = 'Message sent successfully!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>contact us</h3>
   <p> <a href="home.php">home</a> / contact </p>
</div>

<section class="contact">

   <form action="" method="post">
      <h3>say something!</h3>
      <input type="text" name="name" required placeholder="enter your name" class="box">
      <input type="email" name="email" required placeholder="enter your email" class="box">
      <input type="number" name="number" required placeholder="enter your number" class="box">
      <textarea name="message" class="box" placeholder="enter your message" id="" cols="30" rows="10"></textarea>
      <input type="submit" value="send message" name="send" class="btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link -->
<script src="js/script.js"></script>

</body>
</html>
