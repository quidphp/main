# Quid\Main
[![Release](https://img.shields.io/github/v/release/quidphp/main)](https://packagist.org/packages/quidphp/main)
[![License](https://img.shields.io/github/license/quidphp/main)](https://github.com/quidphp/main/blob/master/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/quidphp/main)](https://www.php.net)
[![Style CI](https://styleci.io/repos/203668077/shield)](https://styleci.io)
[![Code Size](https://img.shields.io/github/languages/code-size/quidphp/main)](https://github.com/quidphp/main)

## About
**Quid\Main** is a mid-level library for PHP that provides a set of generic objects that can be extended to build something more specific. It is part of the QuidPHP framework and CMS and can also be used standalone. 

## License
**Quid\Main** is available as an open-source software under the [MIT license](LICENSE).

## Composer
**Quid\Main** can be installed through [Composer](https://getcomposer.org). It is available on [Packagist](https://packagist.org/packages/quidphp/main).
``` bash
$ composer require quidphp/main
```

``` php
function james(array $test):string
{
    return 'LALA';
}

james(array(2,3,4);
```

## Requirement
**Quid\Main** requires the following:
- PHP 7.2+

## Dependency
**Quid\Main** has the following dependency:
- [Quid\Base](https://github.com/quidphp/base)

## Testing
**Quid\Main** testsuite can be run by creating a new [Quid\Project](https://github.com/quidphp/project). All tests and assertions are part of the [Quid\Test](https://github.com/quidphp/test) repository.

## Comment
**Quid\Main** code is commented and all methods are explained. However, the method and property comments are currently written in French.

## Convention
**Quid\Main** is built on the following conventions:
- *Traits*: Traits filenames start with an underscore (_).
- *Interfaces*: Interfaces are stored within the Contract subdirectory.
- *Coding*: No curly braces are used in a IF statement if the condition can be resolved in only one statement.
- *Type*: Files, function arguments and return types are strict typed.
- *Dynamic*: Classes are dynamic and objects need to be instantiated, very few static methods
- *Config*: A special $config static property exists in all classes. This property gets recursively merged with the parents' property on initialization.

## Overview
**Quid\Main** contains more than 90 classes, traits and interfaces. Here is an overview:
- [ArrMap](src/ArrMap.php)
- [ArrObj](src/ArrObj.php)
- [Arrs](src/Arrs.php)
- [Autoload](src/Autoload.php)
- [CatchableException](src/CatchableException.php)
- [Com](src/Com.php)
- [Concatenator](src/Concatenator.php)
- [Contract](src/Contract)
    - [Catchable](src/Contract/Catchable.php)
    - [Email](src/Contract/Email.php)
    - [FileStorage](src/Contract/FileStorage.php)
    - [Hierarchy](src/Contract/Hierarchy.php)
    - [Import](src/Contract/Import.php)
    - [Log](src/Contract/Log.php)
    - [Meta](src/Contract/Meta.php)
    - [Queue](src/Contract/Queue.php)
    - [Session](src/Contract/Session.php)
    - [User](src/Contract/User.php)
- [Error](src/Error.php)
- [Exception](src/Exception.php)
- [Extender](src/Extender.php)
- [Extenders](src/Extenders.php)
- [File](src/File.php)
    - [_csv](src/File/_csv.php)
    - [_email](src/File/_email.php)
    - [_log](src/File/_log.php)
    - [_session](src/File/_session.php)
    - [_storage](src/File/_storage.php)
- [Files](src/Files.php)
- [Flash](src/Flash.php)
- [Importer](src/Importer.php)
- [Insensitive](src/Insensitive.php)
- [Insert](src/Insert.php)
- [Lang](src/Lang.php)
- [Localization](src/Localization.php)
- [Map](src/Map.php)
    - [_arr](src/Map/_arr.php)
    - [_arrs](src/Map/_arrs.php)
    - [_basic](src/Map/_basic.php)
    - [_classe](src/Map/_classe.php)
    - [_classeObj](src/Map/_classeObj.php)
    - [_cloner](src/Map/_cloner.php)
    - [_count](src/Map/_count.php)
    - [_filter](src/Map/_filter.php)
    - [_flash](src/Map/_flash.php)
    - [_insensitive](src/Map/_insensitive.php)
    - [_map](src/Map/_map.php)
    - [_nav](src/Map/_nav.php)
    - [_obj](src/Map/_obj.php)
    - [_prepend](src/Map/_prepend.php)
    - [_readOnly](src/Map/_readOnly.php)
    - [_reference](src/Map/_reference.php)
    - [_sequential](src/Map/_sequential.php)
    - [_sort](src/Map/_sort.php)
- [Redirection](src/Redirection.php)
- [Request](src/Request.php)
- [RequestHistory](src/RequestHistory.php)
- [Res](src/Res.php)
- [Response](src/Response.php)
- [Role](src/Role.php)
- [Roles](src/Roles.php)
- [Root](src/Root.php)
- [Service](src/Service.php)
- [ServiceMailer](src/ServiceMailer.php)
- [ServiceRequest](src/ServiceRequest.php)
- [ServiceVideo](src/ServiceVideo.php)
- [Services](src/Services.php)
- [Session](src/Session.php)
- [Std](src/Std.php)
- [Timeout](src/Timeout.php)
- [Update](src/Update.php)
- [Video](src/Video.php)
- [Widget](src/Widget.php)
    - [_calendar](src/Widget/_calendar.php)
- [Xml](src/Xml.php)
- [_arrObj](src/_arrObj.php)
- [_attr](src/_attr.php)
- [_cache](src/_cache.php)
- [_email](src/_email.php)
- [_inst](src/_inst.php)
- [_log](src/_log.php)
- [_option](src/_option.php)
- [_overload](src/_overload.php)
- [_queue](src/_queue.php)
- [_root](src/_root.php)
- [_rootClone](src/_rootClone.php)
- [_throw](src/_throw.php)
