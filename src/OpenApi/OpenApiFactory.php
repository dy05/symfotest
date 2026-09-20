<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(protected OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet()?->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $openApi->getPaths()->addPath(
            '/ping',
            new PathItem(null, 'ping', null, new Operation('ping-id', [], [], 'Ping server'))
        );


//        // Not work
//        $securitySchemes = $openApi->getComponents()->getSecuritySchemes() ?: new ArrayObject();
//        $securitySchemes['apiKey'] = new ArrayObject([
//            'type' => 'header',
//            'scheme' => 'Bearer',
//            'in' => 'header',
//            'name' => 'Authorization'
//        ]);
//
//        $openApi = $openApi->withComponents(
//            $openApi->getComponents()->withSecuritySchemes($securitySchemes)
//        );


//        $schemas = $openApi->getComponents()->getSecuritySchemes();
//        $schemas['cookieAuth'] = new ArrayObject([
//            'type' => 'apiKey',
//            'in' => 'cookie',
//            'name' => 'PHPSESSID'
//        ]);

//        $schemas = $openApi->getComponents()->getSchemas();
//        $schemas['Credentials'] = new ArrayObject([
//            'type' => 'object',
//            'properties' => [
//                'username' => [
//                    'type' => 'string',
//                    'example' => 'test@test.com',
//                ],
//                'password' => [
//                    'type' => 'string',
//                    'example' => 'password'
//                ]
//            ]
//        ]);
//
//        $pathItem = new PathItem(
//            post: new Operation(
//                operationId: 'postApiLogin',
//                tags: ['Auth'],
//                responses: [
//                    '200' => new Response(
//                        description: 'Authenticated user',
//                        content: new ArrayObject([
//                            'application/json' => [
//                                'schema' => [
//                                    '$ref' => '#/components/schemas/User-read.User'
//                                ]
//                            ]
//                        ])
//                    )
//                ],
//                requestBody: new RequestBody(
//                    content: new ArrayObject([
//                        'application/json' => [
//                            'schema' => [
//                                '$ref' => '#/components/schemas/Credentials'
//                            ]
//                        ]
//                    ])
//                )
//            )
//        );
//        $openApi->getPaths()->addPath('/api/auth/login', $pathItem);
//
//        $pathItem = new PathItem(
//            post: new Operation(
//                operationId: 'postApiLogout',
//                tags: ['User'],
//                responses: [
//                    '200' => new Response(
//                        description: 'Unauthenticate user',
//                        content: new ArrayObject([
//                            'application/json' => [],
//                            'application/ld+json' => [],
//                        ])
////                        content: new ArrayObject([
////                            'application/json' => [
////                                'schema' => new ArrayObject([
////                                    'type' => 'object',
////                                    'properties' => [
////                                        'message' => [
////                                            'type' => 'string',
////                                            'example' => 'User logout successfully.'
////                                        ]
////                                    ]
////                                ])
////                            ],
////                            'application/ld+json' => [
////                                'schema' => new ArrayObject([
////                                    'type' => 'object',
////                                    'properties' => [
////                                        'message' => [
////                                            'type' => 'string',
////                                            'example' => 'User logout successfully.'
////                                        ]
////                                    ]
////                                ])
////                            ]
////                        ])
//                    )
//                ],
//                requestBody: new RequestBody(
//                    content: new ArrayObject([
//                        'application/json' => [],
//                        'application/ld+json' => []
//                    ])
//                )
//
//            )
//        );
//
//        $openApi->getPaths()->addPath('/api/auth/logout', $pathItem);

//        $pathItem = $openApi->getPaths()->getPath('/api/auth/login');
//
//        if ($pathItem && $post = $pathItem->getPost()) {
//            $responses = $post->getResponses();
//
//            // keep only the ones you actually want
//            $responses = array_intersect_key($responses, ['200' => true, '401' => true]);
//
//            $openApi->getPaths()->addPath(
//                '/api/auth/login',
//                $pathItem->withPost($post->withResponses($responses))
//            );
//        }

        return $openApi;
    }
}
