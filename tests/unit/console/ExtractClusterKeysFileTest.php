<?php

namespace Initbiz\CumulusCore\Tests\Models;

use Config;
use Storage;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Console\ExtractClusterKeysFile;

class ExtractClusterKeysFileTest extends CumulusTestCase
{
    public function testHandle() {
        $filename =  'keys/some_cluster_keys_file';
        Config::set('initbiz.cumuluscore::encryption.keys_file_path', $filename);

        $key1 = 'e35c79e0cbf269788aba4c80f1adf7a809fb5dd76adece9668dc296417bb106a';
        $key2 = 'e35c79e0cbf269788aba4c80f1adf7a809fb5dd76adece9668dc296417bb106a';
        $key3 = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';

        $content = "cluster-slug=$key1\n";
        $content .= "cluster-deleted-at-2024-12-21-12-12=$key2\n";
        $content .= "cluster-slug-1=$key3\n";
        Storage::put($filename, $content);

        $command = new ExtractClusterKeysFile();
        $command->handle();

        $this->assertEquals(3, count(Storage::allFiles('keys.testing')));
        $this->assertEquals($key1, Storage::get('keys.testing/cluster-slug'));
    }
}
