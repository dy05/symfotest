<?php

namespace App\Serializer;

use App\Contract\UserOwnedInterface;
use App\Entity\User;
use Exception;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserOwnedDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const string ALREADY_CALLED_DENORMALIZED = 'UserOwnedDenormalizerCalled';

    public function __construct(private readonly Security $security)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $data[static::ALREADY_CALLED_DENORMALIZED] = true;
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);

        if ($obj instanceof UserOwnedInterface) {
            $user = $this->security->getUser();

            if ($user instanceof User) {
                $obj->setUser($user);
            }
        }

        return $obj;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        $alreadyCalled = $data[static::ALREADY_CALLED_DENORMALIZED] ?? false;
        if ($alreadyCalled) {
            return false;
        }

        try {
            $reflectionClass = new ReflectionClass($context['resource_class'] ?? '');
            return $reflectionClass->implementsInterface(UserOwnedInterface::class);
        } catch (Exception $exc) {
            if ($exc->getCode()) {
                return false;
            }
        }

        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            UserOwnedInterface::class => false,
        ];
    }
}
