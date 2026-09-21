<?php

namespace App\Encoder;

use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

final class MultipartDecoder implements DecoderInterface
{
    public const string FORMAT = 'multipart';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function decode(string $data, string $format, array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        return array_map(
            static function (string $element) {
                try {
                    return json_decode($element, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception) {
                    return $element; // not JSON — treat it as a plain string value
                }
            },
            $request->request->all()
        ) + $request->files->all();
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
