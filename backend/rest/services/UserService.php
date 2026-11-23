<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/LoanDao.php';
require_once __DIR__ . '/../dao/ReviewDao.php';

class UserService extends BaseService
{
    protected $entity_name = 'User';
    private const ROLES = ['admin', 'user'];
    private $loan_dao;
    private $review_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $loan_dao = null, $review_dao = null)
    {
        parent::__construct($dao ?? new UserDao());
        $this->loan_dao   = $loan_dao   ?? new LoanDao();
        $this->review_dao = $review_dao ?? new ReviewDao();
    }

    public function add($user)
    {
        $user['role'] = $user['role'] ?? 'user';
        $this->validate($user);

        Validator::required($user, ['password']);
        Validator::password($user['password']);

        // Rule: passwords are NEVER stored in plain text
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        $user['email'] = strtolower(trim($user['email']));

        $created = $this->dao->add($user);
        return $this->dao->get_by_id($created['id']);   // get_by_id does not return the hash
    }

    public function update($user, $id)
    {
        $this->get_by_id($id);
        $this->validate($user, $id);

        // Rule: the password is optional on update; when it is sent, it is checked and hashed again
        if (isset($user['password']) && $user['password'] !== '') {
            Validator::password($user['password']);
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        } else {
            unset($user['password']);
        }
        $user['email'] = strtolower(trim($user['email']));

        $this->dao->update($user, $id);
        return $this->dao->get_by_id($id);
    }

    /** Rule: a user with loans or reviews cannot be deleted (they are part of the library history). */
    public function delete($id)
    {
        $this->get_by_id($id);
        $loans = $this->loan_dao->count_by_user($id);
        $reviews = $this->review_dao->count_by_user($id);
        if ($loans > 0 || $reviews > 0) {
            throw new ValidationException("This user has $loans loan(s) and $reviews review(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ['first_name', 'last_name', 'email']);
        Validator::length($user['first_name'], 'first_name', 2, 50);
        Validator::length($user['last_name'], 'last_name', 2, 50);
        Validator::email($user['email']);
        Validator::length($user['email'], 'email', 5, 100);

        if (isset($user['role'])) {
            Validator::one_of($user['role'], 'role', self::ROLES);
        }

        // Rule: e-mail addresses are unique (Amar@Library.ba and amar@library.ba are the same account)
        $existing = $this->dao->get_by_email(strtolower(trim($user['email'])));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("An account with the e-mail '{$user['email']}' already exists.");
        }
    }
}