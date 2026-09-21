<?php

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaObjectTest extends ApiTestCase
{
    public function testCreateAMediaObject(): void
    {
        $fileName = __DIR__ . '/fixtures/image.jpg';
        if (!file_exists($fileName)) {
            copy(__DIR__ . '/fixtures/me.jpg', $fileName);
        }

        $file = new UploadedFile($fileName, 'image.jpg');
        $client = self::createClient();

        $client->request('POST', '/api/media_objects', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                // If you have additional fields in your MediaObject entity, use the parameters.
//                'parameters' => [
//                     'title' => 'title'
//                ],
                'files' => [
                    'file' => $file,
                ],
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(MediaObject::class);
        $this->assertJsonContains([
            '@context' => '/api/contexts/MediaObject',
        //  'title' => 'title',
        ]);
    }
}
