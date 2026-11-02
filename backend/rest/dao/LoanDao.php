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
}