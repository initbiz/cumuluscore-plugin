<?php namespace InitBiz\CumulusCore\Traits;

use InitBiz\CumulusCore\Behaviors\ListComponent as ListComponentBehavior;
use Cms\Classes\Page;
use InitBiz\CumulusCore\Classes\Helpers;

trait ListComponent
{

    use ExtendedComponentBase;

    public $yamlConfig = 'config_list.yaml';
    public $columns;

    protected $listElements;
    protected $renderedList;

    /* method building list of list items */
    public function makeList($sortColumn = null, $sortOrder = null, $page = null)
    {
        $this->setConfig();
        $this->fillFromConfig();

        $columns = array_keys($this->columns);
        $viewData = [];
        $viewData['updatePageUrl'] = $this->property('updatePage');
        $viewData['createPageUrl'] = $this->property('createPage');
        $viewData['sortColumn'] = null;
        $viewData['sortOrder'] = null;

        $data = $this->model;
        $relations = [];

        //Eager loading definition for model
        foreach ($this->columns as $columnName => $column) {
            if (array_key_exists('relation',$column)) {
                //$data = $data->with(array_values($column));
                $relations[$column['relation']] = $columnName;
            }
        }
        $columns = array_diff($columns, array_values($relations));

        // select all columns except those in relations
        $data = $data->select($columns);

        //Company restrictions by company slug in URL
        if ($this->companyRestricted) {
            $companySlug = $this->param('company');
            $data = $data->whereHas('company', function ($query) use ($companySlug) {
                $query->where('slug', $companySlug);
            });
        }

        $data = $data->get();

        // adding relations to data
        $relationData = [];
        foreach ($data as $model) {
            foreach ($relations as $relationName => $columnName) {
                $relationData[] = [$columnName => $model->$relationName()->first()->$columnName];
            }
        }
        $data = $data->toArray();

        if(!empty($relationData)) {
            foreach ($data as &$row) {
                dd($relationData);
                $row += array_shift($relationData);
            }
        }

        $columnLabels = [];
        $columns += $relations;
        foreach ($columns as $column) {
            $columnLabels[$column] = $this->columns[$column]['label'];
        }

        //var_dump($data);
        //sorting order
        if ($sortColumn) {
            $viewData['sortColumn'] = $sortColumn;
            if ($sortOrder == 'desc') {
                $this->array_sort_by_column($data, $sortColumn, SORT_DESC);
                $viewData['sortOrder'] = 'desc';
            } else {
                $this->array_sort_by_column($data, $sortColumn);
                $viewData['sortOrder'] = 'asc';
            }
        }

        return ['head' => $columnLabels, 'body' => $data, 'viewData' => $viewData];
    }

    /* method rendering list using listElements build with makeList method */
    public function renderList()
    {
        $viewPath = $this->guessViewPathFrom(ListComponentBehavior::class);
        return $this->makePartial($viewPath . '/default.htm', ['data' => $this->listElements]);
    }

    public function prepareVariables()
    {
        $this->page['listElements'] = $this->listElements;
        $this->page['renderedList'] = $this->renderedList;
        $this->page['title'] = $this->title;
        $this->page['updatePageFileName'] = Page::all()[$this->property('updatePage').'.htm']
                                        ->getAttributes()['fileName'];
    }

    public function buildList()
    {
        $this->listElements = $this->makeList();
        $this->renderedList = $this->renderList();
    }

    protected function listProperties()
    {
        return [
            'updatePage' => [
                'page'        => 'Page to update record',
                'description' => 'Pick the page where records update component is embedded',
                'type'        => 'dropdown'
            ],
            'createPage' => [
                'page'        => 'Page to create record',
                'description' => 'Pick the page where records create component is embedded',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function getUpdatePageOptions()
    {
        return Helpers::getPagesFilenames();
        //return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getCreatePageOptions(){
        return Helpers::getPagesFilenames();
    }


    /* AJAX handlers for list */

    public function onSort()
    {
        $previousColumn = post('previousSortColumn');
        $sortOrder = post('sortOrder');

        if ($column = post('sortColumn')) {
            $this->setConfig();
            $this->fillFromConfig();

            if ($column == $previousColumn) {
                if ($sortOrder == 'asc') {
                    $this->listElements = $this->makeList($column, 'desc');
                } else {
                    $this->listElements = $this->makeList($column, 'asc');
                }
            } else {
                $this->listElements = $this->makeList($column);
            }
            $this->renderedList = $this->renderList();
            return $this->onRefresh();
        }
    }

    /**
     * Event handler for refreshing the list.
     */
    public function onRefresh()
    {
        $viewPath = $this->guessViewPathFrom(ListComponentBehavior::class);
        $this->prepareVariables();
        return ['#lista' => $this->makePartial($viewPath . '/default.htm',
            ['data' => $this->listElements])];
    }

    private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

}
