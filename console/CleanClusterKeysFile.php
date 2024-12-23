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
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        $content = Storage::get($keysFilePath);

        $newContent = $this->cleanContent($content);

        $cleanKeysFilePath = $keysFilePath . '-cleaned';
        Storage::put($cleanKeysFilePath, $newContent);
    }

    public function cleanContent($content): string
    {
        $clusterSlugs = Cluster::withTrashed()->get('slug')->pluck('slug')->toArray();

        $newContent = '';
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $parts = explode('=', $line);

            $clusterSlug = trim($parts[0]);

            if (str_contains($clusterSlug, '-deleted-at-')) {
                $position = strpos($clusterSlug, '-deleted-at-');
                $clusterSlug = substr($clusterSlug, 0, $position);
            }

            if (isset($parts[1]) && in_array($clusterSlug, $clusterSlugs)) {
                $newContent .= $line . "\n";
            }
        }

        return $newContent;
    }
}
