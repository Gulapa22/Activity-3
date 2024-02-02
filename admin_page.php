<?php
session_start();

// Include database connection
include 'config.php';

// Check if the user is not logged in, redirect to login page
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the form is submitted to add a new product
if(isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/'.$product_image;

    if(empty($product_name) || empty($product_price) || empty($product_image)) {
        $message[] = 'Please fill out all fields.';
    } else {
        // Insert product into the database
        $insert = "INSERT INTO products (name, price, image) VALUES ('$product_name', '$product_price', '$product_image')";
        $upload = mysqli_query($conn, $insert);
        if($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'New product added successfully.';
        } else {
            $message[] = 'Could not add the product.';
        }
    }
}

// Check if the product deletion request is received
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header('location: admin_page.php');
    exit;
}

// Fetch products from the database
$select = mysqli_query($conn, "SELECT * FROM products");
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
      <style>
      /* Style for navbar */
      .navbar {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 10px 20px;
         background-color: #333;
         color: #fff;
      }
      /* Style for logout button */
      .logout-btn {
         background-color: transparent;
         border: none;
         color: #fff;
         cursor: pointer;
      }
   </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
   <!-- Navbar brand/logo if needed -->
   <!-- <div class="navbar-brand">Your Logo</div> -->
   <!-- Logout button at the upper right corner -->
   <button class="logout-btn" onclick="logout()">
      <i class="fas fa-sign-out-alt"></i> Logout
   </button>
</div>

</head>



<body>

<?php

if(isset($message)){
   foreach($message as $message){
      echo '<span class="message">'.$message.'</span>';
   }
}

?>
   
<div class="container">

   <div class="admin-product-form-container">

      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
         <h3>add a new product</h3>
         <input type="text" placeholder="enter product name" name="product_name" class="box">
         <input type="number" placeholder="enter product price" name="product_price" class="box">
         <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
         <input type="submit" class="btn" name="add_product" value="add product">
      </form>

   </div>

   <?php

   $select = mysqli_query($conn, "SELECT * FROM products");
   
   ?>
   <div class="product-display">
      <table class="product-display-table">
         <thead>
         <tr>
            <th>product image</th>
            <th>product name</th>
            <th>product price</th>
            <th>action</th>
         </tr>
         </thead>
         <?php while($row = mysqli_fetch_assoc($select)){ ?>
         <tr>
            <td><img src="uploaded_img/<?php echo $row['image']; ?>" height="100" alt=""></td>
            <td><?php echo $row['name']; ?></td>
            <td>$<?php echo $row['price']; ?>/-</td>
            <td>
               <a href="admin_update.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> edit </a>
               <a href="admin_page.php?delete=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> delete </a>
            </td>
         </tr>
      <?php } ?>
      </table>
   </div>

</div>

<script>
   // JavaScript function for logout
   function logout() {
      // Redirect to logout.php or your logout endpoint
      window.location.href = "logout.php";
   }
</script>



</body>
</html>