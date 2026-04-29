# restPassword (Reset Password) 🔐

A simple PHP project to implement a **password reset system** by sending a reset link via email 📧, then updating the password using a `token`.

## Requirements ⚙️

* **XAMPP** (Apache + MySQL)
* **PHP** (preferably 8+)
* Composer (required if you want to reinstall dependencies)

## Project Files 📂

* `rest.php`: Page to enter email and send the reset link (uses PHPMailer via SMTP).
* `changePassword.php`: Page to update the password after opening the link (validates `token` + `email` then updates the password).
* `setup.php`: Database connection setup (PDO).

## Database Setup 🗄️

1. Open phpMyAdmin and create a database named:

* `password_reset_db`

2. Create a `users` table (example compatible with the current code):

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  reset_token VARCHAR(255) DEFAULT NULL
);
```

3. Update database connection details in `setup.php` if needed:

* `$db_host` / `$db_user` / `$db_pass` / `$db_name`

## Email Configuration (SMTP) 📬

The code in `rest.php` uses **Mailtrap** (Sandbox) as SMTP. Update the following values as needed:

* `$mail->Host`
* `$mail->Username`
* `$mail->Password`
* `$mail->Port`
* `$mail->setFrom(...)`

> Note: SMTP credentials are currently included inside `rest.php` (as in the project).

## Running on XAMPP 🚀

1. Start **Apache** and **MySQL** from the XAMPP control panel.
2. Make sure the project is located at:

* `c:\xampp\htdocs\restPassword`

3. Open the page:

* `http://localhost/restPassword/rest.php`

## Usage (Full Flow) 🔄

1. Add a user to the `users` table (with the same email you’ll test with) via phpMyAdmin.
2. Open `rest.php`, enter the email, and click send.
3. Get the reset link from Mailtrap (based on SMTP settings in `rest.php`).
4. Open the link in this format:

* `http://localhost/restPassword/changePassword.php?token=...&email=...`

5. Enter the new password and confirm it (minimum 8 characters).

## Important Notes ⚠️

* **The token is single-use**: after resetting the password, `reset_token` is set to `NULL`.
* If `token` or `email` is missing/invalid → an error will be displayed.
* The "Back" button in the pages currently has an empty link (`href=""`); update it based on your login page.
