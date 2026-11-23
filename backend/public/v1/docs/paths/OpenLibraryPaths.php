<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/OpenLibraryRoutes.php (external API).
 * swagger-php reads the #[OA\...] attributes; the methods are empty – they only carry the documentation.
 */
class OpenLibraryPaths
{
    #[OA\Get(
        path: '/books/{id}/openlibrary',
        tags: ['books', 'open library'],
        summary: 'Cover, publishers and subjects of a book from Open Library (external API)',
        description: 'Calls the Open Library Books API with the ISBN of the book (timeout from the config, 5 seconds by default). If Open Library is down, the API answers 503 and the rest of the application keeps working.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Information from Open Library',
                content: new OA\JsonContent(ref: '#/components/schemas/BookInfo')
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found, the book has no ISBN, or Open Library does not know it',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 503,
                description: 'Open Library is not available (timeout, no connection or an error answer)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getBooksByIdOpenlibrary() {}
}