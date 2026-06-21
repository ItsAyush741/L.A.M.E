-- book.sql
-- Run this SECOND (after student.sql, before transaction.sql)
-- FIX: Column was previously named 'title' — renamed to 'bookname' to match all PHP files.
-- The old 'due_date' and 'borrowed_by' columns are removed; borrow status is tracked via the transaction table.

CREATE TABLE IF NOT EXISTS book (
    book_id  INT AUTO_INCREMENT PRIMARY KEY,
    bookname VARCHAR(255) NOT NULL,
    author   VARCHAR(100) NOT NULL
);

-- Sample books
INSERT IGNORE INTO book (book_id, bookname, author) VALUES
    (1, 'The Great Gatsby',        'F. Scott Fitzgerald'),
    (2, 'To Kill a Mockingbird',   'Harper Lee'),
    (3, '1984',                    'George Orwell'),
    (4, 'Pride and Prejudice',     'Jane Austen'),
    (5, 'The Catcher in the Rye',  'J.D. Salinger');
