<?php namespace Initbiz\CumulusCore\Controllers;

use Lang;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Initbiz\CumulusCore\Models\Cluster;

class Clusters extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['initbiz.cumuluscore.access_clusters'];

    /**
     * @var string HTML body tag class
     */
    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-clusters');
    }

    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $toForceDelete = Cluster::onlyTrashed()->whereIn('id', $checkedIds)->get();
        }
        
        $result = $this->asExtension('ListController')->index_onDelete();
        
        if ($toForceDelete) {
            foreach ($toForceDelete as $item) {
                $item->forceDelete();
            }
            Flash::forget();
            Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        }
        
        return $result;
    }
}
