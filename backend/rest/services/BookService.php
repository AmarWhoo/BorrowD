<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BookDao.php';
require_once __DIR__ . '/../dao/AuthorDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/LoanDao.php';
require_once __DIR__ . '/../dao/ReviewDao.php';

class BookService extends BaseService
{
    protected $entity_name = 'Book';
    private $author_dao;
    private $category_dao;
    private $loan_dao;
    private $review_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $author_dao = null, $category_dao = null, $loan_dao = null, $review_dao = null)
    {
        parent::__construct($dao ?? new BookDao());
        $this->author_dao   = $author_dao   ?? new AuthorDao();
        $this->category_dao = $category_dao ?? new CategoryDao();
        $this->loan_dao     = $loan_dao     ?? new LoanDao();
        $this->review_dao   = $review_dao   ?? new ReviewDao();
    }

    public function add($book)
    {
        return parent::add($this->normalize($book));
    }

    public function update($book, $id)
    {
        return parent::update($this->normalize($book), $id);
    }

    /** Rule: a book that has loans or reviews cannot be deleted (it is part of the library history). */
    public function delete($id)
    {
        $book = $this->get_by_id($id);
        $loans = $this->loan_dao->count_by_book($id);
        $reviews = $this->review_dao->count_by_book($id);
        if ($loans > 0 || $reviews > 0) {
            throw new ValidationException("'{$book['title']}' has $loans loan(s) and $reviews review(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function validate($book, $id = null)
    {
        Validator::required($book, ['title', 'author_id', 'category_id']);
        Validator::length($book['title'], 'title', 1, 150);
        Validator::integer($book['author_id'], 'author_id', 1);
        Validator::integer($book['category_id'], 'category_id', 1);

        // Rule: the author and the category must exist
        if (!$this->author_dao->get_by_id($book['author_id'])) {
            throw new ValidationException("The selected author does not exist.");
        }
        if (!$this->category_dao->get_by_id($book['category_id'])) {
            throw new ValidationException("The selected category does not exist.");
        }

        // Rule: the published year cannot be in the future
        if (!empty($book['published_year'])) {
            Validator::integer($book['published_year'], 'published_year', 1, (int) date('Y'));
        }

        if (!empty($book['isbn'])) {
            // Rule: an ISBN has 10 or 13 digits (ISBN-10 may end with X)
            if (!preg_match('/^(\d{9}[\dX]|\d{13})$/', $book['isbn'])) {
                throw new ValidationException("Field 'isbn' must have 10 or 13 digits.");
            }
            // Rule: ISBNs are unique
            $existing = $this->dao->get_by_isbn($book['isbn']);
            if ($existing && $existing['id'] != $id) {
                throw new ValidationException("A book with ISBN {$book['isbn']} already exists ('{$existing['title']}').");
            }
        }
    }

    /** Empty optional fields become NULL; the ISBN is saved without dashes and spaces. */
    private function normalize($book)
    {
        $book = $this->empty_to_null($book, ['isbn', 'published_year', 'description']);
        if (!empty($book['isbn'])) {
            $book['isbn'] = strtoupper(str_replace(['-', ' '], '', $book['isbn']));
        }
        return $book;
    }
}