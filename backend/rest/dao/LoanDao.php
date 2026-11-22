<?php
require_once __DIR__ . '/BaseDao.php';

class LoanDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('loans', ['user_id', 'book_id', 'borrowed_on', 'due_date', 'returned_on']);
    }

    /** The loan that is still open for this book, or null if the book is available. */
    public function get_active_by_book($book_id)
    {
        return $this->query_unique(
            "SELECT * FROM loans WHERE book_id = :book_id AND returned_on IS NULL",
            ['book_id' => $book_id]
        );
    }

    /** How many loans (open or returned) this book has (a book with loans cannot be deleted). */
    public function count_by_book($book_id)
    {
        $row = $this->query_unique("SELECT COUNT(*) AS total FROM loans WHERE book_id = :book_id", ['book_id' => $book_id]);
        return (int) $row['total'];
    }

    /** How many loans (open or returned) this user has (a user with loans cannot be deleted). */
    public function count_by_user($user_id)
    {
        $row = $this->query_unique("SELECT COUNT(*) AS total FROM loans WHERE user_id = :user_id", ['user_id' => $user_id]);
        return (int) $row['total'];
    }

    /** How many loans this user has not returned yet. */
    public function count_active_by_user($user_id)
    {
        $row = $this->query_unique("SELECT COUNT(*) AS total FROM loans WHERE user_id = :user_id AND returned_on IS NULL", ['user_id' => $user_id]);
        return (int) $row['total'];
    }

    /** How many loans this user has not returned although the due date has passed. */
    public function count_overdue_by_user($user_id)
    {
        $row = $this->query_unique(
            "SELECT COUNT(*) AS total FROM loans WHERE user_id = :user_id AND returned_on IS NULL AND due_date < CURDATE()",
            ['user_id' => $user_id]
        );
        return (int) $row['total'];
    }

    /** All loans of one user with the book title, newest first (for the "My loans" page). */
    public function get_by_user($user_id)
    {
        return $this->query(
            "SELECT l.*, b.title AS book_title
             FROM loans l
             JOIN books b ON b.id = l.book_id
             WHERE l.user_id = :user_id
             ORDER BY l.borrowed_on DESC",
            ['user_id' => $user_id]
        );
    }

    /** One loan of this book by this user, or null if the user never borrowed it. */
    public function get_by_user_and_book($user_id, $book_id)
    {
        return $this->query_unique(
            "SELECT * FROM loans WHERE user_id = :user_id AND book_id = :book_id LIMIT 1",
            ['user_id' => $user_id, 'book_id' => $book_id]
        );
    }
}