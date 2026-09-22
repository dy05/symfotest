<?php

namespace App\Contract;


interface HasFileInterface
{
    public function getFilePath(): ?string;

    public function setFilePath(?string $filePath): static;
}
