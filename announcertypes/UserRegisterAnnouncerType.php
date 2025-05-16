<?php

namespace Initbiz\CumulusCore\AnnouncerTypes;

use Event;
use Initbiz\CumulusAnnouncements\Models\Announcement;
use Initbiz\CumulusAnnouncements\Classes\AnnouncerTypeBase;

class UserRegisterAnnouncerType extends AnnouncerTypeBase
{
    public $icon = 'icon-user';
    public $category = 'initbiz.cumuluscore::lang.announcers.welcome_messages';
    public $label = 'initbiz.cumuluscore::lang.announcers.register_user';

    public function handle($announcer)
    {
        Event::listen('rainlab.user.register', function ($user, $data) use ($announcer) {
            $announcement = Announcement::ofAnnouncer($announcer);
            $announcement->receiver = 'users';
            $deferredBindingKey = \Str::password(32, true, false, false);
            $announcement->users()->add($user, $deferredBindingKey);
            $announcement->save(null, $deferredBindingKey);
        }, 50);
    }
}
