# from v.1.x.x to v.2.0.0

## Database changes
In the beginning of Cumulus we did not know some October's and Laravel's conventions. While designing and developing Cumulus we used our own experience and ideas. During this time we get familiar with October's naming suggestions. As a consequence in version 2.0.0 we decided to change some names.

If you have used only repositories to access data, than you do not have to do anything. But if you were using anywhere in you code attributes by name, than you will have to change them as follows.

### Cluster full_name becomes name
`Full_name` from `clusters` table becomes name.

### Primary keys in Cumulus
In version 1.x.x we were using `cluster_id`, `module_id` and `plan_id` as a primary keys. From now all of them will become `id`.

### Drop modules
`initbiz_cumuluscore_modules` and `initbiz_cumuluscore_plan_module` tables will be dropped during upgrade to 2.0.0. Because of that the relation between your plans and modules will be lost. You should create a backup of `initbiz_cumuluscore_plan_modules` and `initbiz_cumuluscore_modules` if you want to restore them later.

In most cases it should be easy to restore them as modules were whole plugins, so there cannot be that many of them.

## Modules becomes features
The biggest change in Cumulus concerns modules. We noticed, that it was too small change for plugins to add the migration.

Modules become features, which has to be registered in plugin registration file (desciprion below).

### Modify modules
Before updating you have to ensure you register features as described in documentation for all of your modules. What is more, you have to remove the initial migration previously created by `create:module` command.

### `ModuleGuard` becomes `FeatureGuard`
The responsibility of `ModuleGuard` component was to ensure that plan has access to specified module and return 403 (Forbidden access) if it does not. The responsibility of `FeatureGuard` is almost the same but it checks if plan has access to any of features specified in component configuration.

### Command `create:module` removed
As a consequence the command `create:module` is removed. If you want to create something similar then create typical OctoberCMS plugin using `create:plugin` command and by adding `registerCumulusFeatures` method (details below).
