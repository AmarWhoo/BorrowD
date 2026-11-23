<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/ReviewRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty – they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class ReviewPaths
{
    #[OA\Get(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'List all reviews',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All reviews',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Review'))
            )
        ]
    )]
    public function getReviews() {}

    #[OA\Get(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Get one review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The review',
                content: new OA\JsonContent(ref: '#/components/schemas/Review')
            ),
            new OA\Response(
                response: 404,
                description: 'Review not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getReviewsById() {}

    #[OA\Post(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'Create a review',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ReviewInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'The created review',
                content: new OA\JsonContent(ref: '#/components/schemas/Review')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data, rating not 1–5, book not borrowed, or already reviewed',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postReviews() {}

    #[OA\Put(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Change the rating and comment of a review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1,
                             schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ReviewUpdate')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'The updated review',
                content: new OA\JsonContent(ref: '#/components/schemas/Review')
            ),
            new OA\Response(
                response: 400,
                description: 'Rating is not a whole number from 1 to 5',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Review not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putReviewsById() {}

    #[OA\Delete(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Delete a review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1,
                             schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 404,
                description: 'Review not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteReviewsById() {}
}
