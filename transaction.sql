-- transaction.sql
-- Run this LAST (after student.sql and book.sql)
-- This table was missing entirely — it is required by transaction.php, homepage.php, and catalog.php.

CREATE TABLE IF NOT EXISTS `transaction` (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id     VARCHAR(20)  NOT NULL,
    student_name   VARCHAR(100) NOT NULL,
    book_id        INT          NOT NULL,
    bookname       VARCHAR(255) NOT NULL,
    date_borrowed  DATE         NOT NULL,
    date_returned  DATE         DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (book_id)    REFERENCES book(book_id)
);
