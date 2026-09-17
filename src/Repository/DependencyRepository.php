<?php

namespace App\Repository;

use App\Entity\Dependency;

class DependencyRepository
{
    public static string $composerKey = 'require-dev';

    private array $data;

    public function __construct(protected string $rootPath)
    {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'composer.json';
        $data = [];
        if (file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);
            //$dependencies = array_merge($json['require'] ?? [], $json['require-dev'] ?? []);
            $dependencies = $json[static::$composerKey] ?? [];

            foreach ($dependencies as $name => $version) {
                $dependency = new Dependency($name, $version);
                $data[$dependency->getUuid()] = $dependency;
            }
        }

        $this->data = $data;
    }

    /**
     * @return Dependency[]
     */
    public function findAll(): array
    {
        return $this->data;
    }

    public function find($uuid): ?Dependency
    {
        return $this->data[$uuid] ?? null;
    }

    public function persist(Dependency $dependency): void
    {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'composer.json';
        if (file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);
            $json[static::$composerKey] = array_merge(
                $json[static::$composerKey] ?? [],
                [
                    $dependency->getName() => $dependency->getVersion()
                ]
            );

            file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    public function delete(Dependency $dependency): void
    {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'composer.json';
        if (file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);
//            $name = $dependency->getName();
//            $json[static::$composerKey] = array_filter(
//                $json[static::$composerKey] ?? [],
//                function ($key) use ($name) {
//                    return $name != $key;
//                },
//                ARRAY_FILTER_USE_KEY
//            );
            unset($json[static::$composerKey][$dependency->getName()]);

            file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
