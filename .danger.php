<?php

declare(strict_types=1);

use Initbiz\Linter\Classes\DangerConfigMaker;

include_once 'linter-plugin/classes/DangerConfigMaker.php';
$configMaker = new DangerConfigMaker();
// Enable rules below
$configMaker->enableRule('linter-plugin/dangerrules/VersionYamlUpdatedRule.php');
return $configMaker->getConfig();
