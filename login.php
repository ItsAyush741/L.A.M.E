<?php
session_start();
$host = 'localhost';
$db = 'l.a.m.e';
$user = 'root';
$pass = '';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare('SELECT name, password FROM student WHERE student_id=?');
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $stmt->bind_result($name, $db_password);
    if ($stmt->fetch() && $password === $db_password) { // In production use password_verify()
        $_SESSION['student_id'] = $student_id;
        $_SESSION['name'] = $name;
        header('Location: homepage.php');
        exit;
    } else {
        $error = 'Invalid Student ID or Password.';
    }
    $stmt->close(); $conn->close();
}
?>
<!-- Place your HTML/CSS login form here, show $error if set -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. Login</title>
    <style>
        body {
            background: #f7fafc; font-family: Arial, sans-serif;
            display: flex; justify-content: center; align-items: center; height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #f2f8fc; padding: 3em 3.5em;
            border-radius: 10px;
            width: 320px;
            box-sizing: border-box;
            text-align: center;
            box-shadow: 0 4px 18px rgba(30,64,175,0.04);
        }
        .logo {
            font-weight: 700; font-size: 20px;
            margin-bottom: 10px; color: #222222;
            letter-spacing: 0.03em;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .logo .icon {
            font-weight: 900; font-size: 26px;
        }
        h1 {
            font-weight: 700; font-size: 1.5rem; margin-bottom: .1rem;
            color: #222222;
        }
        p.subtitle {
            margin: 0 0 1em 0; font-size: 0.9rem; color: #444;
        }
        .scan-box {
            border: 2px dashed #d0d7df;
            border-radius: 10px;
            padding: 30px 20px;
            margin-bottom: 20px;
        }
        .scan-box strong {
            font-weight: 700;
            display: block;
            margin-bottom: 6px;
            font-size: 1.1rem;
            color: #222222;
        }
        .scan-box small {
            display: block;
            font-weight: 400;
            margin-bottom: 15px;
            color: #555;
        }
        .scan-button {
            background: #e4e9eb;
            border: none;
            color: #222;
            font-weight: 700;
            padding: 0.7em 2.5em;
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .scan-button:hover {
            background: #d0d7df;
        }
        hr {
            border: none;
            height: 1px;
            background: #cfd7e0;
            margin: 15px 0;
            position: relative;
        }
        hr::before {
            content: "or";
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f7fafc;
            color: #66b0ee;
            font-weight: 700;
            padding: 0 10px;
        }
        input[type=text], input[type=password] {
            width: 100%;
            padding: 0.7em 0.9em;
            border-radius: 10px;
            border: 1px solid #d8e2ec;
            font-size: 1rem;
            margin-bottom: 15px;
            box-sizing: border-box;
            color: #333;
        }
        input::placeholder {
            color: #9eafbc;
        }
        button.login-button {
            width: 100%;
            padding: 0.7em 0;
            border-radius: 10px;
            background: #e4e9eb;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.17s ease-in-out;
        }
        button.login-button:hover {
            background: #d0d7df;
        }
        .error-message {
            color: #d32d2d;
            font-weight: 600;
            margin-top: 1em;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <span class="icon">&#9776;</span><span>L.A.M.E.</span>
        </div>
        <h1>Welcome to L.A.M.E.</h1>
        <p class="subtitle">Please log in to manage your library account.</p>
        <form method="post" autocomplete="off">
            <div class="scan-box">
                <strong>Scan ID Card</strong>
                <small>Tap your ID card on the scanner</small>
                <button type="button" class="scan-button">Scan Now</button>
            </div>
            <hr />
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login-button">Log In</button>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
