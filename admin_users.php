<?php
// Include the MongoDB PHP library and establish a connection
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('medicine_db'); // Replace 'your_db_name' with your actual MongoDB database name

session_start();

$admin_id = $_SESSION['user_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $usersCollection = $mongoDB->selectCollection('users');
   $result = $usersCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($delete_id)]);

   if ($result->getDeletedCount() > 0) {
       header('location:admin_users.php');
       exit;
   } else {
       echo "Failed to delete user.";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">
   <h1 class="title"> user accounts </h1>
   <div class="box-container">
      <?php
         $usersCollection = $mongoDB->selectCollection('users');
         $users = $usersCollection->find();

         foreach ($users as $user) {
      ?>
      <div class="box">
         <p> user id : <span><?php echo $user['_id']; ?></span> </p>
         <p> username : <span><?php echo $user['name']; ?></span> </p>
         <p> email : <span><?php echo $user['email']; ?></span> </p>
         <p> user type : <span style="color:<?php if($user['user_type'] == 'admin'){ echo 'var(--orange)'; } ?>"><?php echo $user['user_type']; ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $user['_id']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">delete user</a>
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
