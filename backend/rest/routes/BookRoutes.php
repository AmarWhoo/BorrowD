<?php
Flight::route('GET /books', function () {
    Flight::json(Flight::book_service()->get_all());
});

Flight::route('GET /books/@id:[0-9]+', function ($id) {
    Flight::json(Flight::book_service()->get_by_id($id));
});

/** Body: { "title": "Animal Farm", "author_id": 3, "category_id": 2, "isbn": "9780451526342", "published_year": 1945, "description": "..." } */
Flight::route('POST /books', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::book_service()->add($data), 201);
});

Flight::route('PUT /books/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::book_service()->update($data, $id));
});

Flight::route('DELETE /books/@id:[0-9]+', function ($id) {
    Flight::book_service()->delete($id);
    Flight::json(['message' => 'Book deleted.']);
});

// the reviews of one book
Flight::route('GET /books/@id:[0-9]+/reviews', function ($id) {
    Flight::json(Flight::review_service()->get_by_book($id));
});