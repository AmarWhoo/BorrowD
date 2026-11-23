<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/BookDao.php';

class CategoryService extends BaseService
{
    protected $entity_name = 'Category';
    private $book_dao;

    /** DAOs can be passed in (used by unit tests); by default real DAOs are created. */
    public function __construct($dao = null, $book_dao = null)
    {
        parent::__construct($dao ?? new CategoryDao());
        $this->book_dao = $book_dao ?? new BookDao();
    }

    public function add($category)
    {
        return parent::add($this->empty_to_null($category, ['description']));
    }

    public function update($category, $id)
    {
        return parent::update($this->empty_to_null($category, ['description']), $id);
    }

    /** Rule: a category that still contains books cannot be deleted. */
    public function delete($id)
    {
        $this->get_by_id($id);
        $books = $this->book_dao->count_by_category($id);
        if ($books > 0) {
            throw new ValidationException("This category still contains $books book(s). Move or delete them first.");
        }
        $this->dao->delete($id);
    }

    protected function validate($category, $id = null)
    {
        Validator::required($category, ['name']);
        Validator::length($category['name'], 'name', 2, 50);
        if (!empty($category['description'])) {
            Validator::length($category['description'], 'description', 0, 255);
        }

        // Rule: category names are unique
        $existing = $this->dao->get_by_name(trim($category['name']));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A category named '{$category['name']}' already exists.");
        }
    }
}