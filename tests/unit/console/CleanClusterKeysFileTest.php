<?php

namespace Initbiz\CumulusCore\Tests\Models;

use Initbiz\CumulusCore\Console\CleanClusterKeysFile;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Models\Cluster;

class CleanClusterKeysFileTest extends CumulusTestCase
{
    public function testCleanContent() {
        $oldContent = "cluster-slug=e35c79e0cbf269788aba4c80f1adf7a809fb5dd76adece9668dc296417bb106a\n";
        $oldContent .= "cluster-deleted-at-2024-12-21-12-12=e35c79e0cbf269788aba4c80f1adf7a809fb5dd76adece9668dc296417bb106a\n";
        $oldContent .= "cluster-slug-1=3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe\n";

        $cluster = new Cluster();
        $cluster->name = 'Cluster';
        $cluster->slug = 'cluster-slug-1';
        $cluster->save();

        $cluster = new Cluster();
        $cluster->name = 'Cluster';
        $cluster->slug = 'cluster';
        $cluster->save();

        $command = new CleanClusterKeysFile();

        $newContent = "cluster-deleted-at-2024-12-21-12-12=e35c79e0cbf269788aba4c80f1adf7a809fb5dd76adece9668dc296417bb106a\n";
        $newContent .= "cluster-slug-1=3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe\n";
        $this->assertEquals($newContent, $command->cleanContent($oldContent));
    }
}
