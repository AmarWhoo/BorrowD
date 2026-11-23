<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/LoanDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/BookDao.php';

class LoanService extends BaseService
{
    protected $entity_name = 'Loan';
    private const LOAN_DAYS = 14;
    private const MAX_ACTIVE_LOANS = 3;
    private $user_dao;
    private $book_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $user_dao = null, $book_dao = null)
    {
        parent::__construct($dao ?? new LoanDao());
        $this->user_dao = $user_dao ?? new UserDao();
        $this->book_dao = $book_dao ?? new BookDao();
    }

      /** All loans of one user (used by the "My loans" page). */
    public function get_by_user($user_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException("User not found.");
        }
        return $this->dao->get_by_user($user_id);
    }

    /** Creating a loan always goes through borrow(), so every rule is checked. */
    public function add($loan)
    {
        Validator::required($loan, ['user_id', 'book_id']);
        return $this->borrow($loan['user_id'], $loan['book_id']);
    }

    /**
     * Borrow a book. Rules:
     *  - the user and the book must exist
     *  - the book must not be on loan already
     *  - a user with an overdue loan cannot borrow
     *  - a user can have at most MAX_ACTIVE_LOANS active loans
     *  - the dates are set by the server, never taken from the request
     */
    public function borrow($user_id, $book_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        Validator::integer($book_id, 'book_id', 1);

        if (!$this->user_dao->get_by_id($user_id)) {
            throw new ValidationException("The selected user does not exist.");
        }
        $book = $this->book_dao->get_by_id($book_id);
        if (!$book) {
            throw new ValidationException("The selected book does not exist.");
        }
        if ($this->dao->get_active_by_book($book_id)) {
            throw new ValidationException("'{$book['title']}' is already on loan.");
        }
        if ($this->dao->count_overdue_by_user($user_id) > 0) {
            throw new ValidationException("This user has an overdue loan. Return it before borrowing another book.");
        }
        if ($this->dao->count_active_by_user($user_id) >= self::MAX_ACTIVE_LOANS) {
            throw new ValidationException("A user can have at most " . self::MAX_ACTIVE_LOANS . " active loans. Return a book first.");
        }

        $loan = $this->dao->add([
            'user_id'     => $user_id,
            'book_id'     => $book_id,
            'borrowed_on' => date('Y-m-d'),
            'due_date'    => date('Y-m-d', strtotime('+' . self::LOAN_DAYS . ' days'))
        ]);
        return $this->dao->get_by_id($loan['id']);
    }

    /** Return a borrowed book. Rule: a loan can be returned only once. */
    public function return_loan($id)
    {
        $loan = $this->get_by_id($id);
        if ($loan['returned_on'] !== null) {
            throw new ValidationException("This loan was already returned on {$loan['returned_on']}.");
        }
        $this->dao->update(['returned_on' => date('Y-m-d')], $id);
        return $this->dao->get_by_id($id);
    }

    /** An admin can only change the dates (e.g. extend the due date); the user and the book stay the same. */
    public function update($loan, $id)
    {
        $existing = $this->get_by_id($id);
        $loan = $this->empty_to_null($loan, ['returned_on']);
        $loan = array_intersect_key($loan, array_flip(['due_date', 'returned_on']));
        $this->validate($loan + $existing, $id);
        $this->dao->update($loan, $id);
        return $this->dao->get_by_id($id);
    }

    /** Rule: an active loan cannot be deleted – the book has to be returned first. */
    public function delete($id)
    {
        $loan = $this->get_by_id($id);
        if ($loan['returned_on'] === null) {
            throw new ValidationException("This loan is still active. Return the book before deleting the loan.");
        }
        $this->dao->delete($id);
    }

    /** The date rules from the database (chk_loans_due, chk_loans_returned), checked here with a clear message. */
    protected function validate($loan, $id = null)
    {
        Validator::date($loan['due_date'], 'due_date');
        if ($loan['due_date'] < $loan['borrowed_on']) {
            throw new ValidationException("The due date cannot be before the borrow date ({$loan['borrowed_on']}).");
        }
        if ($loan['returned_on'] !== null) {
            Validator::date($loan['returned_on'], 'returned_on');
            if ($loan['returned_on'] < $loan['borrowed_on']) {
                throw new ValidationException("The return date cannot be before the borrow date ({$loan['borrowed_on']}).");
            }
            if ($loan['returned_on'] > date('Y-m-d')) {
                throw new ValidationException("The return date cannot be in the future.");
            }
        }
    }
}