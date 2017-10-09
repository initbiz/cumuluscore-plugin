<p align="center"><img src="http://init.biz/storage/app/media/publiczne/cumulus.png"></p>

# Cumulus plugin
- [Introduction](#introduction)
- [How-to (guards)](#howto)
- [Future plans](#futureplans)

<a name="introduction"></a>
## Introduction
The plugin is a skeleton for building SaaS applications using OctoberCMS.

Its main purpose is to help developers with managing permissions to frontend pages and boost the development of SaaS applications.

<a name="howto"></a>
## How-to

### Concept

To fully understand the concept it is a good idea to watch the videos here: <a href="http://cumulus.init.biz/videos">http://cumulus.init.biz/videos</a>

Working with pages in Cumulus is based on three levels of testing user privileges:

1. User is logged in (using component from Rainlab.Users)
1. User can access to cluster's page (using Cumulus Guard)
1. Cluster has access to the module (using Module Guard)

You can use the Guards on pages, but the best approach is to create the following layouts:
* first one for public pages
* second one with `Session` component from `RainLab.UserPlus` for all pages that requires a user to be signed in
* third one with `Session` component and `CumulusGuard` component for all pages that requires a user to be signed in and to be assigned to a cluster
* Fourth, fifth and so on with `Session` component, `CumulusGuard` component and a `ModuleGuard` component for all pages that requires a user to be signed in, assigned to a cluster and the privilege for a cluster to access the module.

### Modules
Cumulus is using modules to separate functionality and access for front-end users. Cumulus core provides managing privileges, clusters and users while modules provide functionality. Cumulus modules are normal OctoberCMS plugins with extra functionality that helps communicating with Cumulus Core.

After installing Cumulus Core you can run command:

```php artisan cumulus:createmodule <namespace>.<modulename>```

For example:

```php artisan cumulus:createmodule Initbiz.CumulusProducts```

After creating such module (witch basically is OctoberCMS plugin), you will have to run

```php artisan plugin:refresh <namespace>.<modulename>```

in order to register module in Cumulus Core.

<a name="futureplans"></a>
## Future plans

The most important future plans:
* Provide behaviors instead of traits for components to work similar to backend.
