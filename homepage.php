<?php
session_start();
if (!isset($_SESSION['student_id'], $_SESSION['name'])) {
    header('Location: login.php');
    exit;
}
$host = 'localhost';
$db = 'l.a.m.e';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
$books = [];
$stmt = $conn->prepare("SELECT title, author, due_date FROM book WHERE borrowed_by=?");
$stmt->bind_param('s', $student_id);
$stmt->execute();
$stmt->bind_result($title, $author, $due_date);
while ($stmt->fetch()) {
    $books[] = ['title'=>$title, 'author'=>$author, 'due_date'=>$due_date];
}
$stmt->close(); 
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>L.A.M.E. Homepage</title>
    <style>
        body { margin:0; font-family: 'Inter', Arial, sans-serif; background:#f7fafc; color:#222; }
        .navbar { background:#fff; border-bottom:1px solid #e7eaf0; padding:0.5rem 0; display:flex; justify-content:space-between; align-items:center;}
        .logo { display:flex; align-items:center; font-size:1.3rem; font-weight:600; margin-left:2.5rem;}
        .icon { font-size:1.25em; margin-right:0.7em;}
        .brand { letter-spacing:0.03em;}
        .nav-links { list-style:none; display:flex; margin:0 2.5rem 0 0; gap:2.1em;}
        .nav-links li a { text-decoration:none; color:#222; font-size:1rem; font-weight:500; transition: color 0.18s;}
        .nav-links li a:hover { color:#166faa;}
        .container { max-width:900px; margin:3.2rem auto 0 auto; padding:0 3rem;}
        h1 { font-size:2.4rem; font-weight:700; margin-bottom:0.2rem; margin-top:0;}
        .student-id { color:#6db1de; font-size:1rem; margin-bottom:2.1rem;}
        h2 { font-size:1.4rem; font-weight:700; margin-bottom:1.1rem;}
        .books-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; margin-bottom:2.2rem; box-shadow:0 2px 10px rgba(30,64,175,0.03);}
        .books-table th, .books-table td { padding:1.1em 1.2em; text-align:left;}
        .books-table thead tr { background:#f5f8fb;}
        .books-table tbody tr { border-bottom:1px solid #e5ebf0;}
        .books-table tbody tr:last-child { border-bottom:none;}
        .author-link { color:#60a6d9; cursor:pointer;}
        .action-buttons { display:flex; gap:1rem;}
        .borrow-btn { background:#1798d5; color:#fff; border:none; padding:0.75em 2em; font-size:1em; border-radius:7px; cursor:pointer; font-weight:600; transition:background 0.13s;}
        .borrow-btn:hover { background:#117ac4;}
        .return-btn { background:#e4e9eb; color:#1a1a1a; border:none; padding:0.75em 2em; font-size:1em; border-radius:7px; cursor:pointer; font-weight:600; transition:background 0.13s;}
        .return-btn:hover { background:#c5d2da;}
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <span class="icon">&#9776;</span>
        <span class="brand">L.A.M.E.</span>
    </div>
    <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="#">Catalog</a></li>
        <li><a href="#">My Account</a></li>
    </ul>
</nav>
<main class="container">
    <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
    <p class="student-id">Student ID: <span><?php echo htmlspecialchars($student_id); ?></span></p>
    <h2>Currently Borrowed Books</h2>
    <table class="books-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Due Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book["title"]); ?></td>
                    <td class="author-link"><?php echo htmlspecialchars($book["author"]); ?></td>
                    <td><?php echo htmlspecialchars($book["due_date"]); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No books borrowed.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="action-buttons">
        <form action="transaction.php" method="get" style="display:inline;">
            <input type="hidden" name="type" value="borrow">
            <button class="borrow-btn" type="submit">Borrow Book</button>
        </form>
        <form action="transaction.php" method="get" style="display:inline;">
            <input type="hidden" name="type" value="return">
            <button class="return-btn" type="submit">Return Book</button>
        </form>
    </div>
</main>
</body>
</html>
