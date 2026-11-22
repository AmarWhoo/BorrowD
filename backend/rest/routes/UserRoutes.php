<?php
Flight::route('GET /users', function () {
    Flight::json(Flight::user_service()->get_all());
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    Flight::json(Flight::user_service()->get_by_id($id));
});

/** Body: { "first_name": "Emina", "last_name": "Begić", "email": "emina@library.ba", "password": "secret123", "role": "user" } */
Flight::route('POST /users', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->add($data), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->update($data, $id));
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    Flight::user_service()->delete($id);
    Flight::json(['message' => 'User deleted.']);
});

// the loans of one user (the "My loans" page)
Flight::route('GET /users/@id:[0-9]+/loans', function ($id) {
    Flight::json(Flight::loan_service()->get_by_user($id));
});