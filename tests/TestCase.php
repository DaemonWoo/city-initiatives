<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeViteManifest();
    }

    protected function fakeViteManifest(): void
    {
        $manifest = [
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js',
                'isEntry' => true,
            ],
        ];

        $pagesPath = resource_path('js/Pages');

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pagesPath)) as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.vue')) {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($pagesPath) + 1);
            $key = 'resources/js/Pages/'.str_replace(DIRECTORY_SEPARATOR, '/', $relative);

            $manifest[$key] = [
                'file' => 'assets/'.str_replace(['/', '.vue'], ['-', ''], $relative).'.js',
                'src' => $key,
                'isEntry' => true,
            ];
        }

        if (! is_dir($buildPath = public_path('build'))) {
            mkdir($buildPath, 0755, true);
        }

        file_put_contents($buildPath.'/manifest.json', json_encode($manifest));
    }
}
