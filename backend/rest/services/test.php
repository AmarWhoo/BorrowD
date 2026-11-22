<?php
/**
 * Tries every business rule against the real database (sample data from database/library.sql).
 * Every broken rule must end in an exception with a clear message; valid actions must print OK.
 * Everything the test creates is deleted at the end, so the script can be run again.
 *
 * Run from the backend folder:  php rest/services/test.php
 */
require_once __DIR__ . '/AuthorService.php';
require_once __DIR__ . '/CategoryService.php';
require_once __DIR__ . '/BookService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/LoanService.php';
require_once __DIR__ . '/ReviewService.php';

/** Run one action and print OK or the exception it threw. */
function attempt($description, $action)
{
    echo str_pad($description, 50, '.') . ' ';
    try {
        $result = $action();
        echo "OK" . (isset($result['id']) ? " (id {$result['id']})" : '') . "\n";
        return $result;
    } catch (HttpException $e) {
        echo get_class($e) . " [{$e->get_status()}]: {$e->getMessage()}\n";
    }
}

$authors    = new AuthorService();
$categories = new CategoryService();
$books      = new BookService();
$users      = new UserService();
$loans      = new LoanService();
$reviews    = new ReviewService();

echo "=== Authors ===\n";
attempt('Author without a name',               fn() => $authors->add(['nationality' => 'Bosnian']));
attempt('Author born in the future',           fn() => $authors->add(['name' => 'Future Writer', 'birth_year' => 2099]));
attempt('Delete Ivo Andrić (has books)',       fn() => $authors->delete(2));
attempt('Get author #999',                     fn() => $authors->get_by_id(999));
$author = attempt('Valid author',              fn() => $authors->add(['name' => 'Test Author', 'birth_year' => '', 'nationality' => 'Bosnian']));

echo "\n=== Categories ===\n";
attempt('Category without a name',             fn() => $categories->add(['description' => 'x']));
attempt('Duplicate category "Novel"',          fn() => $categories->add(['name' => 'Novel']));
attempt('Update "Novel" keeping its name',     fn() => $categories->update(['name' => 'Novel', 'description' => 'Classic and modern novels'], 1));
attempt('Delete "Novel" (has books)',          fn() => $categories->delete(1));
$category = attempt('Valid category',          fn() => $categories->add(['name' => 'Test category', 'description' => '']));

echo "\n=== Books ===\n";
attempt('Book without a title',                fn() => $books->add(['author_id' => $author['id'], 'category_id' => $category['id']]));
attempt('Book with an unknown author',         fn() => $books->add(['title' => 'X', 'author_id' => 999, 'category_id' => $category['id']]));
attempt('Book with an unknown category',       fn() => $books->add(['title' => 'X', 'author_id' => $author['id'], 'category_id' => 999]));
attempt('Book published in the future',        fn() => $books->add(['title' => 'X', 'author_id' => $author['id'], 'category_id' => $category['id'], 'published_year' => 2099]));
attempt('Book with an invalid ISBN',           fn() => $books->add(['title' => 'X', 'author_id' => $author['id'], 'category_id' => $category['id'], 'isbn' => '12345']));
attempt('Duplicate ISBN of 1984 (with dashes)', fn() => $books->add(['title' => 'X', 'author_id' => $author['id'], 'category_id' => $category['id'], 'isbn' => '978-0-451-52493-5']));
attempt('Delete 1984 (has loans)',             fn() => $books->delete(3));
$book = attempt('Valid book',                  fn() => $books->add(['title' => 'Test Book', 'author_id' => $author['id'], 'category_id' => $category['id'], 'isbn' => '000-0-000-00000-1', 'published_year' => 2026]));

echo "\n=== Users ===\n";
attempt('User with an invalid e-mail',         fn() => $users->add(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@', 'password' => 'secret123']));
attempt('Duplicate e-mail (AMAR@library.ba)',  fn() => $users->add(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'AMAR@library.ba', 'password' => 'secret123']));
attempt('Weak password',                       fn() => $users->add(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@library.ba', 'password' => '123']));
attempt('Unknown role',                        fn() => $users->add(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@library.ba', 'password' => 'secret123', 'role' => 'boss']));
attempt('Delete Amar (has loans and reviews)', fn() => $users->delete(2));
$user = attempt('Valid user',                  fn() => $users->add(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'Test@Library.ba', 'password' => 'secret123']));
echo "    password hash in the response? " . (isset($user['password']) ? "YES – WRONG" : "no – correct") . "\n";

echo "\n=== Loans ===\n";
attempt('Borrow: unknown user',                fn() => $loans->borrow(999, $book['id']));
attempt('Borrow: unknown book',                fn() => $loans->borrow($user['id'], 999));
attempt('Borrow 1984 (already on loan)',       fn() => $loans->borrow($user['id'], 3));
attempt('Amar borrows (has an overdue loan)',  fn() => $loans->borrow(2, 2));
$loan = attempt('Test user borrows Test Book', fn() => $loans->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'due_date' => '2099-01-01']));
echo "    due_date set by the server: {$loan['due_date']}\n";
attempt('Delete the active loan',              fn() => $loans->delete($loan['id']));
attempt('Due date before the borrow date',     fn() => $loans->update(['due_date' => '2000-01-01'], $loan['id']));
attempt('Return the loan',                     fn() => $loans->return_loan($loan['id']));
attempt('Return the same loan again',          fn() => $loans->return_loan($loan['id']));

echo "\n=== Reviews ===\n";
attempt('Review with rating 7',                fn() => $reviews->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'rating' => 7]));
attempt('Review of a book never borrowed',     fn() => $reviews->add(['user_id' => $user['id'], 'book_id' => 2, 'rating' => 4]));
attempt('Amar reviews book 1 again',           fn() => $reviews->add(['user_id' => 2, 'book_id' => 1, 'rating' => 3]));
$review = attempt('Valid review',              fn() => $reviews->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'rating' => '4', 'comment' => 'Test review']));
attempt('Second review of the same book',      fn() => $reviews->add(['user_id' => $user['id'], 'book_id' => $book['id'], 'rating' => 5]));
attempt('Edit review: rating 5',               fn() => $reviews->update(['rating' => 5], $review['id']));

echo "\n=== Clean up (children first) ===\n";
attempt('Delete the test book (has a loan)',   fn() => $books->delete($book['id']));
attempt('Delete the test review',              fn() => $reviews->delete($review['id']));
attempt('Delete the returned loan',            fn() => $loans->delete($loan['id']));
attempt('Delete the test book',                fn() => $books->delete($book['id']));
attempt('Delete the test user',                fn() => $users->delete($user['id']));
attempt('Delete the test category',            fn() => $categories->delete($category['id']));
attempt('Delete the test author',              fn() => $authors->delete($author['id']));