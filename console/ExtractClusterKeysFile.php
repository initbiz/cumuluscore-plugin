<?php

namespace Initbiz\CumulusCore\Console;

use Config;
use Storage;
use Illuminate\Console\Command;
use Initbiz\CumulusCore\Classes\ClusterKey;
use Initbiz\CumulusCore\Classes\Exceptions\CannotOverwriteKeyException;

class ExtractClusterKeysFile extends Command
{
    protected $name = 'cumulus:extract-cluster-keys-file';
    protected $description = 'Extract single cluster_keys file to one file with key per one cluster';

    public function handle()
    {
        $keysFile = Config::get('initbiz.cumuluscore::encryption.keys_file_path');

        $content = Storage::get($keysFile);

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $parts = explode('=', $line);
            try {
                ClusterKey::put($parts[0], $parts[1]);
            } catch (CannotOverwriteKeyException $th) {
                // When re-running this command make it silently proceed to the remaining keys
                continue;
            }
        }
    }
}
