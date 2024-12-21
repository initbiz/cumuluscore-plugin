<?php

namespace Initbiz\CumulusCore\Classes;

use App;
use Config;
use Storage;
use Carbon\Carbon;
use Illuminate\Encryption\Encrypter;
use Initbiz\CumulusCore\Classes\Exceptions\CannotRestoreKeyException;
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
    public static function put(string $clusterSlug, string $key = null): void
    {
        $currentKey = Self::get($clusterSlug);

        if (!empty($currentKey)) {
            throw new CannotOverwriteKeyException();
        }

        if (is_null($key)) {
            $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');
            $key = bin2hex(Encrypter::generateKey($cipher));
        }

        $keyPath = Self::keysPath($clusterSlug);
        Storage::put($keyPath, $key);

        try {
            chmod($keyPath, '600');
        } catch (\Throwable $th) {
            trace_log('Problems with setting chmod on clusters keys directory - set permissions manually');
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
        $keyPath = Self::keysPath($clusterSlug);
        return Storage::get($keyPath);
    }

    /**
     * Mark the key of the cluster as deleted
     *
     * @param string $clusterSlug
     * @return void
     */
    public static function softDelete(string $clusterSlug, Carbon $timestamp = null)
    {
        $key = Self::get($clusterSlug);

        if (empty($key)) {
            return;
        }

        if (is_null($timestamp)) {
            $timestamp = Carbon::now();
        }

        $withTimestamp = $clusterSlug . '-deleted-at-' . $timestamp->format('Y-m-d-H-i');
        Storage::move(Self::keysPath($clusterSlug), Self::keysPath($withTimestamp));
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
        $withTimestamp = $clusterSlug . '-deleted-at-' . $timestamp->format('Y-m-d-H-i');

        $key = Self::get($withTimestamp);
        if (empty($key)) {
            throw new CannotRestoreKeyException("Couldn't find key to restore: " . $withTimestamp);
        }

        $key = Self::get($clusterSlug);
        if (!empty($key)) {
            throw new CannotRestoreKeyException("Key name already taken when restoring key: " . $withTimestamp);
        }

        Storage::move(Self::keysPath($withTimestamp), Self::keysPath($clusterSlug));
    }

    /**
     * Delete the key of the cluster
     *
     * @param string $clusterSlug
     * @return bool status - if deleting was successful
     */
    public static function delete(string $clusterSlug)
    {
        $keyPath = Self::keysPath($clusterSlug);
        return Storage::delete($keyPath);
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

    /**
     * Get path there keys are stored
     *
     * @param string $file to append to the path if provided
     * @return string
     */
    public static function keysPath(string $file = ''): string
    {
        $keysDir = Config::get('initbiz.cumuluscore::encryption.keys_dir');

        // Checking the file for backward compatibility
        $keysFile = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        if ($keysFile !== 'cluster_keys') {
            $parts = explode('/', $keysFile);
            if (count($parts) > 1) {
                array_pop($parts);
                $keysDir = implode('/', $parts);
            }
        }

        if (App::runningUnitTests()) {
            $keysDir .= '.testing';
        }

        // Create the keys dir if it doesn't exist
        // ensure that the directory is only readable and writable by the user who
        // runs the command - probably the webserver user

        if (!Storage::exists($keysDir)) {
            Storage::makeDirectory($keysDir);
            try {
                chmod($keysDir, '700');
            } catch (\Throwable $th) {
                trace_log('Problems with setting chmod on clusters keys directory - set permissions manually');
            }
        }

        if (!empty($file)) {
            $file = ltrim($file, '/');
            $keysDir .= '/' . $file;
        }

        return $keysDir;
    }
}
