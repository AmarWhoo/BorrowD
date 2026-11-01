<?php
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/AuthorDao.php';
require_once __DIR__ . '/CategoryDao.php';
require_once __DIR__ . '/BookDao.php';
require_once __DIR__ . '/LoanDao.php';
require_once __DIR__ . '/ReviewDao.php';

$userDao     = new UserDao();
$authorDao   = new AuthorDao();
$categoryDao = new CategoryDao();
$bookDao     = new BookDao();
$loanDao     = new LoanDao();
$reviewDao   = new ReviewDao();

// ---------------------------------------------------------------
// READ – get_all() for every table (sample data: 3, 5, 4, 6, 4, 3)
// ---------------------------------------------------------------
echo "=== READ: get_all() ===\n";
echo "Users: "      . count($userDao->get_all())     . "\n";
echo "Authors: "    . count($authorDao->get_all())   . "\n";
echo "Categories: " . count($categoryDao->get_all()) . "\n";
echo "Books: "      . count($bookDao->get_all())     . "\n";
echo "Loans: "      . count($loanDao->get_all())     . "\n";
echo "Reviews: "    . count($reviewDao->get_all())   . "\n";

// ---------------------------------------------------------------
// READ – get_by_id() and the special queries
// ---------------------------------------------------------------
echo "\n=== READ: get_by_id() ===\n";
print_r($authorDao->get_by_id(1));
print_r($categoryDao->get_by_id(1));
print_r($loanDao->get_by_id(1));
print_r($reviewDao->get_by_id(1));

echo "\n=== UserDao: get_by_id() – must NOT contain password ===\n";
print_r($userDao->get_by_id(2));

echo "\n=== UserDao: get_by_email() – must contain password (for login) ===\n";
print_r($userDao->get_by_email('amar@library.ba'));

echo "\n=== BookDao: get_by_id() – must contain author_name and category_name ===\n";
print_r($bookDao->get_by_id(1));

echo "\n=== LoanDao: get_active_by_book() ===\n";
echo "Book 3 (on loan, expect a row):\n";
print_r($loanDao->get_active_by_book(3));
echo "Book 2 (available, expect NULL):\n";
var_dump($loanDao->get_active_by_book(2));

// ---------------------------------------------------------------
// CREATE – one new row in every table.
// "hacker" is NOT a column, so only_columns() must drop it.
// ---------------------------------------------------------------
echo "\n=== CREATE: add() ===\n";
$author = $authorDao->add(['name' => 'Test Author', 'birth_year' => 1990, 'nationality' => 'Bosnian']);
print_r($author);

$category = $categoryDao->add(['name' => 'Test category', 'description' => 'Created by test.php', 'hacker' => 'DROP TABLE']);
print_r($category);

$book = $bookDao->add([
    'title'          => 'Test Book',
    'author_id'      => $author['id'],
    'category_id'    => $category['id'],
    'isbn'           => '0000000000001',
    'published_year' => 2026,
    'description'    => 'Created by test.php'
]);
print_r($book);

$user = $userDao->add([
    'first_name' => 'Test',
    'last_name'  => 'User',
    'email'      => 'test@library.ba',
    'password'   => password_hash('test123', PASSWORD_BCRYPT),
    'role'       => 'user'
]);
print_r($user);

$loan = $loanDao->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'borrowed_on' => '2026-11-01', 'due_date' => '2026-11-15']);
print_r($loan);

$review = $reviewDao->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'rating' => 4, 'comment' => 'Created by test.php']);
print_r($review);

// ---------------------------------------------------------------
// UPDATE – change one column in every new row (each line should print 1)
// ---------------------------------------------------------------
echo "\n=== UPDATE: update() – rows changed ===\n";
echo "Author: "   . $authorDao->update(['nationality' => 'Croatian'], $author['id'])                  . "\n";
echo "Category: " . $categoryDao->update(['description' => 'Updated description'], $category['id'])  . "\n";
echo "Book: "     . $bookDao->update(['title' => 'Test Book (updated)'], $book['id'])                . "\n";
echo "User: "     . $userDao->update(['last_name' => 'Updated'], $user['id'])                        . "\n";
echo "Loan: "     . $loanDao->update(['returned_on' => '2026-11-10'], $loan['id'])                   . "\n";
echo "Review: "   . $reviewDao->update(['rating' => 5], $review['id'])                               . "\n";

print_r($bookDao->get_by_id($book['id']));

// ---------------------------------------------------------------
// DELETE – children first (review, loan), then parents,
// otherwise MySQL refuses with error 1451. Afterwards every
// get_by_id() must return NULL.
// ---------------------------------------------------------------
echo "\n=== DELETE: delete() ===\n";
$reviewDao->delete($review['id']);
$loanDao->delete($loan['id']);
$bookDao->delete($book['id']);
$userDao->delete($user['id']);
$categoryDao->delete($category['id']);
$authorDao->delete($author['id']);

var_dump($reviewDao->get_by_id($review['id']));
var_dump($loanDao->get_by_id($loan['id']));
var_dump($bookDao->get_by_id($book['id']));
var_dump($userDao->get_by_id($user['id']));
var_dump($categoryDao->get_by_id($category['id']));
var_dump($authorDao->get_by_id($author['id']));