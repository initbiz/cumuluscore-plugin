<?php

namespace Initbiz\CumulusCore\Classes;

use Config;
use Storage;
use Carbon\Carbon;
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

        Self::backupFile();

        try {
            Storage::append($keysFilePath, $clusterSlug . '=' . $key);
        } catch (\Throwable $th) {
            Self::restoreFile();
            throw $th;
        }
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
            if (isset($parts[1]) && trim($parts[0]) === $clusterSlug) {
                $key = trim($parts[1]);
                break;
            }
        }

        return $key;
    }

    /**
     * Mark the key of the cluster as deleted
     *
     * @param string $clusterSlug
     * @return void
     */
    public static function softDelete(string $clusterSlug, Carbon $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = Carbon::now();
        }

        $timestamp = $timestamp->format('Y-m-d-H-i');

        $key = Self::get($clusterSlug);
        Self::delete($clusterSlug);
        Self::put($clusterSlug . '-deleted-at-' . $timestamp, $key);
    }

    /**
     * Restore the key for the cluster
     *
     * @param string $clusterSlug
     * @param Carbon $timestamp
     * @return void
     */
    public static function restore(string $clusterSlug, Carbon $timestamp)
    {
        $timestamp = $timestamp->format('Y-m-d-H-i');
        $keyWithTimestamp = $clusterSlug . '-deleted-at-' . $timestamp;

        $key = Self::get($keyWithTimestamp);
        if (!empty($key)) {
            Self::delete($keyWithTimestamp);
            Self::put($clusterSlug, $key);
        }
    }

    /**
     * Delete the key of the cluster
     *
     * @param string $clusterSlug
     * @return bool status - if deleting was successful
     */
    public static function delete(string $clusterSlug)
    {
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        $content = Storage::get($keysFilePath);

        // Backup the file in case of some PHP process failure or exception
        Self::backupFile();

        try {
            $key = self::get($clusterSlug);
            $content = str_replace($clusterSlug . '=' . $key, '', $content);
            Storage::put($keysFilePath, $content);
        } catch (\Throwable $th) {
            Self::restoreFile();
            throw $th;
        }
    }

    /**
     * Get the key in binary format
     *
     * @param string $clusterSlug
     * @return string|false
     */
    public static function getBin(string $clusterSlug)
    {
        return hex2bin(Self::get($clusterSlug));
    }

    // Helpers

    /**
     * Restore the cluster keys file
     *
     * @return void
     */
    public static function restoreFile()
    {
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        Storage::copy($keysFilePath . '-backup', $keysFilePath);
    }

    /**
     * Backup the cluster keys file
     *
     * @return void
     */
    public static function backupFile()
    {
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        if (Storage::exists($keysFilePath)) {
            Storage::delete($keysFilePath . '-backup');
        }
        Storage::copy($keysFilePath, $keysFilePath . '-backup');
    }
}
