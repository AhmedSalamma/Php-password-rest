<?php
require_once('setup.php');

$alert = null; 

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
$email = isset($_GET['email']) ? trim((string)$_GET['email']) : '';

if ($token === '' || $email === '') {
    $alert = ['type' => 'error', 'text' => 'الرابط غير مكتمل. يرجى فتح رابط إعادة التعيين من البريد مرة أخرى.'];
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $alert = ['type' => 'error', 'text' => 'صيغة البريد الإلكتروني في الرابط غير صحيحة.'];
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alert) {
    $password = (string)($_POST['password'] ?? '');
    $password_confirm = (string)($_POST['password_confirm'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = :token AND email = :email');
    $stmt->bindValue(':token', $token);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        if ($password !== $password_confirm) {
            $alert = ['type' => 'error', 'text' => 'كلمة المرور غير متطابقة.'];
        } elseif (strlen($password) < 8) {
            $alert = ['type' => 'error', 'text' => 'كلمة المرور قصيرة جداً (8 أحرف على الأقل).'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('
                UPDATE users 
                SET password = :password,
                    reset_token = NULL
                WHERE reset_token = :token 
                AND email = :email
            ');
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $alert = ['type' => 'success', 'text' => 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.'];
        }
    } else {
        $alert = ['type' => 'error', 'text' => 'الرابط غير صالح أو تم استخدامه من قبل.'];
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
        }
        
        .change-password-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }

        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.6;
            border: 1px solid transparent;
        }

        .alert--error {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #9f1239;
        }

        .alert--success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .help {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            line-height: 1.7;
            background: #f7f7fb;
            border: 1px solid #ececf4;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .help ul {
            margin: 8px 18px 0 0;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="change-password-card">
            <div class="logo">
                <h1>🔑 تغيير كلمة المرور</h1>
                <p>قم بتحديث كلمة المرور الخاصة بك</p>
            </div>

            <?php if ($alert): ?>
                <div class="alert <?php echo $alert['type'] === 'success' ? 'alert--success' : 'alert--error'; ?>">
                    <?php echo htmlspecialchars($alert['text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">كلمة المرور الجديدة</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="أدخل كلمة المرور الجديدة" 
                        minlength="8"
                        required
                    >
                    <div class="help">
                        شروط كلمة المرور:
                        <ul>
                            <li>8 أحرف على الأقل</li>
                            <li>يفضّل مزج حروف وأرقام لزيادة الأمان</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">تأكيد كلمة المرور</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="أعد إدخال كلمة المرور" 
                        required
                    >
                </div>
                
                <button type="submit">تحديث كلمة المرور</button>
            </form>
            
            <div class="back-link">
                <a href="">العودة للرئيسية</a>
            </div>
        </div>
    </div>
</body>
</html>
