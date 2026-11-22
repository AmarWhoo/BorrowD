<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/LoanRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty – they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class LoanPaths
{
    #[OA\Get(
        path: '/loans',
        tags: ['loans'],
        summary: 'List all loans',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All loans',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Loan'))
            )
        ]
    )]
    public function getLoans() {}

    #[OA\Get(
        path: '/loans/{id}',
        tags: ['loans'],
        summary: 'Get one loan',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The loan',
                content: new OA\JsonContent(ref: '#/components/schemas/Loan')
            ),
            new OA\Response(
                response: 404,
                description: 'Loan not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getLoansById() {}

    #[OA\Post(
        path: '/loans',
        tags: ['loans'],
        summary: 'Borrow a book (create a loan)',
        description: 'borrowed_on is today and due_date is today + 14 days; both are set by the server.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoanInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'The created loan',
                content: new OA\JsonContent(ref: '#/components/schemas/Loan')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or a broken rule (book already on loan, overdue loan, already 3 active loans)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postLoans() {}

    #[OA\Put(
        path: '/loans/{id}',
        tags: ['loans'],
        summary: 'Change the dates of a loan (e.g. extend it)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoanUpdate')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'The updated loan',
                content: new OA\JsonContent(ref: '#/components/schemas/Loan')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid date, or a date before the borrow date',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Loan not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putLoansById() {}

    #[OA\Delete(
        path: '/loans/{id}',
        tags: ['loans'],
        summary: 'Delete a loan',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Loan deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The loan is still active',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Loan not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteLoansById() {}

    #[OA\Put(
        path: '/loans/{id}/return',
        tags: ['loans'],
        summary: 'Return a borrowed book',
        description: 'Sets returned_on to today. No request body.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The returned loan (returned_on = today)',
                content: new OA\JsonContent(ref: '#/components/schemas/Loan')
            ),
            new OA\Response(
                response: 400,
                description: 'The loan was already returned',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Loan not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putLoansByIdReturn() {}
}
