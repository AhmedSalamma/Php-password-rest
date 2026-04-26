# restPassword (Reset Password)

مشروع PHP بسيط لعمل **إعادة تعيين كلمة المرور** عبر إرسال رابط على البريد الإلكتروني، ثم تحديث كلمة المرور باستخدام `token`.

## المتطلبات

- **XAMPP** (Apache + MySQL)
- **PHP** (يفضّل 8+)
- Composer (لازم لو هتثبت الاعتماديات من جديد)

## ملفات المشروع

- `rest.php`: صفحة إدخال البريد وإرسال رابط إعادة التعيين (PHPMailer عبر SMTP).
- `changePassword.php`: صفحة تغيير كلمة المرور بعد فتح الرابط (تتحقق من `token` + `email` ثم تحدث كلمة المرور).
- `setup.php`: إعداد الاتصال بقاعدة البيانات (PDO).

## إعداد قاعدة البيانات

1) افتح phpMyAdmin وأنشئ قاعدة بيانات باسم:

- `password_reset_db`

2) أنشئ جدول `users` (مثال متوافق مع الكود الحالي):

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  reset_token VARCHAR(255) DEFAULT NULL
);
```

3) عدّل بيانات الاتصال في `setup.php` لو احتجت:

- `$db_host` / `$db_user` / `$db_pass` / `$db_name`

## إعداد البريد (SMTP)

الكود في `rest.php` يستخدم **Mailtrap** (Sandbox) كـ SMTP. عدّل القيم التالية بما يناسبك:

- `$mail->Host`
- `$mail->Username`
- `$mail->Password`
- `$mail->Port`
- `$mail->setFrom(...)`

> ملاحظة: بيانات SMTP موجودة حاليًا داخل `rest.php` (كما هي في المشروع).

## التشغيل على XAMPP

1) شغّل **Apache** و **MySQL** من لوحة تحكم XAMPP.
2) تأكد أن المشروع موجود هنا:

- `c:\xampp\htdocs\restPassword`

3) افتح الصفحة:

- `http://localhost/restPassword/rest.php`

## طريقة الاستخدام (تجربة كاملة)

1) أضف مستخدم في جدول `users` (بنفس البريد اللي هتجرب بيه) من phpMyAdmin.
2) افتح `rest.php` واكتب البريد واضغط إرسال.
3) خُد رابط إعادة التعيين من Mailtrap (حسب إعدادات SMTP الموجودة في `rest.php`).
4) افتح الرابط بالشكل:

- `http://localhost/restPassword/changePassword.php?token=...&email=...`

5) اكتب كلمة المرور الجديدة وتأكيدها (8 أحرف على الأقل).

## ملاحظات مهمة

- **الـ token يُستخدم مرة واحدة**: بعد تغيير كلمة المرور يتم تعيين `reset_token = NULL`.
- لو `token` أو `email` ناقص/غير صحيح → سيظهر خطأ.
- زر "العودة" في الصفحات حاليًا رابط فاضي (`href=""`)؛ عدّله حسب صفحة تسجيل الدخول عندك.
