<?php namespace InitBiz\CumulusCore\Traits;

use InitBiz\CumulusCore\Behaviors\ListComponent as ListComponentBehavior;

trait ListComponent
{

    use ExtendedComponentBase;

    public $listConfig = 'config_list.yaml';

    protected $listElements;
    protected $renderedList;

    /* method building list of list items */
    public function makeList($sortColumn = null, $sortOrder = null, $page = null)
    {
        $this->setConfig();
        $this->fillFromConfig();

        $columns = array_keys($this->columns);
        $viewData = [];
        $viewData['sortColumn'] = null;
        $viewData['sortOrder'] = null;

        $data = $this->model->select($columns);
        if ($sortColumn) {
            $viewData['sortColumn'] = $sortColumn;
            if ($sortOrder == 'desc') {
                $data = $data->orderBy($sortColumn, 'desc');
                $viewData['sortOrder'] = 'desc';
            } else {
                $data = $data->orderBy($sortColumn);
                $viewData['sortOrder'] = 'asc';
            }
        }
        $data = $data->get()->toArray();

        $columnLabels = [];
        foreach ($columns as $column) {
            $columnLabels[$column] = $this->columns[$column]['label'];
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
    }

    protected function buildList()
    {
        $this->listElements = $this->makeList();
        $this->renderedList = $this->renderList();
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

}
