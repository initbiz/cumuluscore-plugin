<?php

namespace Initbiz\CumulusCore\AnnouncerTypes;

use Event;
use Validator;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusAnnouncements\Models\Announcement;
use Initbiz\CumulusAnnouncements\Classes\AnnouncerTypeBase;

class OnboardingUsersAnnouncerType extends AnnouncerTypeBase
{
    public $icon = 'icon-user';
    public $category = 'initbiz.cumuluscore::lang.announcers.welcome_messages';
    public $code = 'onboarding-users';
    public $label = 'initbiz.cumuluscore::lang.announcers.onboarding_users';

    public function additionalFields()
    {
        return [
            'plans' => [
                'type' => 'checkboxlist',
                'label' => 'initbiz.cumuluscore::lang.announcers.plans_label',
            ],
            'registered_days_ago' => [
                'type' => 'number',
                'label' => 'initbiz.cumuluscore::lang.announcers.registered_days_ago_label',
                'default' => 7,
            ],
        ];
    }

    public function handle($announcer)
    {
        $additionalData = $announcer->additional_data;

        if (empty($additionalData)) {
            return;
        }

        $rules = [
            'plans' => 'required|array',
            'registered_days_ago' => 'required|int',
        ];

        $validator = Validator::make($additionalData, $rules);
        if ($validator->fails()) {
            return;
        }

        Event::listen('initbiz.cumuluscore.sendOnboardingMessages', function () use ($announcer) {
            $additionalData = $announcer->additional_data;
            $daysAgo = $additionalData['registered_days_ago'];

            $dayStart = Carbon::now()->subDays($daysAgo)->setTime(0, 0, 0);
            $dayEnd = Carbon::now()->subDays($daysAgo)->setTime(23, 59, 59);
            $usersRegisteredDaysAgo = User::isActivated()
                ->where('created_at', '>=', $dayStart)
                ->where('created_at', '<=', $dayEnd)
                ->get();

            if ($usersRegisteredDaysAgo->isEmpty()) {
                return;
            }

            foreach ($usersRegisteredDaysAgo as $user) {
                $user->loadMissing(['clusters.plan']);

                $found = false;
                foreach ($user->clusters as $userCluster) {
                    if (in_array($userCluster->plan->id, $additionalData['plans'])) {
                        // For nicer messages - we're rewriting the relation with this single cluster that matches
                        $user->setRelation('clusters', collect([$userCluster]));
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    return;
                }

                $announcement = Announcement::ofAnnouncer($announcer);
                $announcement->receiver = 'users';

                $deferredBindingKey = \Str::password(32, true, false, false);
                $announcement->users()->add($user, $deferredBindingKey);
                $announcement->save(null, $deferredBindingKey);
            }
        });
    }

    public function getPlansOptions()
    {
        return Plan::all()->pluck('name', 'id');
    }
}
