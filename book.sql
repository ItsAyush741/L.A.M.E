CREATE TABLE book (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    due_date DATE,
    borrowed_by VARCHAR(20),  -- student_id of the borrower
    FOREIGN KEY (borrowed_by) REFERENCES student(student_id)
);
