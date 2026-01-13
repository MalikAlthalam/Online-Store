<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emad Store - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .welcome-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 80px 60px;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
        }
        
        .logo {
            font-size: 4rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        
        .tagline {
            color: #666;
            font-size: 1.4rem;
            margin-bottom: 50px;
            line-height: 1.6;
        }
        
        .main-button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 20px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            min-width: 250px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .main-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .subtitle {
            color: #888;
            font-size: 1rem;
            margin-top: 20px;
            font-style: italic;
        }
        
        .login-link {
            color: #667eea;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #667eea;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .login-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 600px) {
            .welcome-container {
                padding: 60px 40px;
                margin: 20px;
            }
            
            .logo {
                font-size: 3rem;
            }
            
            .tagline {
                font-size: 1.2rem;
            }
            
            .main-button {
                padding: 15px 40px;
                font-size: 1.1rem;
                min-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="logo">🛍️ Emad Store</div>
        <p class="tagline">مرحباً بك في متجر عماد<br>وجهتك المثالية للموضة</p>
        
        <a href="register.php" class="main-button">إنشاء حساب جديد</a>
        <br>
        <br>

        <small>أو</small>
        <br>
        
        <a href="login.php" class="login-link">تسجيل الدخول</a>
        
        <p class="subtitle">ابدأ رحلة التسوق معنا اليوم</p>
    </div>
</body>
</html>