<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\AuthController;
use App\Controller\SecurityController;
use App\Repository\UserRepository;
use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user/me',
            controller: AuthController::class,
            openapi: new Operation(
                security: [
                    [
                        'JWT' => [
                            'name' => 'Authorization',
                            'schema' => 'Bearer',
                            'type' => 'header',
                            'in' => 'header'
                        ]
                    ]
                ],

            ),
            description: 'Get active user',
            security: 'is_granted("ROLE_USER")',
            name: 'me'
        ),
        new Get(
            uriTemplate: '/auth/me',
            stateless: false,
            controller: AuthController::class,
            openapi: new Operation(
                security: [
                    ['cookieAuth' => []]
                ]
            ),
            description: 'Get active user with cookie',
            security: 'is_granted("ROLE_USER")',
            name: 'me cookie'
        ),
        new ApiPost(
            uriTemplate: '/auth/login',
            controller: SecurityController::class . '::apiAuthLogin',
            openapi: new Operation(
                summary: 'Authenticate User',
                description: 'Authenticate User',
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string'],
                                    'password' => ['type' => 'string']
                                ]
                            ],
                            'example' => [
                                'username' => 'test@test.com',
                                'password' => 'password'
                            ]
                        ],
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string'],
                                    'password' => ['type' => 'string']
                                ]
                            ],
                            'example' => [
                                'username' => 'test@test.com',
                                'password' => 'password'
                            ]
                        ]
                    ])
                )
            ),
            description: 'Login user',
            read: false,
            name: 'login'
//            parameters: [
//                'username' =>  new QueryParameter(
//                    schema: [
//                        'type' => 'string'
//                    ],
//                    required: true,
//                    default: 'test@test.com'
//                ),
//                'password' =>  new QueryParameter(
//                    schema: [
//                        'type' => 'string'
//                    ],
//                    required: true,
//                    default: 'password'
//                ),
//            ]
        )
    ],
    normalizationContext: [
        'groups' => ['read:User']
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:User'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['read:User'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['read:User'])]
    private array $roles = [];

    /**
     * @var null|string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }
}
