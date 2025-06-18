<?php
session_start();

// DB config
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'event_management';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$registerMsg = "";
$staySignUp = false;

// Handle Sign Up
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $pass = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

  $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $registerMsg = "Email already exists. Try another.";
    $staySignUp = true;
  } else {
    $insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $name, $email, $pass);
    if ($insert->execute()) {
      $registerMsg = "Registration successful. Please sign in!";
    } else {
      $registerMsg = "Something went wrong. Try again.";
      $staySignUp = true;
    }
    $insert->close();
  }
  $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login / Register</title>
  <link rel="stylesheet" href="../assets/styler/style.css" />
</head>
<body>
  <!-- Add class if signup failed -->
  <div class="container<?php if ($staySignUp) echo ' right-panel-active'; ?>" id="container">

    <!-- Sign Up -->
    <div class="form-container sign-up-container">
      <form action="" method="POST">
        <h1>Create Account</h1>
        <div class="social-container">
          <a href="#"><img src="https://img.icons8.com/color/48/google-logo.png" alt="Google"/></a>
          <a href="#"><img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook"/></a>
          <a href="#"><img src="https://img.icons8.com/color/48/linkedin.png" alt="LinkedIn"/></a>
        </div>
        <span>or use your email for registration</span>

        <?php if (!empty($registerMsg)): ?>
          <p style="color: <?php echo $staySignUp ? 'red' : 'green'; ?>;"><?php echo $registerMsg; ?></p>
        <?php endif; ?>

        <input type="text" name="name" placeholder="Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="signup">Sign Up</button>
      </form>
    </div>

    <!-- Sign In -->
    <div class="form-container sign-in-container">
      <form action="login.php" method="POST">
        <h1>Sign In</h1>
        <div class="social-container">
          <a href="#"><img src="https://img.icons8.com/color/48/google-logo.png" alt="Google"/></a>
          <a href="#"><img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook"/></a>
          <a href="#"><img src="https://img.icons8.com/color/48/linkedin.png" alt="LinkedIn"/></a>
        </div>
        <span>or use your account</span>
        
        <?php if (!empty($_SESSION["login_error"])): ?>
        <p style="color: red;"><?php echo $_SESSION["login_error"]; unset($_SESSION["login_error"]); ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <a href="#">Forgot your password?</a>
        <button type="submit" name="signin">Sign In</button>
      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>Enter your personal details to use all site features</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Friend!</h1>
          <p>Register with your personal details to use all of our features</p>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel Toggle Script -->
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const signUpButton = document.getElementById('signUp');
      const signInButton = document.getElementById('signIn');
      const container = document.getElementById('container');

      if (signUpButton && signInButton && container) {
        signUpButton.addEventListener('click', () => {
          container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
          container.classList.remove("right-panel-active");
        });
      }
    });
  </script>
</body>
</html>
