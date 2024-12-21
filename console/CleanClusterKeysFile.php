<?php

namespace Initbiz\CumulusCore\Console;

use Config;
use Storage;
use Illuminate\Console\Command;
use Initbiz\CumulusCore\Models\Cluster;

class CleanClusterKeysFile extends Command
{
    protected $name = 'cumulus:clean-keys-file';
    protected $description = 'Clean cluster_keys file from clusters that do not exist in the DB anymore';

    public function handle()
    {
        $clusterSlugs = Cluster::withTrashed()->get('slug')->pluck('slug')->toArray();

        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        $content = Storage::get($keysFilePath);

        $newContent = '';
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $parts = explode('=', $line);

            if (isset($parts[1]) && in_array(trim($parts[0]), $clusterSlugs)) {
                $newContent .= $line . "\n";
            }
        }

        $cleanKeysFilePath = $keysFilePath . '-cleaned';
        Storage::put($cleanKeysFilePath, $newContent);
    }
}
