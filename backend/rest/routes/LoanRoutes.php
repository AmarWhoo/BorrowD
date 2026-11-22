<?php
Flight::route('GET /loans', function () {
    Flight::json(Flight::loan_service()->get_all());
});

Flight::route('GET /loans/@id:[0-9]+', function ($id) {
    Flight::json(Flight::loan_service()->get_by_id($id));
});

/** Borrow a book. Body: { "user_id": 2, "book_id": 4 } – the dates are set by the server. */
Flight::route('POST /loans', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::loan_service()->add($data), 201);
});

/** Change the dates of a loan (e.g. extend it). Body: { "due_date": "2026-12-20" } */
Flight::route('PUT /loans/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::loan_service()->update($data, $id));
});

// PUT /loans/5/return – the book is returned today
Flight::route('PUT /loans/@id:[0-9]+/return', function ($id) {
    Flight::json(Flight::loan_service()->return_loan($id));
});

Flight::route('DELETE /loans/@id:[0-9]+', function ($id) {
    Flight::loan_service()->delete($id);
    Flight::json(['message' => 'Loan deleted.']);
});