<?php
use OpenApi\Attributes as OA;

/*
 * General API information + reusable schemas (referenced from the paths with ref: '#/components/schemas/...').
 * swagger-php 6 reads these PHP 8 attributes – the class itself is empty.
 * (Plain /* comments on purpose: swagger-php would turn a /** doc comment into a description.)
 */

#[OA\Info(
    title: 'BorrowD API',
    description: 'REST API of BorrowD – a book lending library – IT 2001 Web Programming.',
    version: '1.0',
    contact: new OA\Contact(name: 'Web Programming')
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]

/* ---------- shared answers ---------- */
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Field \'name\' is required.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Author deleted.')]
)]

/* ---------- authors ---------- */
#[OA\Schema(
    schema: 'AuthorInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Ivo Andrić'),
        new OA\Property(property: 'birth_year', type: 'integer', nullable: true, example: 1892),
        new OA\Property(property: 'nationality', type: 'string', nullable: true, example: 'Yugoslav')
    ]
)]
#[OA\Schema(
    schema: 'Author',
    allOf: [new OA\Schema(ref: '#/components/schemas/AuthorInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 2)]
)]

/* ---------- categories ---------- */
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Poetry'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Poems and collections of poems')
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [new OA\Schema(ref: '#/components/schemas/CategoryInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 1)]
)]

/* ---------- books ---------- */
#[OA\Schema(
    schema: 'BookInput',
    required: ['title', 'author_id', 'category_id'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Animal Farm'),
        new OA\Property(property: 'author_id', type: 'integer', example: 3),
        new OA\Property(property: 'category_id', type: 'integer', example: 2),
        new OA\Property(property: 'isbn', type: 'string', nullable: true, example: '9780451526342',
                        description: '10 or 13 digits, dashes are removed; unique'),
        new OA\Property(property: 'published_year', type: 'integer', nullable: true, example: 1945),
        new OA\Property(property: 'description', type: 'string', nullable: true,
                        example: 'Farm animals take over and repeat the mistakes of their masters.')
    ]
)]
#[OA\Schema(
    schema: 'Book',
    allOf: [new OA\Schema(ref: '#/components/schemas/BookInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'author_name', type: 'string', example: 'George Orwell'),
        new OA\Property(property: 'category_name', type: 'string', example: 'Dystopia')
    ]
)]

/* ---------- users ---------- */
#[OA\Schema(
    schema: 'UserInput',
    required: ['first_name', 'last_name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Emina'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Begić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'emina@library.ba'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123',
                        description: 'At least 8 characters with a letter and a digit. Optional on update.'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user',
                        description: 'Default: user')
    ]
)]
#[OA\Schema(
    schema: 'User',
    description: 'A user as the API returns it – the password is never returned.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'first_name', type: 'string', example: 'Amar'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hodžić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'amar@library.ba'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-01 10:00:00')
    ]
)]

/* ---------- loans ---------- */
#[OA\Schema(
    schema: 'LoanInput',
    description: 'Borrow a book. borrowed_on and due_date (+14 days) are set by the server.',
    required: ['user_id', 'book_id'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'book_id', type: 'integer', example: 4)
    ]
)]
#[OA\Schema(
    schema: 'LoanUpdate',
    description: 'Only the dates of a loan can be changed; the user and the book stay the same.',
    properties: [
        new OA\Property(property: 'due_date', type: 'string', format: 'date', example: '2026-12-20'),
        new OA\Property(property: 'returned_on', type: 'string', format: 'date', nullable: true, example: null)
    ]
)]
#[OA\Schema(
    schema: 'Loan',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'book_id', type: 'integer', example: 3),
        new OA\Property(property: 'borrowed_on', type: 'string', format: 'date', example: '2026-10-20'),
        new OA\Property(property: 'due_date', type: 'string', format: 'date', example: '2026-11-03'),
        new OA\Property(property: 'returned_on', type: 'string', format: 'date', nullable: true, example: null,
                        description: 'NULL while the loan is active')
    ]
)]
#[OA\Schema(
    schema: 'UserLoan',
    description: 'A loan of one user, with the title of the book (GET /users/{id}/loans).',
    allOf: [new OA\Schema(ref: '#/components/schemas/Loan')],
    properties: [new OA\Property(property: 'book_title', type: 'string', example: 'Nineteen Eighty-Four')]
)]

/* ---------- reviews ---------- */
#[OA\Schema(
    schema: 'ReviewInput',
    description: 'The user must have borrowed the book and can review it only once.',
    required: ['user_id', 'book_id', 'rating'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'book_id', type: 'integer', example: 3),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 4),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'A frightening classic.')
    ]
)]
#[OA\Schema(
    schema: 'ReviewUpdate',
    description: 'Only the rating and the comment can be changed.',
    required: ['rating'],
    properties: [
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Even better the second time.')
    ]
)]
#[OA\Schema(
    schema: 'Review',
    allOf: [new OA\Schema(ref: '#/components/schemas/ReviewInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-12 18:30:00')
    ]
)]
#[OA\Schema(
    schema: 'BookReview',
    description: 'A review of one book, with the name of the reviewer (GET /books/{id}/reviews).',
    allOf: [new OA\Schema(ref: '#/components/schemas/Review')],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Lejla'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Kovačević')
    ]
)]
/* ---------- external API: Open Library ---------- */
#[OA\Schema(
    schema: 'BookInfo',
    description: 'Information about one of our books from Open Library, found by its ISBN.',
    properties: [
        new OA\Property(property: 'book_id', type: 'integer', example: 3),
        new OA\Property(property: 'isbn', type: 'string', example: '9780451524935'),
        new OA\Property(property: 'title', type: 'string', example: '1984', description: 'The title on Open Library'),
        new OA\Property(property: 'cover_url', type: 'string', nullable: true,
                        example: 'https://covers.openlibrary.org/b/id/15279181-L.jpg'),
        new OA\Property(property: 'number_of_pages', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'publish_date', type: 'string', nullable: true, example: '1977'),
        new OA\Property(property: 'publishers', type: 'array', items: new OA\Items(type: 'string'),
                        example: ['Signet Classics']),
        new OA\Property(property: 'subjects', type: 'array', items: new OA\Items(type: 'string'),
                        description: 'At most 8', example: ['futurology', 'censorship', 'surveillance']),
        new OA\Property(property: 'openlibrary_url', type: 'string', nullable: true,
                        example: 'http://openlibrary.org/books/OL62592675M/1984')
    ]
)]
class OpenApiSpec {}