<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/AuthorRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty – they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class AuthorPaths
{
    #[OA\Get(
        path: '/authors',
        tags: ['authors'],
        summary: 'List all authors',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All authors',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Author'))
            )
        ]
    )]
    public function getAuthors() {}

    #[OA\Get(
        path: '/authors/{id}',
        tags: ['authors'],
        summary: 'Get one author',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The author',
                content: new OA\JsonContent(ref: '#/components/schemas/Author')
            ),
            new OA\Response(
                response: 404,
                description: 'Author not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getAuthorsById() {}

    #[OA\Post(
        path: '/authors',
        tags: ['authors'],
        summary: 'Create a author',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AuthorInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'The created author',
                content: new OA\JsonContent(ref: '#/components/schemas/Author')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data (e.g. missing name, birth year in the future)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postAuthors() {}

    #[OA\Put(
        path: '/authors/{id}',
        tags: ['authors'],
        summary: 'Update a author',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AuthorInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'The updated author',
                content: new OA\JsonContent(ref: '#/components/schemas/Author')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data (e.g. missing name, birth year in the future)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Author not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putAuthorsById() {}

    #[OA\Delete(
        path: '/authors/{id}',
        tags: ['authors'],
        summary: 'Delete a author',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Author deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The author still has books',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Author not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteAuthorsById() {}
}
