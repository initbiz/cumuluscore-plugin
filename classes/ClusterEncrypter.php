<?php

namespace Initbiz\CumulusCore\Classes;

use Config;
use Illuminate\Encryption\Encrypter;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\ClusterKey;

/**
 * Cluster encrypter
 */
class ClusterEncrypter
{
    /**
     * Encrypter instance to locally cache
     *
     * @var Encrypter
     */
    private $encrypter;

    /**
     * Cluster that key is going to be used in the encrypter
     *
     * @var Cluster
     */
    protected $cluster;

    public function __construct($cluster = null)
    {
        if (is_null($cluster)) {
            $cluster = Helpers::getCluster();
        }

        $this->cluster = $cluster;
        $this->encrypter = $this->makeEncrypter($cluster);
    }

    /**
     * Encrypt the values using cluster's key
     *
     * @param mixed $value value to encrypt
     * @return mixed encrypted value
     */
    public function encrypt($value)
    {
        return $this->encrypter->encrypt($value);
    }

    /**
     * Decrypt the values using cluster's key
     *
     * @param mixed $value
     * @return mixed decrypted value
     */
    public function decrypt($value)
    {
        return $this->encrypter->decrypt($value);
    }

    /**
     * Make the encrypter instance for internal use
     *
     * @return Encrypter encrypter with cluster's key set
     */
    private function makeEncrypter()
    {
        if ($this->encrypter instanceof Encrypter) {
            return $this->encrypter;
        }

        $cipherKey = ClusterKey::get($this->cluster->slug);
        $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');

        return new Encrypter(hex2bin($cipherKey), $cipher);
    }

}
