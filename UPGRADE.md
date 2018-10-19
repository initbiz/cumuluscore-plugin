# from v.1.x.x to v.2.0.0
It is big. I know. It is funny in technology that after you create something it does not make sense after you work with it for some time. This is what happened to modules and some conventions we used in versions 1.x.x. Sorry about the amount of changes, but we hope our plugin will be much better and usable after the upgrade.

## Database changes
In the beginning of Cumulus we did not know some October's and Laravel's conventions. While designing and developing Cumulus we used our own experience and ideas. During this time we get familiar with October's naming suggestions. As a consequence in version 2.0.0 we decided to change a few names.

### Cluster full_name becomes name
`Full_name` from `clusters` table becomes name.

### Primary keys in Cumulus
In version 1.x.x we were using `cluster_id`, `module_id` and `plan_id` as a primary keys. From now all of them will become `id`.

### Drop modules
`initbiz_cumuluscore_modules` and `initbiz_cumuluscore_plan_module` tables will be dropped during upgrade to 2.0.0. Because of that the relation between your plans and modules will be lost. You should create a backup of `initbiz_cumuluscore_plan_modules` and `initbiz_cumuluscore_modules` if you want to review them after upgrade.

In most cases it should be easy to restore them as modules were whole plugins. Only plans and their relations with modules have to be restored in feature convention.

## Modules becomes features
The biggest change in Cumulus v.2 concerns modules. We noticed, that it was not enough for plugin to register only one feature (since modules were actually features of system). This leaded us to plugin registration file, where now plugins can register as many features as they want to (more info in documentation).

Methods from `ClusterRepository` that concerns modules will right now use features. It applies to almost every "module" word in methods and attributes names. What is more modules used slugs while features use codes. So every time where we were talking about module slug, right now it is about feature code.

### Modify modules
Before updating to v.2 **you will have to ensure you register features** as described in documentation for all of your modules.

What is more, **you have to remove the initial migration** previously created by `create:module` command:
1. remove file named `register_initbiz_cumulus_module.php`
1. remove line running it in `version.yaml` file (at the beginning)

### `ModuleGuard` becomes `FeatureGuard`
The responsibility of `ModuleGuard` component was to ensure that plan has access to specified module and return 403 (Forbidden access) if it does not. The responsibility of `FeatureGuard` is the same but it checks if plan has access to any of features specified in component configuration.

**Access to only one feature is enough to enter the page.**

### Command `create:module` removed
As a consequence the command `create:module` is removed. If you want to create something similar then create normal OctoberCMS plugin using `create:plugin` command and by adding `registerCumulusFeatures` method (details in documentation).

## `Settings` model becomes `AutoAssignSettings`
If you have used `Settings` model somewhere in your code than you will have to change its name to `AutoAssignSettings`.

Because of that you will have to reconfigure autoassign in settings or update `initbiz_cumuluscore_settings` row code to `initbiz_cumuluscore_autoassignsettings` in `system_settings` table.

## `Menu` and `MenuItem` components removed
From version 2.0.0 we decided to use [RainLab.Pages](https://octobercms.com/plugin/rainlab-pages) to build menus. It is powerful, supported and extendable way to build menus.

## Cumulus Plus users
If you are using Cumulus Plus extension make sure you change permissions from module name to feature code in "permissions".
