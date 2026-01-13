<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emad Store - Login</title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php
require "config.php";
require "auth.php";

$message = "";


if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST["username_email"]);
    $password = $_POST["password"];
    $remember_me = isset($_POST["remember_me"]);


    if (empty($input) || empty($password)) {
        $message = "❌ Please enter both username/email and password!";
    } else {
        try {
           
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :input OR email = :input) AND is_active = 1");
            $stmt->execute(['input' => $input]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
             
                if ($remember_me) {
                    $remember_token = generateRememberToken();
                 
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE id = :user_id");
                    $stmt->execute(['token' => $remember_token, 'user_id' => $user['id']]);
                    
                  
                    setRememberCookie($remember_token);
                }
                
             
                if ($user['role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit;
            } else {
                $message = "❌ Invalid username/email or password!";
            }
        } catch (PDOException $e) {
            $message = "❌ Database error. Please try again later.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

  <div class="wrapper">
    <form action="login.php" method="post">
      <h2>Login to Emad Store</h2>
        <div class="input-field">
        <input type="text" name="username_email" value="<?= isset($_POST['username_email']) ? htmlspecialchars($_POST['username_email']) : '' ?>" required tabindex="1">
        <label>Enter your email or username</label>
      </div>
      <div class="input-field">
        <input type="password" name="password" required tabindex="2">
        <label>Enter your password</label>
      </div>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember" name="remember_me" tabindex="3">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit" tabindex="4">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div>
      <?php if ($message): ?>
        <p style="color:red; text-align: center; margin-top: 10px;"><?= $message ?></p>
      <?php endif; ?>
    </form>
  </div>

  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      const usernameEmail = document.querySelector('input[name="username_email"]').value.trim();
      const password = document.querySelector('input[name="password"]').value.trim();
      
      if (!usernameEmail || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return false;
      }
    });
  </script>
</body>
</html>