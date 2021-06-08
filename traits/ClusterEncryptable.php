<?php

namespace Initbiz\CumulusCore\Traits;

use App;
use Config;
use Exception;
use Illuminate\Encryption\Encrypter;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Classes\ClusterKey;
use Initbiz\CumulusCore\Classes\Exceptions\CannotUseClusterEncrypterException;

/**
 * Use this trait in models that you want to encrypt using the cluster's key
 * Remember that this will make the data available only in frontend,
 * when a cluster is selected and available for the user.
 */
trait ClusterEncryptable
{
    /**
     * @var array clusterEncryptable is a list of attribute names which should be encrypted
     *
     * protected $clusterEncryptable = [];
     */

    /**
     * @var array originalClusterEncryptableValues is the original attribute values
     * before they were encrypted
     */
    protected $originalClusterEncryptableValues = [];

    /**
     * Encrypter instance - local cache attribute
     *
     * @var Encrypter
     */
    private $encrypter;

    /**
     * bootClusterEncryptable boots the clusterEncryptable trait for a model
     * @return void
     */
    public static function bootClusterEncryptable()
    {
        if (!property_exists(get_called_class(), 'clusterEncryptable')) {
            throw new Exception(sprintf(
                'You must define a $clusterEncryptable property in %s to use the ClusterEncryptable trait.',
                get_called_class()
            ));
        }

        /*
         * Encrypt required fields when necessary
         */
        static::extend(function ($model) {
            $clusterEncryptable = $model->getClusterEncryptableAttributes();

            $model->bindEvent('model.beforeSetAttribute', function ($key, $value) use ($model, $clusterEncryptable) {
                if (
                    in_array($key, $clusterEncryptable) &&
                    $value !== null &&
                    $value !== ''
                ) {
                    return $model->makeClusterEncryptableValue($key, $value);
                }
            });

            $model->bindEvent('model.beforeGetAttribute', function ($key) use ($model, $clusterEncryptable) {
                if (
                    in_array($key, $clusterEncryptable) &&
                    array_get($model->attributes, $key) !== null &&
                    array_get($model->attributes, $key) !== ''
                ) {
                    return $model->getClusterEncryptableValue($key);
                }
            });
        });
    }

    /**
     * makeClusterEncryptableValue encrypts an attribute value and saves it in the original locker
     * @param  string $key   Attribute
     * @param  string $value Value to encrypt
     * @return string Encrypted value
     */
    public function makeClusterEncryptableValue($key, $value)
    {
        $encrypter = $this->makeEncrypter();

        $this->originalClusterEncryptableValues[$key] = $value;

        return $encrypter->encrypt($value);
    }

    /**
     * getClusterEncryptableValue decrypts an attribute value
     * @param  string $key Attribute
     * @return string Decrypted value
     */
    public function getClusterEncryptableValue($key)
    {
        $encrypter = $this->makeEncrypter();

        return $encrypter->decrypt($this->attributes[$key]);
    }

    /**
     * getClusterEncryptableAttributes returns a collection of fields that will be encrypted.
     * @return array
     */
    public function getClusterEncryptableAttributes()
    {
        return $this->clusterEncryptable;
    }

    /**
     * getOriginalEncryptableValues returns the original values of any encrypted attributes
     * @return array
     */
    public function getOriginalClusterEncryptableValues()
    {
        return $this->originalClusterEncryptableValues;
    }

    /**
     * getOriginalClusterEncryptableValue returns the original values of any encrypted attributes.
     * @return mixed
     */
    public function getOriginalClusterEncryptableValue($attribute)
    {
        return $this->originalClusterEncryptableValues[$attribute] ?? null;
    }

    /**
     * Make encrypter instance using the currently using cluster
     *
     * @return Encrypter
     */
    protected function makeEncrypter()
    {
        $cluster = $this->getCluster();

        if (!$cluster) {
            // If there's no cluster, we cannot get the key and encrypter as a consequence, as well
            throw new CannotUseClusterEncrypterException();
        }

        if ($this->encrypter !== null) {
            return $this->encrypter;
        }

        $cipherKey = ClusterKey::get($cluster->slug);
        $cipher = Config::get('initbiz.cumuluscore::encryption.cipher');

        $this->encrypter = new Encrypter(hex2bin($cipherKey), $cipher);

        return $this->encrypter;
    }

    /**
     * Get the cluster the user is currently in, works only in the frontend
     * @return Cluster|null
     */
    protected function getCluster()
    {
        if (!App::runningInBackend()) {
            return Helpers::getCluster();
        }
    }
}
