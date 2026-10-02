<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\AnnouncerTypes;

use Event;
use RainLab\User\Components\Registration;
use Initbiz\CumulusAnnouncements\Models\Announcement;
use Initbiz\CumulusAnnouncements\Classes\AnnouncerTypeBase;

class UserRegisterAnnouncerType extends AnnouncerTypeBase
{
    public $icon = 'icon-user';
    public $category = 'initbiz.cumuluscore::lang.announcers.welcome_messages';
    public $label = 'initbiz.cumuluscore::lang.announcers.register_user';

    public function handle($announcer)
    {
        Event::listen('rainlab.user.register', function ($param1, $param2) use ($announcer) {
            if ($param1 instanceof Registration) {
                $user = $param2;
            } else {
                $user = $param1;
            }

            $announcement = Announcement::ofAnnouncer($announcer);
            $announcement->receiver = 'users';
            $deferredBindingKey = \Str::password(32, true, false, false);
            $announcement->users()->add($user, $deferredBindingKey);
            $announcement->save(null, $deferredBindingKey);
        }, 50);
    }
}
