<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Traits;

use Exception;
use Initbiz\CumulusCore\Classes\ClusterEncrypter;

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
    private $clusterEncrypter;

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
                    in_array($key, $clusterEncryptable, true) &&
                    $value !== null &&
                    $value !== ''
                ) {
                    return $model->makeClusterEncryptableValue($key, $value);
                }
            });

            $model->bindEvent('model.beforeGetAttribute', function ($key) use ($model, $clusterEncryptable) {
                if (
                    in_array($key, $clusterEncryptable, true) &&
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
        $clusterEncrypter = ClusterEncrypter::instance();

        $this->originalClusterEncryptableValues[$key] = $value;

        return $clusterEncrypter->encrypt($value);
    }

    /**
     * getClusterEncryptableValue decrypts an attribute value
     * @param  string $key Attribute
     * @return string Decrypted value
     */
    public function getClusterEncryptableValue($key)
    {
        $clusterEncrypter = ClusterEncrypter::instance();

        return $clusterEncrypter->decrypt($this->attributes[$key]);
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
}
