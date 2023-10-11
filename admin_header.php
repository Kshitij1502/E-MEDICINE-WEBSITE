<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Add your HTML head content here -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Admin Panel</title>
   <!-- Add your CSS and other head content here -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
<header class="header">
   <div class="flex">
      <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>
      <nav class="navbar">
         <a href="admin_page.php">Home</a>
         <a href="admin_products.php">Products</a>
         <a href="admin_users.php">Users</a>
         <a href="admin_contacts.php">Messages</a>
         <a href="admin_bills.php">Bills</a>
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>
      <div class="account-box">
         <?php if (isset($_SESSION['name'])) : ?>
            <p>Username: <span><?php echo $_SESSION['name']; ?></span></p>
         <?php endif; ?>
         <?php if (isset($_SESSION['email'])) : ?>
            <p>Email: <span><?php echo $_SESSION['email']; ?></span></p>
         <?php endif; ?>
         <?php if (isset($user)) : ?>
            <p><?= $user['name']; ?></p>
            <a href="logout.php" class="delete-btn">Logout</a>
         <?php else : ?>
            <div>New <a href="login.php">Login</a> | <a href="register.php">Register</a></div>
         <?php endif; ?>
      </div>
   </div>
</header>
<!-- Add the rest of your HTML content here -->
<!-- Add your JavaScript and other footer content here -->
</body>
</html>
