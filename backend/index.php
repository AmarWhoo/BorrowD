<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/rest/services/AuthorService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/BookService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/LoanService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/OpenLibraryService.php';

// Register every service once
Flight::register('author_service', 'AuthorService');
Flight::register('category_service', 'CategoryService');
Flight::register('book_service', 'BookService');
Flight::register('user_service', 'UserService');
Flight::register('loan_service', 'LoanService');
Flight::register('review_service', 'ReviewService');
Flight::register('openlibrary_service', 'OpenLibraryService');

/* ONE place that turns every exception into a JSON answer */
Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {          // thrown by a service
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {                                    // PDOException, a bug ...
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);                          // details only in the server log
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => $message]))
        ->send();
});

/* A URL that matches no route */
Flight::map('notFound', function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => 'Endpoint not found.']))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'BorrowD API', 'message' => 'The backend is running.']);
});

require_once __DIR__ . '/rest/routes/AuthorRoutes.php';
require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/BookRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/LoanRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
require_once __DIR__ . '/rest/routes/OpenLibraryRoutes.php';

Flight::start();