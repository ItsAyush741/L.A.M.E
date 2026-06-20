<<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) { header('Location: login.php'); exit; }
$host = "localhost"; $db = "l.a.m.e"; $user = "root"; $pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
$type = $_GET['type'] ?? '';
$message = '';

if (($type == 'borrow' || $type == 'return') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['book_title']);

    // Determine if input is ID (number) or name (string)
    if (ctype_digit($input)) { // treat as book_id
        $stmt = $conn->prepare("SELECT book_id, bookname FROM book WHERE book_id=?");
        $stmt->bind_param("i", $input);
    } else { // treat as bookname
        $stmt = $conn->prepare("SELECT book_id, bookname FROM book WHERE bookname=?");
        $stmt->bind_param("s", $input);
    }

    $stmt->execute();
    $stmt->bind_result($book_id, $bookname);
    if ($stmt->fetch()) {
        $stmt->close();

        if ($type == 'borrow') {
            // Check if book is already borrowed (no open transaction)
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM transaction WHERE book_id=? AND date_returned IS NULL");
            $check_stmt->bind_param("i", $book_id);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count == 0) {
                $borrow_stmt = $conn->prepare("INSERT INTO transaction (student_id, student_name, book_id, bookname, date_borrowed) VALUES (?, ?, ?, ?, CURDATE())");
                $borrow_stmt->bind_param("ssis", $student_id, $name, $book_id, $bookname);
                $borrow_stmt->execute();
                $borrow_stmt->close();
                $message = "Book borrowed successfully!";
            } else {
                $message = "Sorry, the book is currently not available.";
            }
        } else { // return
            $return_stmt = $conn->prepare("UPDATE transaction SET date_returned=CURDATE() WHERE student_id=? AND book_id=? AND date_returned IS NULL");
            $return_stmt->bind_param("si", $student_id, $book_id);
            $return_stmt->execute();
            if ($return_stmt->affected_rows) {
                $message = "Book returned successfully!";
            } else {
                $message = "You have not borrowed this book or it is already returned.";
            }
            $return_stmt->close();
        }
    } else {
        $message = "No book found with that ID or name.";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. - <?php echo ucfirst($type); ?> Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f7fafc;
            font-family: 'Inter', Arial, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        .main-card {
            max-width: 480px;
            margin: 48px auto;
            padding: 2.5rem 2.2rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 18px rgba(59, 130, 246, 0.08);
        }
        label {
            font-weight: 600;
            margin-bottom: .5rem;
        }
        .btn-primary {
            background: #1798d5;
            border: none;
        }
        .btn-primary:hover {
            background: #117ac4;
        }
        .message {
            margin-top: 1.2rem;
            font-weight: 600;
            color: #166faa;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
        <a class="navbar-brand" href="#"><span class="icon">&#9776;</span> L.A.M.E.</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto fw-semibold">
            <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
             <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
            <li class="nav-item"><a class="nav-link" href="#">My Account</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- MAIN CARD CENTERED -->
    <div class="main-card shadow-sm">
        <h1 class="h3 mb-4 fw-bold"><?php echo ucfirst($type); ?> Book</h1>
        <form method="post" autocomplete="off" novalidate>
            <div class="mb-3">
                <label for="book_title" class="form-label">Book ID or Book Name</label>
                <input type="text" name="book_title" id="book_title" class="form-control" placeholder="Enter Book ID or Book Name" required />
            </div>
            <button type="submit" class="btn btn-primary w-100"><?php echo ucfirst($type); ?></button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
