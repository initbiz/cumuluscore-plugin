<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Classes;

use Config;
use Illuminate\Encryption\Encrypter;
use Initbiz\CumulusCore\Models\Cluster;

/**
 * Cluster encrypter
 */
class ClusterEncrypter
{
    use \October\Rain\Support\Traits\Singleton;

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

    public function init()
    {
        $this->cluster = Helpers::getCluster();
    }

    public function setCluster(Cluster $cluster): void
    {
        $this->cluster = $cluster;
    }

    /**
     * Encrypt the values using cluster's key
     *
     * @param mixed $value value to encrypt
     * @return mixed encrypted value
     */
    public function encrypt($value)
    {
        $encrypter = $this->getEncrypter();
        if (is_null($encrypter)) {
            return $value;
        }

        return $encrypter->encrypt($value);
    }

    /**
     * Decrypt the values using cluster's key
     *
     * @param mixed $value
     * @return mixed decrypted value
     */
    public function decrypt($value)
    {
        $encrypter = $this->getEncrypter();
        if (is_null($encrypter)) {
            return $value;
        }

        return $encrypter->decrypt($value);
    }

    /**
     * Get Encrypter instance, only for internal use
     * returns null, if can't resolve cluster
     *
     * @param Cluster|null $cluster
     * @return Encrypter|null
     */
    private function getEncrypter(?Cluster $cluster = null): ?Encrypter
    {
        if ($this->encrypter instanceof Encrypter) {
            return $this->encrypter;
        }

        $cluster = $this->cluster;
        if (!$cluster instanceof Cluster) {
            $cluster = Helpers::getCluster();
        }

        if (is_null($cluster)) {
            return null;
        }

        return $this->encrypter = $this->makeEncrypter($cluster);
    }

    /**
     * Make the encrypter instance for internal use
     *
     * @param Cluster $cluster to make encrypter for
     * @return Encrypter encrypter with cluster's key set
     */
    private function makeEncrypter(Cluster $cluster): Encrypter
    {
        $cipherKey = ClusterKey::get($cluster->slug);
        $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');

        return new Encrypter(hex2bin($cipherKey), strtolower($cipher));
    }
}
