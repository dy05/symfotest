<?php

namespace App\Serializer;

use App\Contract\HasFileInterface;
use App\Entity\MediaObject;
use App\Entity\Post;
use ArrayObject;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MediaObjectNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        $context[self::ALREADY_CALLED] = true;

        if ($object instanceof HasFileInterface) {
            $object->setFilePath($this->storage->resolveUri($object, 'file'));
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof HasFileInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            // MediaObject::class => true,
            // Post::class => true,
            HasFileInterface::class => false,
        ];
    }
}
