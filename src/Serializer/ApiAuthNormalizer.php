<?php

namespace App\Serializer;

use App\Attribute\ApiAuthGroups;
use App\Contract\UserOwnedInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiAuthNormalizer implements NormalizerInterface
{
    private const string ALREADY_CALLED_NORMALIZED = 'ApiAuthNormalizerCalled';

    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        private readonly NormalizerInterface $normalizer,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null
    {
        $context[static::ALREADY_CALLED_NORMALIZED] = true;

        $reflectionClass = new ReflectionClass(get_class($data));
        /** @var ApiAuthGroups $apiAuthGroups */
        $apiAuthGroups = $reflectionClass->getAttributes(ApiAuthGroups::class)[0]->newInstance();

        $groups = $context['groups'] ?? [];

        foreach($apiAuthGroups->groups as $permission => $group) {
            if ($this->authorizationChecker->isGranted($permission, $data)) {
                $groups[] = $group;
            }
        }

        $context['groups'] = array_unique($groups);

//        if ($this->authorizationChecker->isGranted(UserOwnedVoter::CAN_EDIT, $data)) {
//            $context['groups'][] = 'read:collection:User';
//        }

        // $obj = $this->normalizer->normalize($data, $format, $context);
        // $medias = $obj['medias'] ?? [];
        // if (!is_object($medias) && !is_array($medias)) {
        //     $medias = [];
        // }

        // $obj->setMedias($medias);
        // return $obj;

        return $this->normalizer->normalize($data, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!is_object($data)) {
            return false;
        }

        $reflectionClass = new ReflectionClass(get_class($data));
        $classAttributes = $reflectionClass->getAttributes(ApiAuthGroups::class);
//        $argumentsTab = isset($classAttributes[0]) ? $classAttributes[0]->getArguments() : [];
//        $authorizations = array_values($argumentsTab[0] ?? []);
//
//        $isSupported = in_array('read:collection:Owner', $authorizations)
//            || in_array('read:collection:User', $authorizations) ;

//        if (! $isSupported) {
//            return false;
//        }

        if (empty($classAttributes)) {
            return false;
        }

        $alreadyCalled = $context[static::ALREADY_CALLED_NORMALIZED] ?? false;
        if ($alreadyCalled) {
            return false;
        }

        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        if (!in_array($format, ['json', 'jsonld'], true)) {
            return [];
        }

        return [
            UserOwnedInterface::class => false,
        ];
    }
}
