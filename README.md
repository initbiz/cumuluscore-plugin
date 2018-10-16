# Cumulus Core

## Introduction
The plugin is a skeleton for building Software as a Service (SaaS) applications using OctoberCMS. Software as a Service application is (according to Wikipedia) a software licensing and delivery model in which software is licensed on a subscription basis and is centrally hosted.

It is much simpler that it looks at the first sight. It has use cases in a lot of scenarios and situations (after working with Cumulus for almost 2 years I cannot see application without it :) ).

It may be useful every time you want to create application that is restricting users' access to subpages.

Typical use cases where Cumulus may help:

* system for your clients - where company of your client can have it's private data in cloud while other clients cannot see each other's data
* system for schools - where classes can share some data and have access to some data while cannot see other classes data
* every system that supports different functionality for different plans (like "Bronze", "Silver", "Gold" etc.)

## Documentation

## TL;DR
If you just want to see what Cumulus can do for you, great place to start will be:
1. installing official [Cumulus theme](https://octobercms.com/theme/initbiz-cumulus)
1. running `php artisan cumulus:seed` command (see [Cumulus Demo]() documentation for info about the command)

After that you are ready to play with Cumulus based app with demo data seeded :)

If you want to play with your own configuration of Cumulus see documentation.

## CumulusCore extensions
**[Cumulus Announcements](https://octobercms.com/plugin/initbiz-cumulusannouncements)**
![Cumulus Announcements Icon](https://octobercms.com/storage/app/uploads/public/5b0/ed4/66c/thumb_9923_64_64_0_0_auto.png)
Notify users of your system about things that concerns them, their clusters or their plans.

**[Cumulus Plus](https://octobercms.com/plugin/initbiz-cumulusplus)**
![Cumulus Plus Icon](https://octobercms.com/storage/app/uploads/public/5b2/a0e/2d7/thumb_10080_64_64_0_0_auto.png)
Extend your Cumulus Core system with dashboard and settings pages within seconds.

### Concept
To fully understand the concept it is a good idea to watch the video here: <a href="http://cumulus.init.biz/videos">http://cumulus.init.biz/videos</a>

Working with pages in Cumulus is based on four levels of checking user privileges:

1. Public pages
1. User is logged in (using `Session` component from Rainlab.Users)
1. User can access to cluster's page (using Cumulus Guard)
1. Cluster has access to the feature (using Feature Guard)

You can use the Guards on pages, but the best approach is to create the following layouts:
* first one for public pages
* second one with `Session` component from `RainLab.UserPlus` for all pages that requires a user to be signed in
* third one with `Session` component and `CumulusGuard` component for all pages that requires a user to be signed in and to be assigned to a cluster
* Fourth, fifth and so on with `Session` component, `CumulusGuard` component and a `FeatureGuard` component for all pages that requires a user to be signed in, assigned to a cluster and the privilege for a cluster to access the feature.

## Features
Cumulus is using features to separate functionality and access for front-end users. Every plugin can register it's own features using `registerCumulusFeatures` method in plugin registration file.

The syntax is similar to registering backend permissions, so you may be familiar with.

For example:

```php
    public function registerCumulusFeatures()
    {
        return [
           'initbiz.cumulusinvoices.manage_invoices' => [
               'label' => 'initbiz.cumulusinvoices::lang.feature.manage_invoices',
               'description' => 'initbiz.cumulusinvoices::lang.feature.manage_invoices_desc',
               'tab' => 'initbiz.cumulusinvoices::lang.feature.invoices_tab',
           ]
        ];
    }
```

After regustering new features in your plugin you can run command:

```php artisan cumulus:purgefeatures```


## Rainlab.User extension
The plugin extends RainLab.User plugin and uses the same `User` model, so if you want to restrict backend admin to manage users remember that there is controller from RainLab.Users that uses the same Model and can access the same data.
