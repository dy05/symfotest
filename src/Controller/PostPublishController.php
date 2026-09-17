<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class PostPublishController
{
    public function __invoke(Post $data): Post
    {
        $data->setOnline(true);
        return $data;
    }
}
