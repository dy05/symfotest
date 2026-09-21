<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Attribute\ApiAuthGroups;
use App\Repository\PostRepository;
use App\Contract\UserOwnedInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiAuthGroups([
    'CAN_EDIT' => 'read:collection:Owner',
    'ROLE_USER' => 'read:collection:User',
])]
class Post implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Groups(['read:collection', 'write:item', 'create:item'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:5, groups: ['update:item'])]
    #[Groups(['read:collection', 'write:item'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Groups(['read:collection:User', 'write:item', 'create:item'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:item', 'read:collection:Owner'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    private ?DateTime $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'posts')]
    #[Groups(['read:item', 'write:item'])]
    #[ApiProperty(example: '/api/categories/1')]
    #[Assert\Valid]
    private ?Category $category = null;

    #[ORM\Column(options: ["default" => 0])]
    #[ApiProperty(openapiContext: ['type' => 'boolean'])]
    #[Groups(['read:collection:User', 'write:item'])]
    private bool $online = false;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $user = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\ManyToMany(targetEntity: Media::class, inversedBy: 'posts', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'post_media')]
    #[Assert\Valid]
    #[Assert\IsNull]
    #[Groups(['read:collection', 'read:item', 'write:item'])]
    private Collection $medias;

//    #[ORM\ManyToOne(targetEntity: MediaObject::class)]
//    #[ORM\JoinColumn(nullable: true)]
//    #[ApiProperty(types: ['https://schema.org/image'])]
//    #[Groups(['read:collection', 'read:item', 'write:item'])]
//    public ?MediaObject $image = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['read:collection', 'read:item'])]
    private ?string $contentUrl = null;

    #[Vich\UploadableField(
        mapping: 'media_object',
        fileNameProperty: 'filePath',
    )]
    #[Groups(['read:collection', 'read:item', 'write:item'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection', 'read:item'])]
    public ?string $filePath = null;


    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->medias = new ArrayCollection();
    }

    public static function validationGroups(self $post): array
    {
        return ['update:item'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): static
    {
        $this->online = $online;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function setMedias(?array $medias = []): static
    {
        $this->medias = new ArrayCollection($medias ?? []);

        return $this;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        $this->medias->removeElement($media);

        return $this;
    }

    public function setContentUrl(?string $contentUrl = null): static
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->filePath ? '/storage/' . $this->filePath : null;
    }
}
