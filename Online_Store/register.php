<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emad Store - Registration</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

<?php
require "config.php";
require "auth.php";

$message = "";
$errors = [];

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $captcha_input = trim($_POST["captcha"]);
    $account_type = $_POST["account_type"] ?? "user";
    $admin_secret = $_POST["admin_secret"] ?? "";

    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters long";
    } elseif (strlen($username) > 20) {
        $errors['username'] = "Username must be less than 20 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Username can only contain letters, numbers, and underscores";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "Email is too long";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    } elseif (strlen($password) > 50) {
        $errors['password'] = "Password must be less than 50 characters";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)";
    }

    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = "Please confirm your password";
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
    }

    if (empty($captcha_input)) {
        $errors['captcha'] = "Please enter the CAPTCHA code";
    } elseif (!isset($_SESSION['captcha_text'])) {
        $errors['captcha'] = "CAPTCHA session expired. Please refresh the page.";
    } elseif (strtolower(trim($captcha_input)) !== strtolower(trim($_SESSION['captcha_text']))) {
        $errors['captcha'] = "Invalid CAPTCHA code. Please try again.";
    }

    if ($account_type === "admin") {
        if (empty($admin_secret)) {
            $errors['admin_secret'] = "Admin secret code is required";
        } elseif ($admin_secret !== "Admin@123456") {
            $errors['admin_secret'] = "Invalid admin secret code";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetch()) {
                $errors['username'] = "Username already exists. Please choose a different username.";
            }

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $errors['email'] = "Email already exists. Please use a different email address.";
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
                $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword, 'role' => $account_type]);
                
                if ($stmt->rowCount() > 0) {
                    $account_type_text = $account_type === 'admin' ? 'Admin' : 'User';
                    $message = "✅ $account_type_text account created successfully! Please login with your credentials.";
                    $_POST = [];
                } else {
                    $message = "❌ Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $message = "❌ Database error. Please try again later.";
            // Log the error for debugging (in production, don't show detailed errors)
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
    <div class="container">
        <div class="registration-form">
            <h1>Emad Store</h1>
            <h2>Create Your Account</h2>
            <form id="registrationForm" action="register.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required tabindex="1">
                    <span class="error-message"><?= isset($errors['username']) ? $errors['username'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required tabindex="2">
                    <span class="error-message"><?= isset($errors['email']) ? $errors['email'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required tabindex="3">
                    <span class="error-message"><?= isset($errors['password']) ? $errors['password'] : '' ?></span>
                    <small style="color: #666; font-size: 12px;">Password must be at least 8 characters with uppercase, lowercase, number, and special character</small>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required tabindex="4">
                    <span class="error-message"><?= isset($errors['confirmPassword']) ? $errors['confirmPassword'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label>Account Type</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="account_type" value="user" checked tabindex="5" style="margin-right: 8px;">
                            <span>Regular User</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="account_type" value="admin" tabindex="6" style="margin-right: 8px;">
                            <span>Admin User</span>
                        </label>
                    </div>
                </div>
                <div class="form-group" id="admin-secret-group" style="display: none;">
                    <label for="admin_secret">Admin Secret Code</label>
                    <input type="password" id="admin_secret" name="admin_secret" tabindex="7" placeholder="Enter admin secret code">
                    <span class="error-message"><?= isset($errors['admin_secret']) ? $errors['admin_secret'] : '' ?></span>
                    
                </div>
                <div class="form-group">
                    <label for="captcha">Security Verification</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="text" id="captcha" name="captcha" required tabindex="5" style="flex: 1;">
                        <img src="captcha.php" alt="CAPTCHA" style="border: 1px solid #ddd; cursor: pointer;" onclick="this.src='captcha.php?'+Math.random()" title="Click to refresh">
                    </div>
                    <span class="error-message"><?= isset($errors['captcha']) ? $errors['captcha'] : '' ?></span>
                    <small style="color: #666; font-size: 12px;">Please enter the characters shown in the image above</small>
                </div>
                <div class="button-group">
                    <button type="submit" id="registerBtn" tabindex="8">Register Account</button>
                    <button type="button" id="loginBtn" tabindex="9">Login</button>
                </div>
                <p style="color:red;"><?= $message ?></p>
            </form>
        </div>
    </div>

    <script>
        // Client-side validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            let isValid = true;
            const errors = {};

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

            // Username validation
            const username = document.getElementById('name').value.trim();
            if (username.length < 3) {
                errors.username = 'Username must be at least 3 characters long';
                isValid = false;
            } else if (username.length > 20) {
                errors.username = 'Username must be less than 20 characters';
                isValid = false;
            } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                errors.username = 'Username can only contain letters, numbers, and underscores';
                isValid = false;
            }

            // Email validation
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errors.email = 'Please enter a valid email address';
                isValid = false;
            }

            // Password validation
            const password = document.getElementById('password').value;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/;
            if (password.length < 8) {
                errors.password = 'Password must be at least 8 characters long';
                isValid = false;
            } else if (!passwordRegex.test(password)) {
                errors.password = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)';
                isValid = false;
            }

            // Confirm password validation
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                errors.confirmPassword = 'Passwords do not match';
                isValid = false;
            }

            // Display errors
            if (!isValid) {
                e.preventDefault();
                Object.keys(errors).forEach(field => {
                    const errorElement = document.querySelector(`[name="${field}"]`).nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.textContent = errors[field];
                    }
                });
            }
        });

        // Real-time password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[@$!%*?&]/.test(password)
            };

            const strengthText = Object.values(strength).filter(Boolean).length;
            let strengthMessage = '';
            let strengthColor = '';

            if (strengthText < 3) {
                strengthMessage = 'Weak';
                strengthColor = 'red';
            } else if (strengthText < 5) {
                strengthMessage = 'Medium';
                strengthColor = 'orange';
            } else {
                strengthMessage = 'Strong';
                strengthColor = 'green';
            }

            // Update strength indicator if it exists
            const strengthIndicator = this.parentNode.querySelector('.password-strength');
            if (!strengthIndicator) {
                const indicator = document.createElement('small');
                indicator.className = 'password-strength';
                indicator.style.color = strengthColor;
                indicator.textContent = `Password strength: ${strengthMessage}`;
                this.parentNode.appendChild(indicator);
            } else {
                strengthIndicator.style.color = strengthColor;
                strengthIndicator.textContent = `Password strength: ${strengthMessage}`;
            }
        });

        // Login button functionality
        document.getElementById('loginBtn').addEventListener('click', function() {
            window.location.href = 'login.php';
        });

        // Admin account type toggle
        document.querySelectorAll('input[name="account_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const adminSecretGroup = document.getElementById('admin-secret-group');
                const adminSecretInput = document.getElementById('admin_secret');
                
                if (this.value === 'admin') {
                    adminSecretGroup.style.display = 'block';
                    adminSecretInput.required = true;
                } else {
                    adminSecretGroup.style.display = 'none';
                    adminSecretInput.required = false;
                    adminSecretInput.value = '';
                }
            });
        });
    </script>
</body>
</html> 