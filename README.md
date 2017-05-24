<p align="center"><img src="http://init.biz/storage/app/media/publiczne/cumulus.png"></p>

# Cumulus plugin
- [Introduction](#introduction)
- [How-to (guards)](#howto)
- [Future plans](#futureplans)

<a name="introduction"></a>
## Introduction
The plugin is a skeleton for building SaaS applications using OctoberCMS. Cumulus is alpha version so please do not use it in production but rather suggest features and solutions.

Its main purpose is to help developers with creating modules embedded on CMS pages and manage permissions to frontend pages. To understand the concept go ahead and watch the video series here: <a href="http://cumulus.init.biz/videos">http://cumulus.init.biz/videos</a>

The name origins from puffy clouds as it is used to build cloud-environment applications.

<a name="howto"></a>
## How-to
### Installation
The plugin requires `RainLab.UserPlus` and extends its features. For more information check the first video here: <a href="http://cumulus.init.biz/videos"> http://cumulus.init.biz/videos</a>.

### Concept
Working with pages in Cumulus is based on three levels of testing user privileges.

1. User is logged in (using mechanism from Rainlab.Users)
1. User can access to company's page (using Cumulus Guard)
1. Company has access to the module (using Module Guard)

The best approach is to create the following layouts:
* one with session component from `RainLab.UserPlus`
* second with above component and `CumulusGuard` component
* Third, 4th and so on with both above components and a `ModuleGuard` component.

### Modules
Cumulus is using modules to separate functionality and access for front-end users. Cumulus core provides managing privileges, companies and users while modules provide functionality. Modules are almost normal OctoberCMS plugins with extra functionality that helps communicating with Cumulus Core.

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
* Provide theme in marketplace that will singleclick-install whole base environment with example module and Cumulus Core ready to create new modules
