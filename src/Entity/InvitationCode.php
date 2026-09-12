<?php

namespace App\Entity;

use App\Repository\InvitationCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvitationCodeRepository::class)]
#[UniqueEntity('code')]
class InvitationCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: 'The code cannot be empty')]
    #[Assert\Length(exactly: 5, exactMessage: 'Must be exactly 5 characters')]
    #[Assert\Regex(pattern: "/^\d+$/", message: 'Must contains only numbers')]
    private ?string $code = null;

    #[ORM\Column]
    private ?\DateTime $expired_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getExpiredAt(): ?\DateTime
    {
        return $this->expired_at;
    }

    public function setExpiredAt(\DateTime $expired_at): static
    {
        $this->expired_at = $expired_at;

        return $this;
    }
}
