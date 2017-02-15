<?php namespace InitBiz\CumulusCore\Console;

use October\Rain\Scaffold\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateModule extends GeneratorCommand
{
    /**
     * @var string The console command name.
     */
    protected $name = 'cumulus:createmodule';
    private $moduleName;
    private $moduleAuthor;
    /**
     * @var string The console command description.
     */
    protected $description = 'Creating new cumulus module structure.';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'stubs/createModule/lang/en/lang.stub' => 'lang/en/lang.php',
        'stubs/createModule/classes/Guard.stub' => 'classes/Guard.php',
        'stubs/createModule/components/ModuleGuard.stub' => 'components/{{name}}Guard.php',
        'stubs/createModule/Plugin.stub' => 'Plugin.php',
        'stubs/createModule/PluginYaml.stub' => 'plugin.yaml',
        'stubs/createModule/updates/register_initbiz_cumulus_module.stub' => 'updates/register_initbiz_cumulus_module.php',
        'stubs/createModule/updates/versionYaml.stub' => 'updates/version.yaml',
    ];

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $this->vars = $this->processVars($this->prepareVars());

        $this->makeStubs();

        $this->info('Cumulus module created successfully.');
    }


    /**
     * Prepare variables for stubs.
     *
     */
    protected function prepareVars()
    {
        $moduleCode = $this->argument('plugin');

        $parts = explode('.', $moduleCode);
        $this->moduleName = array_pop($parts);
        $this->moduleAuthor = array_pop($parts);

        return [
            'name' => $this->moduleName,
            'author' => $this->moduleAuthor,
            'componentAlias' => lcfirst($this->moduleName)
        ];
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of the module to create. Eg: InITbiz.CumulusProducts'],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite existing files with generated ones.'],
        ];
    }

}
