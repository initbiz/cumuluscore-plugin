<?php namespace Initbiz\CumulusCore\AnnouncerTypes;

use Event;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusAnnouncements\Models\Announcement;
use Initbiz\CumulusAnnouncements\Classes\AnnouncerTypeBase;

class UserRegisterAnnouncerType extends AnnouncerTypeBase
{
    public $label = 'initbiz.cumuluscore::lang.announcers.register_user';

    public function handle($announcer)
    {
        Event::listen('rainlab.user.register', function($user, $data) use ($announcer) {
            $announcement = Announcement::ofAnnouncer($announcer);
            $announcement->receiver = 'users';
            $announcement->save();
            $announcement->users()->add($user);
            $announcement->save();
        }, 50);
    }
}
