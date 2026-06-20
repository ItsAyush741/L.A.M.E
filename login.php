<?php
session_start();
$host = "localhost"; $db = "l.a.m.e"; $user = "root"; $pass = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT name, password FROM student WHERE student_id=?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($name, $db_password);

    if ($stmt->fetch() && $password === $db_password) {
        $_SESSION['student_id'] = $student_id;
        $_SESSION['name'] = $name;
        header('Location: homepage.php'); exit;
    } else {
        $error = "Invalid Student ID or Password.";
    }
    $stmt->close(); $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f7fafc;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', Arial, sans-serif;
        }
        .login-container {
            background: #f2f8fc;
            padding: 2.5rem 3rem;
            border-radius: 1rem;
            box-shadow: 0 4px 18px rgba(30, 64, 175, 0.07);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 1rem;
            color: #222;
            letter-spacing: 0.03em;
            text-align: center;
        }
        .logo .icon {
            font-weight: 900;
            font-size: 30px;
            vertical-align: middle;
            margin-right: 8px;
        }
        .error-message {
            color: #d32d2d;
            font-weight: 600;
            margin-top: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <span class="icon">&#9776;</span>L.A.M.E.
        </div>
        <h1 class="h4 text-center mb-3">Welcome to L.A.M.E.</h1>
        <p class="text-center text-muted mb-4">Please log in to manage your library account.</p>
        <form method="post" autocomplete="off" novalidate>
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <input type="text" name="student_id" id="student_id" class="form-control" placeholder="Enter your Student ID" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Log In</button>
            <?php if ($error): ?>
              <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>
    </div>
    <!-- Bootstrap JS Bundle CDN (for components if used) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
