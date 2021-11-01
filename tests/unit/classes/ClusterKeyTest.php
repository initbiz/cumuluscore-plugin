<?php

namespace Initbiz\CumulusCore\Tests\Models;

use Config;
use Storage;
use Carbon\Carbon;
use Initbiz\CumulusCore\Classes\ClusterKey;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Classes\Exceptions\CannotOverwriteKeyException;

class ClusterKeyTest extends CumulusTestCase
{
    public function testPut()
    {
        $keysFilePath = Config::get('initbiz.cumuluscore::encryption.keys_file_path');
        $key = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug', $key);

        $content = Storage::get($keysFilePath);
        $this->assertEquals($content, 'cluster-slug=' . $key . "\n");

        $this->expectException(CannotOverwriteKeyException::class);
        ClusterKey::put('cluster-slug', $key);
    }

    public function testGet()
    {
        $key = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug', $key);

        $this->assertEquals($key, ClusterKey::get('cluster-slug'));

        $key2 = '4cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug-2', $key2);

        $this->assertEquals($key, ClusterKey::get('cluster-slug'));
        $this->assertEquals($key2, ClusterKey::get('cluster-slug-2'));
        $this->assertEquals($key, ClusterKey::get('cluster-slug'));
    }

    public function testDelete()
    {
        $key = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug', $key);
        $key2 = '4cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug-2', $key2);

        ClusterKey::delete('cluster-slug');

        $this->assertEquals($key2, ClusterKey::get('cluster-slug-2'));
        $this->assertEquals('', ClusterKey::get('cluster-slug'));
    }

    public function testSoftDelete()
    {
        $key = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug', $key);
        $key2 = '4cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug-2', $key2);

        ClusterKey::softDelete('cluster-slug');

        $timestamp = Carbon::now()->format('Y-m-d-H-i');

        $this->assertEquals($key2, ClusterKey::get('cluster-slug-2'));
        $this->assertEquals($key, ClusterKey::get('cluster-slug-deleted-at-' . $timestamp));
    }

    public function testRestore()
    {
        $key = '3cc99ba0f00ab45b1526bc4c469495d6db07772659f964aa2c86c858a98932fe';
        ClusterKey::put('cluster-slug', $key);

        $timestamp = Carbon::now();
        ClusterKey::softDelete('cluster-slug', $timestamp);
        ClusterKey::restore('cluster-slug', $timestamp);

        $this->assertEquals($key, ClusterKey::get('cluster-slug'));
    }
}
