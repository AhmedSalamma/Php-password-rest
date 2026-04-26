<?php
require_once('setup.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['email'])) {
        $alert = ['type' => 'error', 'text' => 'يرجى إدخال البريد الإلكتروني.'];
    } else {
        $email = trim((string)$_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $alert = ['type' => 'error', 'text' => 'صيغة البريد الإلكتروني غير صحيحة.'];
        } else {
            $token = bin2hex(random_bytes(20));

            $stmt = $pdo->prepare("
                UPDATE users 
                SET reset_token = :token 
                WHERE email = :email
            ");

            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'b991a17a7f0719';
                    $mail->Password = '366d4fef02c0e1';
                    $mail->Port = 587;

                    $mail->setFrom('no-reply@test.com', 'My App');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password';

                    $link = "http://localhost/restPassword/changePassword.php?token=$token&email=$email";

                    $mail->Body = "
                        <h3>Reset Password</h3>
                        <p>اضغط على الرابط:</p>
                        <a href='$link'>$link</a>
                    ";

                    $mail->send();
                    $alert = ['type' => 'success', 'text' => 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني.'];

                } catch (Exception $e) {
                    $alert = ['type' => 'error', 'text' => "تعذّر إرسال البريد: {$mail->ErrorInfo}"];
                }

            } else {
                $alert = ['type' => 'error', 'text' => 'هذا المستخدم غير موجود في سجلاتنا.'];
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
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
        
        .reset-card {
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
        
        .info-text {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
            line-height: 1.6;
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
        
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input[type="email"]:focus {
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
        <div class="reset-card">
            <div class="logo">
                <h1>🔐 إعادة تعيين كلمة المرور</h1>
                <p>استعيد حسابك بأمان</p>
            </div>

            <?php if ($alert): ?>
                <div class="alert <?php echo $alert['type'] === 'success' ? 'alert--success' : 'alert--error'; ?>">
                    <?php echo htmlspecialchars($alert['text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="info-text">
                    أدخل عنوان بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور.
                </div>
                
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" required>
                </div>
                
                <button type="submit">إرسال رابط إعادة التعيين</button>
            </form>
            
            <div class="back-link">
                <a href="">العودة لصفحة تسجيل الدخول</a>
            </div>
        </div>
    </div>
</body>
</html>
