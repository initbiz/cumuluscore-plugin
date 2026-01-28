<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Models;

use Model;

/**
 * GeneralSettings Model
 */
class GeneralSettings extends Model
{
    public $implement = [
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'initbiz_cumuluscore_generalsettings';

    public $settingsFields = 'fields.yaml';
}
