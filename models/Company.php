<?php namespace InitBiz\CumulusCore\Models;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Model;
use RainLab\User\Models\User as UserModel;

/**
 * Model
 */
class Company extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_companies';
    public $primaryKey = 'company_id';
    protected $fillable = ['full_name', 'slug'];
    public $belongsToMany = [
        'modules' => [
            Module::class,
            'table' => 'initbiz_cumuluscore_company_module',
            'key'      => 'company_id',
            'otherKey' => 'module_id'
        ]
    ];

    public $hasMany = [
        'users' => [
            UserModel::class,
            'table' => 'users',
            'key'      => 'company_id',
            'otherKey' => 'user_id'
        ]
    ];

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    public static function getMenuTypeInfo()
    {
        //TODO To consider extending automatic static menu generating
        $result = ['dynamicItems' => true];

        $theme = Theme::getActiveTheme();

        $pages = CmsPage::listInTheme($theme, true);
        $cmsPages = [];
        dd($pages);
        foreach ($pages as $page) {
            if (!$page->hasComponent('cumulusGuard')) {
                continue;
            }

            $cmsPages[] = $page;
        }
        $result['cmsPages'] = $cmsPages;
        return $result;
    }

    public static function resolveMenuItem($item, $url, $theme)
    {
        //TODO To consider extending automatic static menu generating
        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);
        $cumulusPages = [];
        foreach ($pages as $page) {
            if (!$page->hasComponent('cumulusGuard')) {
                continue;
            }
            $cumulusPages[] = $page;
        }
        $result = null;
        if (!$item->reference || !$item->cmsPage)
            return;

        $result = [
            'items' => []
        ];

        $categoryItem['isActive'] = $categoryItem['url'] == $url;
        $result['items'][] = $categoryItem;

        return $result;
    }
}

