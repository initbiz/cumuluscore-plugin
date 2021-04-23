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
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');

        // Create the keys file if it doesn't exist
        // ensure that the file is only readable and writable by the user who
        // run the command - probably it's the webserver user

        if (!Storage::exists($keysFilePath)) {
            Storage::put($keysFilePath, '');
            try {
                chmod($keysFilePath, '600');
            } catch (\Throwable $th) {
                trace_log('Problems with setting chmod on clusters keys file - check the permissions manually');
            }
        }

        $keyExists = Self::get($clusterSlug);

        if ($keyExists) {
            throw new CannotOverwriteKeyException();
        }

        if (is_null($key)) {
            $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');
            $key = bin2hex(Encrypter::generateKey($cipher));
        }

        Storage::prepend($keysFilePath, $clusterSlug . '=' . $key);
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
        $content = Storage::get($keysFilePath);

        $key = '';
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $parts = explode('=', $line);
            if (isset($parts[1]) && $parts[0] === $clusterSlug) {
                $key = trim($parts[1]);
                break;
            }
        }
        return $key;
    }
}
