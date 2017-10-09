<?php namespace InitBiz\CumulusCore\Traits;

use Yaml;

trait ExtendedComponentBase
{
    use \System\Traits\ViewMaker;

    protected $config;

    public $model;
    public $title;

    /* param for cluster id that will be used in all components that need filtering */
    public $clusterRestricted = true;

    /* method that sets config */
    public function setConfig()
    {
        $this->config = $this->makeConfig();
    }

    /* method that parse config */
    public function makeConfig()
    {
        $viewPath = $this->guessViewPath();
        return Yaml::parseFile($viewPath . '/' . $this->yamlConfig);
    }

    /**
     * Transfers config values stored inside the $config property directly
     * on to the root object properties. If no properties are defined
     * all config will be transferred if it finds a matching property.
     * @param array $properties
     * @return void
     */
    protected function fillFromConfig($properties = null)
    {
        if ($properties === null) {
            $properties = array_keys((array)$this->config);
        }

        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->{$property} = $this->config[$property];
            }
            if ($property === 'model') {
                $this->model = new $this->config['model'];
            }
        }
    }

    public function cumulusProperties()
    {
        return [
            'clusterSlug' => [
                'title'       => 'Cluster slug',
                'description' => 'Slug of cluster that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }
}
