<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ContactData
{
    #[Assert\NotBlank(message: 'Please enter your name.')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Please enter your email address.')]
    #[Assert\Length(max: 100)]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    public ?string $email = null;

//    #[Assert\NotBlank(message: 'Please enter a subject.')]
//    #[Assert\Length(min: 3, max: 150)]
//    public ?string $subject = null;

    #[Assert\NotBlank(message: 'Please enter a phone number.')]
    #[Assert\Length(exactly: 10)]
    public ?string $phone = null;

    #[Assert\NotBlank(message: 'Please enter a message.')]
    #[Assert\Length(min: 4, max: 255)]
    public ?string $message = null;

    #[Assert\IsTrue(message: 'Please accept rgpd.')]
    public bool $rgpd = false;
}
