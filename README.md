# L.A.M.E. — Library Automation Management Entity

A PHP/MySQL web application for managing student library accounts. Students can log in, browse the book catalog, borrow books, and return them.

---

## Tech Stack

| Layer    | Technology                       |
|----------|----------------------------------|
| Backend  | PHP 8+ (procedural, MySQLi)      |
| Database | MySQL / MariaDB                  |
| Frontend | HTML5, Bootstrap 5.3, vanilla JS |
| Server   | Apache (via XAMPP / WAMP / LAMP) |

---

## Project Structure

```
L.A.M.E/
├── db.php            # ← Centralized DB credentials (edit this to change DB settings)
├── login.php         # Login page
├── logout.php        # Session destroy + redirect
├── homepage.php      # Student dashboard — shows currently borrowed books
├── catalog.php       # Full book catalog with availability status
├── transaction.php   # Borrow / Return book form
├── login.css         # Shared stylesheet (legacy static mockup)
├── login.html        # Static UI mockup (not used in production flow)
├── student.sql       # Schema + sample data for `student` table   — import FIRST
├── book.sql          # Schema + sample data for `book` table      — import SECOND
└── transaction.sql   # Schema for `transaction` table             — import THIRD
```

---

## Setup Instructions

### Prerequisites
- **For Online Deployment (Cloud):** A [Railway](https://railway.app/) account.
- **For Local Development (Optional):** XAMPP (or WAMP / LAMP / any PHP+MySQL stack), PHP 8.0+, MySQL 5.7+ / MariaDB 10.4+.

---

### Option A — Cloud Deployment (Railway)

#### Step 1 — Deploy on Railway
1. Push your repository to GitHub.
2. Log into [Railway](https://railway.app/) and create a new project.
3. Select **Deploy from GitHub repo** and choose your repository.
4. Railway will automatically build and deploy your PHP application.

#### Step 2 — Add a MySQL Database on Railway
1. In your Railway project dashboard, click **+ New** and select **Database > Add MySQL**.
2. Wait for the database service to provision.

#### Step 3 — Link Environment Variables
In your web service settings on Railway, navigate to the **Variables** tab and reference the MySQL variables:
- `MYSQLHOST` = `${{MySQL.MYSQLHOST}}`
- `MYSQLPORT` = `${{MySQL.MYSQLPORT}}`
- `MYSQLUSER` = `${{MySQL.MYSQLUSER}}`
- `MYSQLPASSWORD` = `${{MySQL.MYSQLPASSWORD}}`
- `MYSQLDATABASE` = `${{MySQL.MYSQLDATABASE}}`

#### Step 4 — Import SQL Files into Railway MySQL
You can import the database schema and sample data into your Railway MySQL database using a terminal (replace bracketed terms with credentials found under the **Variables** tab of your Railway MySQL service):
```bash
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < student.sql
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < book.sql
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < transaction.sql
```
*(Alternatively, connect using a database manager like DBeaver, TablePlus, or VS Code Database Client to run the SQL files on the remote database.)*

---

### Option B — Local Setup (XAMPP)

#### Step 1 — Start Your Server
Launch XAMPP Control Panel and start **Apache** and **MySQL**.

#### Step 2 — Create the Database
1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **New** and create a database named exactly: `l.a.m.e`
   > ⚠️ The name must include the dots — it's `l.a.m.e`, not `lame`.

#### Step 3 — Import SQL Files (in order)
In phpMyAdmin, select the `l.a.m.e` database, go to the **Import** tab, and import each file **in this order**:
1. `student.sql`
2. `book.sql`
3. `transaction.sql`

#### Step 4 — Copy Files to Web Root
Copy the entire `L.A.M.E/` folder into your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\LAME\
```

#### Step 5 — Configure Database Credentials (if needed)
Open `db.php` and update the constants if your MySQL setup is different:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'l.a.m.e');
define('DB_USER', 'root');
define('DB_PASS', '');        // ← add your MySQL password here
```

#### Step 6 — Open the App
Navigate to: `http://localhost/LAME/login.php`

---

## Default Login Credentials

| Student ID | Password | Name          |
|------------|----------|---------------|
| S001       | pass123  | Alice Johnson |
| S002       | pass456  | Bob Smith     |

> ⚠️ **Security Note:** Passwords are stored as plain text for development convenience only.  
> In production, use `password_hash()` when inserting and `password_verify()` when checking.

---

## Features

- ✅ Student login / logout with session management
- ✅ Dashboard showing currently borrowed books
- ✅ Full book catalog with real-time availability status
- ✅ Live search/filter on the catalog page
- ✅ Borrow a book by ID or exact name
- ✅ Return a book by ID or exact name
- ✅ Centralized database config (`db.php`)
- ✅ Error messages for invalid login, unavailable books, DB failures

---

## Known Limitations

- No admin panel (adding/removing books requires direct DB access)
- Passwords stored plain-text — hashing must be added before any production use
- Single copy per book (one active borrow per `book_id` at a time)
- No due-date or overdue tracking

---

## Database Schema

```
student          book              transaction
-----------      ----------        ---------------
student_id (PK)  book_id (PK AI)   transaction_id (PK AI)
name             bookname          student_id  → student.student_id
password         author            student_name
                                   book_id     → book.book_id
                                   bookname
                                   date_borrowed
                                   date_returned (NULL = still borrowed)
```
