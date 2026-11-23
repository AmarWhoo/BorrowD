<?php
// External API: cover and details of one book from Open Library (found by its ISBN)
Flight::route('GET /books/@id:[0-9]+/openlibrary', function ($id) {
    Flight::json(Flight::openlibrary_service()->get_book_info($id));
});