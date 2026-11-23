<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/BookRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty – they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class BookPaths
{
    #[OA\Get(
        path: '/books',
        tags: ['books'],
        summary: 'List all books',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All books',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Book'))
            )
        ]
    )]
    public function getBooks() {}

    #[OA\Get(
        path: '/books/{id}',
        tags: ['books'],
        summary: 'Get one book',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The book',
                content: new OA\JsonContent(ref: '#/components/schemas/Book')
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getBooksById() {}

    #[OA\Post(
        path: '/books',
        tags: ['books'],
        summary: 'Create a book',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'The created book',
                content: new OA\JsonContent(ref: '#/components/schemas/Book')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data, unknown author or category, or the ISBN already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postBooks() {}

    #[OA\Put(
        path: '/books/{id}',
        tags: ['books'],
        summary: 'Update a book',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3,
                             schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'The updated book',
                content: new OA\JsonContent(ref: '#/components/schemas/Book')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data, unknown author or category, or the ISBN already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putBooksById() {}

    #[OA\Delete(
        path: '/books/{id}',
        tags: ['books'],
        summary: 'Delete a book',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Book deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The book has loans or reviews',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteBooksById() {}

    #[OA\Get(
        path: '/books/{id}/reviews',
        tags: ['books', 'reviews'],
        summary: 'List the reviews of one book',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 6,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The reviews of the book, newest first',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/BookReview'))
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getBooksByIdReviews() {}
}
