<?php
require 'vendor/autoload.php';

$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->selectDatabase("medicine_db");
$collection = $database->selectCollection("users");

$message = array();

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $confirmPassword = $_POST['cpass'];

    // Check if user with the same email exists
    $existingUser = $collection->findOne(['email' => $email]);

    if ($existingUser) {
        $message[] = 'User email already exists!';
    } elseif ($password !== $confirmPassword) {
        $message[] = 'Confirm password not matched!';
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Assign user_type as "user"
        $user_type = "user";

        // Insert user data into MongoDB
        $insertResult = $collection->insertOne([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'user_type' => $user_type // Assign user_type
        ]);

        if ($insertResult->getInsertedCount()) {
            header('location: login.php');
            exit(); // Exit to prevent further execution
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      body {
         background-image: url('images/background.jpg');
         background-size: cover;
         background-repeat: no-repeat;
         background-attachment: fixed;
         margin: 0;
         padding: 0;
         font-family: Arial, sans-serif;
      }

      .message {
         background-color: rgba(255, 255, 255, 0.8);
         padding: 10px;
         margin: 10px;
         border-radius: 5px;
      }

      .message span {
         margin-right: 10px;
      }

      .btn {
         background-color: #007BFF;
         color: #fff;
         padding: 10px 20px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }
   </style>
</head>
<body>
<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<section class="form-container">
   <form action="" method="POST">
      <h3>register now</h3>
      <input type="text" name="name" class="box" placeholder="enter your name" required>
      <input type="email" name="email" class="box" placeholder="enter your email" required>
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="password" name="cpass" class="box" placeholder="confirm your password" required>
      <input type="submit" value="register now" class="btn" name="submit">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>
</section>
</body>
</html>
