-- Library database for the Web Programming project (Milestone 2)
-- Import: mysql -u root -p < database/library.sql

CREATE DATABASE IF NOT EXISTS library;
USE library;

-- ---------------------------------------------------------------
-- Tables
-- ---------------------------------------------------------------

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(50)  NOT NULL,
    last_name     VARCHAR(50)  NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role          ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE authors (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    birth_year  INT,
    nationality VARCHAR(50)
);

CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE books (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(150) NOT NULL,
    author_id      INT NOT NULL,
    category_id    INT NOT NULL,
    isbn           VARCHAR(20) UNIQUE,
    published_year INT,
    description    TEXT,
    CONSTRAINT fk_books_author   FOREIGN KEY (author_id)   REFERENCES authors(id),
    CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Status is derived: active = returned_on IS NULL; overdue = active and due_date < today
CREATE TABLE loans (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT  NOT NULL,
    book_id     INT  NOT NULL,
    borrowed_on DATE NOT NULL,
    due_date    DATE NOT NULL,
    returned_on DATE NULL COMMENT 'NULL while the loan is active',
    CONSTRAINT fk_loans_user  FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_loans_book  FOREIGN KEY (book_id) REFERENCES books(id),
    CONSTRAINT chk_loans_due      CHECK (due_date >= borrowed_on),
    CONSTRAINT chk_loans_returned CHECK (returned_on IS NULL OR returned_on >= borrowed_on)
);

CREATE TABLE reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT     NOT NULL,
    book_id    INT     NOT NULL,
    rating     TINYINT NOT NULL,
    comment    TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user   FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_reviews_book   FOREIGN KEY (book_id) REFERENCES books(id),
    CONSTRAINT uq_reviews_user_book UNIQUE (user_id, book_id),
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
);

-- ---------------------------------------------------------------
-- Sample data
-- Test accounts:
--   admin@library.ba  / admin123  (admin)
--   amar@library.ba   / user123   (user)
--   lejla@library.ba  / user123   (user)
-- ---------------------------------------------------------------

INSERT INTO users (first_name, last_name, email, password, role) VALUES
('Admin', 'Library', 'admin@library.ba', '$2y$10$muOvvaM.R9bp7Rh0iNu3P.0WwlBCMkjjs8WNrqM7O5tJrkdoyqJNS', 'admin'),
('Amar',  'Hodžić',  'amar@library.ba',  '$2y$10$/9kXYXiaMiEm7gDx/cuQEufGPLOLxOFCmVbkwxByyq2SS9vc.ucES', 'user'),
('Lejla', 'Kovačević', 'lejla@library.ba', '$2y$10$/9kXYXiaMiEm7gDx/cuQEufGPLOLxOFCmVbkwxByyq2SS9vc.ucES', 'user');

INSERT INTO authors (name, birth_year, nationality) VALUES
('Meša Selimović',   1910, 'Bosnian'),
('Ivo Andrić',       1892, 'Yugoslav'),
('George Orwell',    1903, 'British'),
('Agatha Christie',  1890, 'British'),
('Robert C. Martin', 1952, 'American');

INSERT INTO categories (name, description) VALUES
('Novel',       'Classic and modern novels'),
('Dystopia',    'Stories about oppressive future societies'),
('Mystery',     'Crime and detective fiction'),
('Programming', 'Books about software development');

INSERT INTO books (title, author_id, category_id, isbn, published_year, description) VALUES
('Death and the Dervish',   1, 1, '9780810112971', 1966, 'A dervish questions his faith after his brother is arrested.'),
('The Bridge on the Drina', 2, 1, '9780226020457', 1945, 'The story of a bridge in Višegrad across four centuries.'),
('Nineteen Eighty-Four',    3, 2, '9780451524935', 1949, 'Life under total surveillance in Oceania.'),
('Animal Farm',             3, 2, '9780451526342', 1945, 'Farm animals take over and repeat the mistakes of their masters.'),
('Murder on the Orient Express', 4, 3, '9780062693662', 1934, 'Hercule Poirot solves a murder on a snowed-in train.'),
('Clean Code',              5, 4, '9780132350884', 2008, 'Principles for writing readable and maintainable code.');

INSERT INTO loans (user_id, book_id, borrowed_on, due_date, returned_on) VALUES
(2, 1, '2026-10-01', '2026-10-15', '2026-10-12'),  -- returned
(2, 3, '2026-10-20', '2026-11-03', NULL),          -- active
(3, 6, '2026-10-05', '2026-10-19', NULL),          -- overdue
(3, 5, '2026-09-10', '2026-09-24', '2026-09-22');  -- returned

INSERT INTO reviews (user_id, book_id, rating, comment) VALUES
(2, 1, 5, 'One of the best books I have ever read.'),
(3, 5, 4, 'Clever ending, I did not see it coming.'),
(3, 6, 3, 'Useful, but some advice feels dated.');