<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$books    = [];
$db_error = '';

$conn = get_db_connection();
$sql  = "SELECT b.book_id, b.bookname, b.author,
         (SELECT COUNT(*) FROM `transaction` t
          WHERE t.book_id = b.book_id AND t.date_returned IS NULL) AS is_borrowed
         FROM book b
         ORDER BY b.bookname ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    $db_error = 'Could not load the catalog. Please try again later.';
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>L.A.M.E. – Book Catalog</title>
    <meta name="description" content="Browse all books in the L.A.M.E. library catalog and see their availability." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background: #f7fafc; font-family: 'Inter', Arial, sans-serif; }
        .navbar-brand { font-weight: 700; font-size: 1.3rem; }
        .catalog-table th, .catalog-table td { vertical-align: middle; }
        .badge { font-size: 1em; }
        .search-box { max-width: 350px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="homepage.php"><span>&#9776;</span> L.A.M.E.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto fw-semibold">
        <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="catalog.php">Catalog</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h1 class="fw-bold mb-4">Book Catalog</h1>

    <?php if ($db_error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($db_error); ?></div>
    <?php else: ?>
    <input class="form-control search-box mb-4" id="bookSearch" type="text"
           placeholder="Search by title or author..." onkeyup="filterTable()" />

    <div class="table-responsive">
        <table class="table table-hover catalog-table" id="booksTable">
            <thead>
                <tr>
                    <th scope="col">Book ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                <tr><td colspan="4" class="text-muted text-center">No books found in catalog.</td></tr>
                <?php else: ?>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                    <td><?php echo htmlspecialchars($book['bookname']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td>
                        <?php if ($book['is_borrowed'] == 0): ?>
                            <span class="badge bg-success">Available</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Borrowed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterTable() {
    var filter = document.getElementById('bookSearch').value.toLowerCase();
    var rows   = document.querySelectorAll('#booksTable tbody tr');
    rows.forEach(function(row) {
        if (row.cells.length < 3) return;
        var title  = row.cells[1].textContent.toLowerCase();
        var author = row.cells[2].textContent.toLowerCase();
        row.style.display = (title.includes(filter) || author.includes(filter)) ? '' : 'none';
    });
}
</script>
</body>
</html>
