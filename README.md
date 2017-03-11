<p align="center"><img src="http://init.biz/storage/app/media/publiczne/cumulus.png"></p>

# Cumulus plugin
- [Introduction](#introduction)
- [How-to (guards)](#howto)
- [Future plans](#futureplans)

<a name="introduction"></a>
## Introduction
The plugin is a skeleton for building SaaS applications using OctoberCMS. Cumulus is still in pre-alpha so please do not use it in production but rather suggest features and solutions :)

Its main purpose is to ease companies (developers) creating modules and manage permissions to frontend pages. To understand the concept go ahead and watch the video series here: <a href="http://cumulus.init.biz/videos">http://cumulus.init.biz/videos</a>

The name origins from puffy clouds as it is used to build applications in cloud environments.

<a name="howto"></a>
## How-to
### Installation
The plugin requires `RainLab.UserPlus` and extends its features. For more information check the first video here: <a href="http://cumulus.init.biz/videos"> http://cumulus.init.biz/videos</a>.

### Concept
Working with pages in Cumulus is based on three levels of testing user privileges.

1. User is logged in
1. User can access to company's page
1. Company has access to the module

The most useful solution is to create two layouts:
* one with session component from `RainLab.UserPlus`
* second with session component and `CumulusGuard` component

Third, 4th and so on with a module guards.

### Modules
In Cumulus all is about modules. Cumulus core provides managing privileges, companies and users while modules provide functionality. Modules are normal OctoberCMS plugins but have some things that helps communicating with Cumulus Core.

After installing Cumulus Core you can run command:

```php artisan cumulus:createmodule <namespace>.<modulename>```

For example:

```php artisan cumulus:createmodule InitBiz.CumulusProducts```

After creating such module (witch basically is OctoberCMS plugin), you will have to run

```php artisan plugin:refresh <namespace>.<modulename>```

in order to register module in Cumulus Core.

<a name="futureplans"></a>
## Future plans
It is still experimental plugin but we hope it has potential.

The most important future plans:
* Complete the documentation (a lot of features are still not documented)
* Change registering menu items to component menuItem to be set on page where menu entry will be shown
* Change moduleGuards to one moduleGuard in CumulusCore with dropdown in Inspector
* Provide theme in marketplace that will singleclick-install whole base environment with example module and Cumulus Core ready to create new modules
