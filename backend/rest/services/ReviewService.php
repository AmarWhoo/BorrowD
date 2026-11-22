<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/BookDao.php';
require_once __DIR__ . '/../dao/LoanDao.php';

class ReviewService extends BaseService
{
    protected $entity_name = 'Review';
    private $user_dao;
    private $book_dao;
    private $loan_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $user_dao = null, $book_dao = null, $loan_dao = null)
    {
        parent::__construct($dao ?? new ReviewDao());
        $this->user_dao = $user_dao ?? new UserDao();
        $this->book_dao = $book_dao ?? new BookDao();
        $this->loan_dao = $loan_dao ?? new LoanDao();
    }

        /** All reviews of one book (used by the book details page). */
    public function get_by_book($book_id)
    {
        Validator::integer($book_id, 'book_id', 1);
        if (!$this->book_dao->get_by_id($book_id)) {
            throw new NotFoundException("Book not found.");
        }
        return $this->dao->get_by_book($book_id);
    }

    public function add($review)
    {
        return parent::add($this->empty_to_null($review, ['comment']));
    }

    /** Only the rating and the comment can be changed; the user and the book stay the same. */
    public function update($review, $id)
    {
        $existing = $this->get_by_id($id);
        $review = $this->empty_to_null($review, ['comment']);
        $review = array_intersect_key($review, array_flip(['rating', 'comment']));
        $this->validate($review + $existing, $id);
        $this->dao->update($review, $id);
        return $this->dao->get_by_id($id);
    }

    protected function validate($review, $id = null)
    {
        Validator::required($review, ['user_id', 'book_id', 'rating']);
        Validator::integer($review['user_id'], 'user_id', 1);
        Validator::integer($review['book_id'], 'book_id', 1);
        Validator::integer($review['rating'], 'rating', 1, 5);
        if (!empty($review['comment'])) {
            Validator::length($review['comment'], 'comment', 0, 1000);
        }

        // Rule: the user and the book must exist
        if (!$this->user_dao->get_by_id($review['user_id'])) {
            throw new ValidationException("The selected user does not exist.");
        }
        $book = $this->book_dao->get_by_id($review['book_id']);
        if (!$book) {
            throw new ValidationException("The selected book does not exist.");
        }

        // Rule: one review per user and book
        $existing = $this->dao->get_by_user_and_book($review['user_id'], $review['book_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("This user has already reviewed '{$book['title']}'. Edit the existing review instead.");
        }

        // Rule: only books the user has borrowed can be reviewed
        if (!$this->loan_dao->get_by_user_and_book($review['user_id'], $review['book_id'])) {
            throw new ValidationException("Only books you have borrowed can be reviewed. Borrow '{$book['title']}' first.");
        }
    }
}