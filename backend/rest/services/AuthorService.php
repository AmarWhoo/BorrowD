<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthorDao.php';
require_once __DIR__ . '/../dao/BookDao.php';

class AuthorService extends BaseService
{
    protected $entity_name = 'Author';
    private $book_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $book_dao = null)
    {
        parent::__construct($dao ?? new AuthorDao());
        $this->book_dao = $book_dao ?? new BookDao();
    }

    public function add($author)
    {
        return parent::add($this->empty_to_null($author, ['birth_year', 'nationality']));
    }

    public function update($author, $id)
    {
        return parent::update($this->empty_to_null($author, ['birth_year', 'nationality']), $id);
    }

    /** Rule: an author who still has books cannot be deleted. */
    public function delete($id)
    {
        $this->get_by_id($id);
        $books = $this->book_dao->count_by_author($id);
        if ($books > 0) {
            throw new ValidationException("This author still has $books book(s). Delete them or change their author first.");
        }
        $this->dao->delete($id);
    }

    protected function validate($author, $id = null)
    {
        Validator::required($author, ['name']);
        Validator::length($author['name'], 'name', 2, 100);

        // Rule: the birth year cannot be in the future
        if (!empty($author['birth_year'])) {
            Validator::integer($author['birth_year'], 'birth_year', 1, (int) date('Y'));
        }
        if (!empty($author['nationality'])) {
            Validator::length($author['nationality'], 'nationality', 2, 50);
        }
    }
}