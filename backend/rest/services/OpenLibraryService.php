<?php
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';
require_once __DIR__ . '/../dao/BookDao.php';

/**
 * External API: Open Library (https://openlibrary.org/dev/docs/api/books).
 * Finds a book of our library on Open Library by its ISBN and returns the cover and extra details.
 * If Open Library is slow or down, the failure is logged and the client gets a friendly 503 –
 * the rest of the application keeps working.
 */
class OpenLibraryService
{
    private const CONNECT_TIMEOUT = 3;   // seconds to connect to Open Library
    private const MAX_SUBJECTS = 8;      // Open Library returns 100+ subjects; the page needs a few
    private $book_dao;

    /** The DAO can be passed in (used by unit tests); by default the real one is created. */
    public function __construct($book_dao = null)
    {
        $this->book_dao = $book_dao ?? new BookDao();
    }

    /** Cover, publishers, subjects ... of one book of our library, found on Open Library by its ISBN. */
    public function get_book_info($book_id)
    {
        Validator::integer($book_id, 'id', 1);
        $book = $this->book_dao->get_by_id($book_id);
        if (!$book) {
            throw new NotFoundException("Book not found.");
        }
        if (empty($book['isbn'])) {
            throw new NotFoundException("'{$book['title']}' has no ISBN, so it cannot be looked up on Open Library.");
        }

        $key = 'ISBN:' . $book['isbn'];
        $data = $this->request('/api/books?' . http_build_query(['bibkeys' => $key, 'format' => 'json', 'jscmd' => 'data']));

        // Open Library answers {} when it does not know the ISBN
        if (!isset($data[$key])) {
            throw new NotFoundException("No information about '{$book['title']}' was found on Open Library.");
        }
        $info = $data[$key];

        // Return only what the page needs, with the same field names every time
        return [
            'book_id'         => (int) $book['id'],
            'isbn'            => $book['isbn'],
            'title'           => $info['title'] ?? $book['title'],
            'cover_url'       => $info['cover']['large'] ?? $info['cover']['medium'] ?? null,
            'number_of_pages' => $info['number_of_pages'] ?? null,
            'publish_date'    => $info['publish_date'] ?? null,
            'publishers'      => array_column($info['publishers'] ?? [], 'name'),
            'subjects'        => array_slice(array_column($info['subjects'] ?? [], 'name'), 0, self::MAX_SUBJECTS),
            'openlibrary_url' => $info['url'] ?? null
        ];
    }

    /** GET request to Open Library with a timeout. Every failure is logged and becomes a 503. */
    private function request($path)
    {
        $url = rtrim(Config::OPEN_LIBRARY_URL(), '/') . $path;

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,                               // return the body instead of printing it
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT        => (int) Config::OPEN_LIBRARY_TIMEOUT(), // the whole request, in seconds
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'BorrowD/1.0 (IT 2001 student project)'
        ]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        if ($body === false) {                  // timeout, no internet, DNS error ...
            $this->fail($url, "cURL error: $error");
        }
        if ($status !== 200) {                  // Open Library answered with an error (500, 503 ...)
            $this->fail($url, "HTTP status $status");
        }
        $data = json_decode($body, true);
        if (!is_array($data)) {                 // not JSON (e.g. an HTML error page)
            $this->fail($url, "the answer is not valid JSON");
        }
        return $data;
    }

    /** Write the details to the error log; the client only gets a friendly message. */
    private function fail($url, $reason)
    {
        error_log("Open Library request failed: $reason ($url)");
        throw new ExternalServiceException("Open Library is not available at the moment. Please try again later.");
    }
}