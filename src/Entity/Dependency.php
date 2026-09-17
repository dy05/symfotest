<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as ApiPost;
use App\State\DependencyStateProcessor;
use App\State\DependencyStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: false,
        ),
        new Get(),
        new ApiPost(),
        new Patch(
            denormalizationContext: [
                'groups' => ['update:Dependency']
            ]
        ),
        new Delete()
    ],
    provider: DependencyStateProvider::class,
    processor: DependencyStateProcessor::class
)]
class Dependency
{
    #[ApiProperty(identifier: true)]
    private string $uuid;

    #[ApiProperty(description: "Dependency's name")]
    #[Assert\Length(min: 2)]
    #[Assert\NotBlank]
    private string $name;

    #[ApiProperty(
        description: "Dependency's version",
        openapiContext: [
            'example' => '5.0.*'
        ]
    )]
    #[Assert\Length(min: 3)]
    #[Assert\NotBlank]
    #[Groups(['update:Dependency'])]
    private string $version;

    public function __construct(
        string $name,
        string $version
    )
    {
        $this->uuid = Uuid::v5(Uuid::fromString(Uuid::NAMESPACE_URL), $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): Dependency
    {
        $this->version = $version;
        return $this;
    }

    public function setName(string $name): Dependency
    {
        $this->name = $name;
        return $this;
    }
}
