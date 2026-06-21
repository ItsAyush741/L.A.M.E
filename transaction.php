<?php
// FIX: Was '<<?' (double less-than) on the original line 1 — caused fatal PHP parse error.
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$student_id = $_SESSION['student_id'];
$name       = $_SESSION['name'];
$type       = $_GET['type'] ?? '';
$message    = '';
$msg_class  = 'info';  // Bootstrap alert color class

// Validate type parameter
if (!in_array($type, ['borrow', 'return'], true)) {
    header('Location: homepage.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['book_title'] ?? '');

    if ($input === '') {
        $message   = 'Please enter a Book ID or Book Name.';
        $msg_class = 'warning';
    } else {
        $conn = get_db_connection();

        // Determine if input is a numeric ID or a book name
        if (ctype_digit($input)) {
            $stmt = $conn->prepare("SELECT book_id, bookname FROM book WHERE book_id = ?");
            $stmt->bind_param('i', $input);
        } else {
            $stmt = $conn->prepare("SELECT book_id, bookname FROM book WHERE bookname = ?");
            $stmt->bind_param('s', $input);
        }

        if (!$stmt) {
            $message   = 'Database error. Please try again later.';
            $msg_class = 'danger';
        } else {
            $stmt->execute();
            $stmt->bind_result($book_id, $bookname);
            $found = $stmt->fetch();
            $stmt->close();

            if (!$found) {
                $message   = 'No book found with that ID or Name.';
                $msg_class = 'warning';
            } elseif ($type === 'borrow') {
                // Check if book is already borrowed
                $check = $conn->prepare(
                    "SELECT COUNT(*) FROM `transaction` WHERE book_id = ? AND date_returned IS NULL"
                );
                $check->bind_param('i', $book_id);
                $check->execute();
                $check->bind_result($count);
                $check->fetch();
                $check->close();

                if ($count > 0) {
                    $message   = 'Sorry, "' . htmlspecialchars($bookname) . '" is currently not available.';
                    $msg_class = 'danger';
                } else {
                    $ins = $conn->prepare(
                        "INSERT INTO `transaction` (student_id, student_name, book_id, bookname, date_borrowed)
                         VALUES (?, ?, ?, ?, CURDATE())"
                    );
                    $ins->bind_param('ssis', $student_id, $name, $book_id, $bookname);
                    if ($ins->execute()) {
                        $message   = '✓ "' . htmlspecialchars($bookname) . '" borrowed successfully!';
                        $msg_class = 'success';
                    } else {
                        $message   = 'Failed to record the borrow. Please try again.';
                        $msg_class = 'danger';
                    }
                    $ins->close();
                }
            } else {
                // Return
                $upd = $conn->prepare(
                    "UPDATE `transaction` SET date_returned = CURDATE()
                     WHERE student_id = ? AND book_id = ? AND date_returned IS NULL"
                );
                $upd->bind_param('si', $student_id, $book_id);
                $upd->execute();
                if ($upd->affected_rows > 0) {
                    $message   = '✓ "' . htmlspecialchars($bookname) . '" returned successfully!';
                    $msg_class = 'success';
                } else {
                    $message   = 'You have not borrowed this book, or it has already been returned.';
                    $msg_class = 'warning';
                }
                $upd->close();
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. – <?php echo ucfirst($type); ?> Book</title>
    <meta name="description" content="<?php echo ucfirst($type); ?> a book from the L.A.M.E. library." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background: #f7fafc; font-family: 'Inter', Arial, sans-serif; }
        .navbar-brand { font-weight: 700; font-size: 1.3rem; }
        .main-card {
            max-width: 480px;
            margin: 48px auto;
            padding: 2.5rem 2.2rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 18px rgba(59, 130, 246, 0.08);
        }
        label { font-weight: 600; margin-bottom: .5rem; }
        .btn-primary { background: #1798d5; border: none; }
        .btn-primary:hover { background: #117ac4; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container">
        <a class="navbar-brand" href="homepage.php">&#9776; L.A.M.E.</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto fw-semibold">
            <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="main-card shadow-sm">
        <h1 class="h3 mb-4 fw-bold"><?php echo ucfirst($type); ?> Book</h1>
        <form method="post" autocomplete="off" novalidate>
            <div class="mb-3">
                <label for="book_title" class="form-label">Book ID or Book Name</label>
                <input type="text" name="book_title" id="book_title" class="form-control"
                       placeholder="Enter Book ID or Book Name" required
                       value="<?php echo htmlspecialchars($_POST['book_title'] ?? ''); ?>" />
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <?php echo ucfirst($type); ?>
            </button>
        </form>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $msg_class; ?> mt-3 mb-0">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="homepage.php" class="text-muted small">&larr; Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
