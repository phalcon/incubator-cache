<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

use Dotenv\Dotenv;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        if (defined($key)) {
            return constant($key);
        }

        return getenv($key) ?: $default;
    }
}

/**
 * Calls .env and merges the global and local configurations
 */
if (!function_exists('loadEnvironment')) {
    function loadEnvironment(string $root)
    {
        /**
         * Load local environment if it exists
         */
        (Dotenv::createImmutable(realpath(__DIR__), ".env.default"))->load();

        defineFromEnv('PATH_CACHE');
        defineFromEnv('PATH_DATA');
        defineFromEnv('PATH_OUTPUT');
        defineFromEnv('DATA_SQLITE_CACHE_NAME');
    }
}

if (!function_exists('defineFromEnv')) {
    function defineFromEnv(string $name)
    {
        if (defined($name)) {
            return;
        }

        define(
            $name,
            env($name)
        );
    }
}

/**
 * Ensures that certain folders are always ready for us.
 */
if (!function_exists('loadFolders')) {
    function loadFolders()
    {
        $folders = [
            'annotations',
            'assets',
            'cache',
            'cache/models',
            'logs',
            'session',
            'stream',
        ];

        foreach ($folders as $folder) {
            $item = outputDir('tests/' . $folder);

            if (true !== file_exists($item)) {
                mkdir($item, 0777, true);
            }
        }
    }
}

/**
 * Returns the cache folder
 */
if (!function_exists('cacheDir')) {
    function cacheDir(string $fileName = ''): string
    {
        return codecept_output_dir() . 'tests/cache/' . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('dataDir')) {
    function dataDir(string $fileName = ''): string
    {
        return codecept_data_dir() . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('logsDir')) {
    function logsDir(string $fileName = ''): string
    {
        return codecept_output_dir() . 'tests/logs/' . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('outputDir')) {
    function outputDir(string $fileName = ''): string
    {
        return codecept_output_dir() . $fileName;
    }
}

if (!function_exists('getOptionsSqlite')) {
    /**
     * Get sqlite db options
     */
    function getOptionsSqlite(): array
    {
        return [
            'dbname' => env('DATA_SQLITE_CACHE_NAME'),
        ];
    }
}
