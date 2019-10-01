<?php namespace Initbiz\CumulusCore\Controllers;

use Lang;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\PluginManager;
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

    /**
     * Override the parent method to permanently remove items that were once removed
     *
     * @return void
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $toForceDelete = Cluster::onlyTrashed()->whereIn('id', $checkedIds)->get();
        }
        
        $this->asExtension('ListController')->index_onDelete();
        
        if ($toForceDelete) {
            foreach ($toForceDelete as $item) {
                $item->forceDelete();
            }
            Flash::forget();
            Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        }
        
        return $this->listRefresh();
    }

    /**
     * Handler to restore softly deleted items
     *
     * @return void
     */
    public function index_onRestore() {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $toRestore = Cluster::onlyTrashed()->whereIn('id', $checkedIds)->get();
        } else {
            Flash::error(Lang::get('initbiz.cumuluscore::lang.restore.flash_empty'));
        }

        if ($toRestore) {
            foreach ($toRestore as $item) {
                $item->restore();
            }
            Flash::success(Lang::get('initbiz.cumuluscore::lang.restore.flash_success'));
        }

        return $this->listRefresh();
    }

    public function relationExtendManageWidget($widget, $field, $model)
    {
        $pluginManager = PluginManager::instance();
        if ($pluginManager->exists("Initbiz.CumulusRoles")) {
            $widgetExtender = new \Initbiz\CumulusRoles\Classes\WidgetExtender();
            $widgetExtender->relationExtendManageWidget($widget, $field, $model);
        }
    }
}
