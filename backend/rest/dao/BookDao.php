<?php
require_once __DIR__ . '/BaseDao.php';

class BookDao extends BaseDao
{
    /** Every book together with the name of its author and its category. */
    private const SELECT_WITH_NAMES = "SELECT b.*, a.name AS author_name, c.name AS category_name
                                       FROM books b
                                       JOIN authors a    ON a.id = b.author_id
                                       JOIN categories c ON c.id = b.category_id";

    public function __construct()
    {
        parent::__construct('books', ['title', 'author_id', 'category_id', 'isbn', 'published_year', 'description']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT_WITH_NAMES . " ORDER BY b.title");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT_WITH_NAMES . " WHERE b.id = :id", ['id' => $id]);
    }
}