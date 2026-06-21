<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$student_id = $_SESSION['student_id'];
$name       = $_SESSION['name'];

$books = [];
$db_error = '';

$conn = get_db_connection();
$sql  = "SELECT t.transaction_id, t.book_id, t.bookname, t.date_borrowed
         FROM `transaction` t
         WHERE t.student_id = ? AND t.date_returned IS NULL
         ORDER BY t.date_borrowed DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $stmt->bind_result($transaction_id, $book_id, $bookname, $date_borrowed);
    while ($stmt->fetch()) {
        $books[] = [
            'transaction_id' => $transaction_id,
            'book_id'        => $book_id,
            'bookname'       => $bookname,
            'date_borrowed'  => $date_borrowed,
        ];
    }
    $stmt->close();
} else {
    $db_error = 'Could not load borrowed books. Please try again later.';
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. – Home</title>
    <meta name="description" content="Your L.A.M.E. library dashboard — view your currently borrowed books." />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand fs-3 fw-bold" href="homepage.php">L.A.M.E.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent" aria-controls="navbarContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
        <li class="nav-item"><a class="nav-link text-danger fw-semibold" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
  <p class="text-secondary">Student ID: <?php echo htmlspecialchars($student_id); ?></p>
  <h3 class="mt-4 mb-3">Currently Borrowed Books</h3>

  <?php if ($db_error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($db_error); ?></div>
  <?php elseif (!empty($books)): ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Book Name</th>
          <th>Date Borrowed</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($books as $book): ?>
          <tr>
            <td><?php echo htmlspecialchars($book['bookname']); ?></td>
            <td><?php echo htmlspecialchars($book['date_borrowed']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="fs-5 text-muted">You have not borrowed any books yet.</p>
  <?php endif; ?>

  <div class="d-flex gap-3 mt-4">
    <a href="transaction.php?type=borrow" class="btn btn-primary flex-grow-1">Borrow Book</a>
    <a href="transaction.php?type=return" class="btn btn-outline-secondary flex-grow-1">Return Book</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
