<?php

namespace Initbiz\CumulusCore\Classes;

use Config;
use Storage;
use Illuminate\Encryption\Encrypter;
use Initbiz\CumulusCore\Classes\Exceptions\CannotOverwriteKeyException;

/**
 * Helper class to store and obtain cluster keys
 */
class ClusterKey
{
    /**
     * Append the cluster key to the keys file
     * The file will be created if not already present
     *
     * @param string $clusterSlug
     * @param string $key
     * @return void
     */
    public static function put(string $clusterSlug, string $key = null)
    {
        $keyExists = Self::get($clusterSlug);

        if ($keyExists) {
            throw new CannotOverwriteKeyException();
        }

        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');

        if (is_null($key)) {
            $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');
            $key = Encrypter::generateKey($cipher);
        }

        Storage::append($keysFilePath, $key);
    }

    /**
     * Get the key of the cluster
     *
     * @param string $clusterSlug
     * @return string cluster's key
     */
    public static function get(string $clusterSlug)
    {
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        $content = fopen(Storage::path($keysFilePath), 'r');

        $key = '';
        while (!feof($content)) {
            $line = fgets($content);
            list($slug, $key) = explode('=', $line);
            if ($slug === $clusterSlug) {
                break;
            }
        }

        fclose($content);
        return $key;
    }
}
