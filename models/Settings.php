<?php namespace Initbiz\CumulusCore\Models;

use Model;

class Settings extends Model
{

    public $implement = [
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'initbiz_cumulusproducts_settings';

    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'background' => 'System\Models\File'
    ];

}
