<?php
@include 'config.php';

session_start();

// Set up MongoDB connection
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->selectDatabase("medicine_db");
$usersCollection = $database->selectCollection("users");

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $password = $_POST['pass'];
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    // Search for user by email
    $user = $usersCollection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['_id'];

        $userType = isset($user['user_type']) ? trim($user['user_type']) : '';

        switch ($userType) {
            case 'admin':
                // Redirect to admin page
                header('Location:admin_page.php');
                exit();
            case 'user':
                // Redirect to user dashboard
                header('Location:home.php?user=customer');
                exit();
            default:
                // Handle unknown user type
                echo "Unknown user type!";
        }

    } else {
        $message[] = 'Incorrect email or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

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
      <h3>login now</h3>
      <input type="email" name="email" class="box" placeholder="enter your email" required>
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="submit" value="login now" class="btn" name="submit">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</section>

</body>
</html>

