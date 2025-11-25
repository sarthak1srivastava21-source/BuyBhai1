<?php
session_start();

// connect to InfinityFree database
$db = mysqli_connect('sql312.infinityfree.com', 'if0_40484470', 'iSlljBTQt94OSP', 'if0_40484470_de');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$errors = array();


// REGISTER USER
if (isset($_POST['reg_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    if (empty($username)) { $errors[] = "Username is required"; }
    if (empty($email)) { $errors[] = "Email is required"; }
    if (empty($password_1)) { $errors[] = "Password is required"; }
    if ($password_1 != $password_2) { $errors[] = "Passwords do not match"; }

    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['username'] === $username) { $errors[] = "Username already exists"; }
        if ($user['email'] === $email) { $errors[] = "Email already exists"; }
    }

    if (count($errors) == 0) {
        $password = password_hash($password_1, PASSWORD_DEFAULT); // secure hash
        $query = "INSERT INTO users (username, email, password) VALUES('$username', '$email', '$password')";
        mysqli_query($db, $query);
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        header('location: index.php');
    }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) { $errors[] = "Username is required"; }
    if (empty($password)) { $errors[] = "Password is required"; }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE username='$username'";
        $results = mysqli_query($db, $query);
        if ($results && mysqli_num_rows($results) == 1) {
            $row = mysqli_fetch_assoc($results);
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['success'] = "You are now logged in";
                header('location: index.php');
            } else {
                $errors[] = "Wrong username/password combination";
            }
        } else {
            $errors[] = "User not found";
        }
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Login & Registration</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { width: 350px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <header style="background-color:#333; padding:10px 0;">
  <nav>
    <ul style="list-style:none; margin:0; padding:0; display:flex; justify-content:center;">
      <li style="margin:0 15px;">
        <a href="home.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Home</a>
      </li>
      <li style="margin:0 15px;">
        <a href="discount.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Discounts</a>
      </li>
      <li style="margin:0 15px;">
        <a href="delivery.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Delivery</a>
      </li>
      <li style="margin:0 15px;">
        <a href="addToCart.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Cart</a>
      </li>
      <li style="margin:0 15px;">
        <a href="payment.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Payment</a>
      </li>
      <li style="margin:0 15px;">
        <a href="signin.html" style="text-decoration:none; color:white; font-weight:bold; transition:color 0.3s;">Sign In</a>
      </li>
    </ul>
  </nav>
</header>
<div class="container">
    <?php if (isset($_SESSION['username'])): ?>
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <p><a href="index.php?logout=1"><button>Logout</button></a></p>
    <?php else: ?>
        <h2>Register</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password_1" placeholder="Password" required>
            <input type="password" name="password_2" placeholder="Confirm Password" required>
            <button type="submit" name="reg_user">Register</button>
        </form>

        <h2>Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login_user">Login</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
</div>
</body>
</html>