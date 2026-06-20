<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}

$host = "localhost";
$db = "l.a.m.e";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];

$books = [];
$sql = "SELECT t.transaction_id, t.book_id, b.bookname, b.author, t.date_borrowed
        FROM transaction t
        JOIN book b ON t.book_id = b.book_id
        WHERE t.student_id=? AND t.date_returned IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($transaction_id, $book_id, $bookname, $author, $date_borrowed);
while ($stmt->fetch()) {
    $books[] = [
        'transaction_id' => $transaction_id,
        'book_id' => $book_id,
        'bookname' => $bookname,
        'author' => $author,
        'date_borrowed' => $date_borrowed,
    ];
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand fs-3 fw-bold" href="#">L.A.M.E.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">My Account</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
  <p class="text-secondary">Student ID: <?php echo htmlspecialchars($student_id); ?></p>
  <h3 class="mt-4 mb-3">Currently Borrowed Books</h3>

  <?php if (!empty($books)): ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Book Name</th>
          <th>Author</th>
          <th>Date Borrowed</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($books as $book): ?>
          <tr>
            <td><?php echo htmlspecialchars($book['bookname']); ?></td>
            <td><?php echo htmlspecialchars($book['author']); ?></td>
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

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
