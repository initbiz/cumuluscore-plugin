<?php namespace Initbiz\CumulusCore\Traits;

use Initbiz\CumulusCore\Behaviors\FormComponent as FormComponentBehavior;
use Cms\Classes\Page;

trait FormComponent
{
    use ExtendedComponentBase;

    public $yamlConfig = 'config_form.yaml';
    public $fields;
    public $formElements;
    public $renderedForm;

    public function buildForm()
    {
        $this->formElements = $this->makeForm();
        $this->renderedForm = $this->renderForm();
    }

    public function makeForm($context = 'create')
    {
        $this->setConfig();
        $this->fillFromConfig();

        //render form and if context == update get values from database and fill it
        return $this->fields;
    }

    /* method rendering form using listElements build with makeList method */
    public function renderForm()
    {
        $viewPath = $this->guessViewPathFrom(FormComponentBehavior::class);
        return $this->makePartial($viewPath . '/default.htm', ['data' => $this->formElements]);
    }

    public function prepareVariables()
    {
        $this->page['renderedForm'] = $this->renderedForm;
        $this->page['title'] = $this->title;
    }

    public function onSave() {
        $this->makeForm();
        //return (var_dump($this->fields));

        $relations =[];
        foreach ($this->fields as $fieldName=> $field) {
            if (array_key_exists('relation',$field)) {
                $relations[$field['relation']] = $columnName;
            }
        }
        $columns = array_diff($columns, array_values($relations));
        $this->model->create();
        //return $this->model->save(post())?true:false;
    }
}
