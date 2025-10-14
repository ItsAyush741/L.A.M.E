<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}

$type = $_GET['type'] ?? '';
if (!in_array($type, ['borrow', 'return'])) {
    header('Location: homepage.php');
    exit;
}
$host = 'localhost';
$db = 'l.a.m.e';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_title = trim($_POST['book_title'] ?? '');
    if ($type === 'borrow') {
        $stmt = $conn->prepare("UPDATE book SET borrowed_by=?, due_date=DATE_ADD(CURDATE(), INTERVAL 14 DAY) WHERE title=? AND borrowed_by IS NULL");
        $stmt->bind_param('ss', $_SESSION['student_id'], $book_title);
        $stmt->execute();
        $message = $stmt->affected_rows ? "Book borrowed!" : "Book unavailable.";
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE book SET borrowed_by=NULL, due_date=NULL WHERE title=? AND borrowed_by=?");
        $stmt->bind_param('ss', $book_title, $_SESSION['student_id']);
        $stmt->execute();
        $message = $stmt->affected_rows ? "Book returned!" : "Book not found in borrowed list.";
        $stmt->close();
    }
}
$conn->close();
?>
<!-- Place your transaction HTML/CSS here, showing $message if set-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>L.A.M.E. - <?php echo ucfirst($type); ?> Book</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7fafc; margin: 0; }
        .container {
            max-width: 450px;
            background: #ffffff;
            margin: 5rem auto;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 2px 18px rgba(59,130,246,0.08);
            text-align: center;
        }
        h1 {
            margin-bottom: 1em;
            font-weight: 600;
            color: #222;
        }
        label {
            display: block;
            margin: 1em 0 0.5em 0;
            text-align: left;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.75em 1em;
            border-radius: 10px;
            border: 1px solid #c9d6e6;
            font-size: 1rem;
            box-sizing: border-box;
        }
        button {
            margin-top: 1.5rem;
            padding: 0.75em 2.5em;
            background: #1798d5;
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s;
        }
        button:hover {
            background: #117ac4;
        }
        .message {
            margin-top: 1.5rem;
            font-weight: 500;
            color: #166faa;
        }
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e7eaf0;
            padding: 0.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
            margin: auto;
            margin-top: 2rem;
        }
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 600;
            margin-left: 2.5rem;
        }
        .icon {
            font-size: 1.25em;
            margin-right: 0.7em;
        }
        .brand {
            letter-spacing: 0.03em;
        }
        .nav-links {
            list-style: none;
            display: flex;
            margin: 0 2.5rem 0 0;
            gap: 2.1em;
        }
        .nav-links li a {
            text-decoration: none;
            color: #222;
            font-size: 1rem;
            font-weight: 500;
            transition: color 0.18s;
        }
        .nav-links li a:hover {
            color: #166faa;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <span class="icon">&#9776;</span>
            <span class="brand">L.A.M.E.</span>
        </div>
        <ul class="nav-links">
            <li><a href="homepage.php">Home</a></li>
            <li><a href="#">Catalog</a></li>
            <li><a href="#">My Account</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1><?php echo ucfirst($type); ?> Book</h1>
        <form method="post" autocomplete="off">
            <label for="book_title">Book Title</label>
            <input type="text" name="book_title" id="book_title" required>
            <button type="submit"><?php echo ucfirst($type); ?></button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
