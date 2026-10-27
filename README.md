# BorrowD

BorrowD is a book lending web application. Users browse the library catalogue, borrow and return books and review the books they have read, while admins manage the catalogue, loans and users.

## Technology stack
Frontend: HTML, CSS, JavaScript, jQuery, Bootstrap 5 (single-page application)
Backend: PHP, FlightPHP (REST API), PDO, MySQL

## Roles
- **Admin**: full management of all entities
- **User**: browses books, borrows and returns books, writes reviews

## Entities and relationships
- **Users**: have many loans and many reviews
- **Authors**: have many books
- **Categories**: have many books
- **Books**: belong to one author and one category; have many loans and reviews
- **Loans**: connect a user and a book (borrowed on, due date, returned on)
- **Reviews**: a user's rating and comment for a book