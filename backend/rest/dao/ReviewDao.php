<?php
require_once __DIR__ . '/BaseDao.php';

class ReviewDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('reviews', ['user_id', 'book_id', 'rating', 'comment']);
    }

    /** How many reviews this book has (a book with reviews cannot be deleted). */
    public function count_by_book($book_id)
    {
        $row = $this->query_unique("SELECT COUNT(*) AS total FROM reviews WHERE book_id = :book_id", ['book_id' => $book_id]);
        return (int) $row['total'];
    }

    /** How many reviews this user has written (a user with reviews cannot be deleted). */
    public function count_by_user($user_id)
    {
        $row = $this->query_unique("SELECT COUNT(*) AS total FROM reviews WHERE user_id = :user_id", ['user_id' => $user_id]);
        return (int) $row['total'];
    }

    /** The review this user wrote for this book, or null (one review per user and book). */
    public function get_by_user_and_book($user_id, $book_id)
    {
        return $this->query_unique(
            "SELECT * FROM reviews WHERE user_id = :user_id AND book_id = :book_id",
            ['user_id' => $user_id, 'book_id' => $book_id]
        );
    }

    /** All reviews of one book with the reviewer's name, newest first (for the book details page). */
    public function get_by_book($book_id)
    {
        return $this->query(
            "SELECT r.*, u.first_name, u.last_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.book_id = :book_id
             ORDER BY r.created_at DESC",
            ['book_id' => $book_id]
        );
    }
}