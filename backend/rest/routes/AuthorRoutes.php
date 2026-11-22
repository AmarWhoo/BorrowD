<?php
Flight::route('GET /authors', function () {
    Flight::json(Flight::author_service()->get_all());
});

Flight::route('GET /authors/@id:[0-9]+', function ($id) {
    Flight::json(Flight::author_service()->get_by_id($id));
});

/** Body: { "name": "Ivo Andrić", "birth_year": 1892, "nationality": "Yugoslav" } */
Flight::route('POST /authors', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::author_service()->add($data), 201);
});

Flight::route('PUT /authors/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::author_service()->update($data, $id));
});

Flight::route('DELETE /authors/@id:[0-9]+', function ($id) {
    Flight::author_service()->delete($id);
    Flight::json(['message' => 'Author deleted.']);
});