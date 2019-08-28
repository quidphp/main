# Quid\Main
<p align='center'>
  <a href='https://packagist.org/packages/quidphp/main'><img src='https://img.shields.io/github/v/release/quidphp/main' alt='Release' /></a>
  <a href='https://github.com/quidphp/main/blob/master/LICENSE'><img src='https://img.shields.io/github/license/quidphp/main' alt='License' /></a>
  <a href='https://www.php.net'><img src='https://img.shields.io/packagist/php-v/quidphp/main' alt='PHP Version' /></a>
  <a href='https://styleci.io'><img src='https://styleci.io/repos/203668077/shield' alt='Style CI' /></a>
  <a href='https://github.com/quidphp/main'><img src='https://img.shields.io/github/languages/code-size/quidphp/main' alt='Code Size' /></a>
</p>

## About
**Quid\Main** is a mid-level library for PHP that provides a set of generic objects that can be extended to build something more specific. It is part of the QuidPHP framework and CMS but it can be used standalone. 

## License
**Quid\Main** is available as an open-source software under the [MIT license](LICENSE).

## Composer
**Quid\Main** can be installed through [Composer](https://getcomposer.org). It is available on [Packagist](https://packagist.org/packages/quidphp/main).
``` bash
$ composer require quidphp/main
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
- *Filename*: Traits filenames start with an underscore (_), interfaces are stored within a Contract subdirectory.
- *Coding*: No curly braces are used in a IF statement if the condition can be resolved in only one statement.
- *Type*: Files, function arguments and return types are strict typed.
- *Dynamic*: Classes are dynamic and objects need to be instantiated, very few static methods
- *Config*: A special $config static property exists in all classes. This property gets recursively merged with the parents' property on initialization.

## Overview
**Quid\Main** contains more than 90 classes, traits and interfaces. Here is an overview:
- [ArrMap](src/ArrMap.php) | ArrMap
- [ArrObj](src/ArrObj.php) | ArrObj
- [Arrs](src/Arrs.php) | Arrs
- [Autoload](src/Autoload.php) | Autoload
- [CatchableException](src/CatchableException.php) | CatchableException
- [Com](src/Com.php) | Com
- [Concatenator](src/Concatenator.php) | Concatenator
    - [Catchable](src/Contract/Catchable.php) | Catchable
    - [Email](src/Contract/Email.php) | Email
    - [FileStorage](src/Contract/FileStorage.php) | FileStorage
    - [Hierarchy](src/Contract/Hierarchy.php) | Hierarchy
    - [Import](src/Contract/Import.php) | Import
    - [Log](src/Contract/Log.php) | Log
    - [Meta](src/Contract/Meta.php) | Meta
    - [Queue](src/Contract/Queue.php) | Queue
    - [Session](src/Contract/Session.php) | Session
    - [User](src/Contract/User.php) | User
- [Error](src/Error.php) | Error
- [Exception](src/Exception.php) | Exception
- [Extender](src/Extender.php) | Extender
- [Extenders](src/Extenders.php) | Extenders
- [File](src/File.php) | File
    - [_csv](src/File/_csv.php) | _csv
    - [_email](src/File/_email.php) | _email
    - [_log](src/File/_log.php) | _log
    - [_session](src/File/_session.php) | _session
    - [_storage](src/File/_storage.php) | _storage
- [Files](src/Files.php) | Files
- [Flash](src/Flash.php) | Flash
- [Importer](src/Importer.php) | Importer
- [Insensitive](src/Insensitive.php) | Insensitive
- [Insert](src/Insert.php) | Insert
- [Lang](src/Lang.php) | Lang
- [Localization](src/Localization.php) | Localization
- [Map](src/Map.php) | Map
    - [_arr](src/Map/_arr.php) | _arr
    - [_arrs](src/Map/_arrs.php) | _arrs
    - [_basic](src/Map/_basic.php) | _basic
    - [_classe](src/Map/_classe.php) | _classe
    - [_classeObj](src/Map/_classeObj.php) | _classeObj
    - [_cloner](src/Map/_cloner.php) | _cloner
    - [_count](src/Map/_count.php) | _count
    - [_filter](src/Map/_filter.php) | _filter
    - [_flash](src/Map/_flash.php) | _flash
    - [_insensitive](src/Map/_insensitive.php) | _insensitive
    - [_map](src/Map/_map.php) | _map
    - [_nav](src/Map/_nav.php) | _nav
    - [_obj](src/Map/_obj.php) | _obj
    - [_prepend](src/Map/_prepend.php) | _prepend
    - [_readOnly](src/Map/_readOnly.php) | _readOnly
    - [_reference](src/Map/_reference.php) | _reference
    - [_sequential](src/Map/_sequential.php) | _sequential
    - [_sort](src/Map/_sort.php) | _sort
- [Redirection](src/Redirection.php) | Redirection
- [Request](src/Request.php) | Request
- [RequestHistory](src/RequestHistory.php) | RequestHistory
- [Res](src/Res.php) | Res
- [Response](src/Response.php) | Response
- [Role](src/Role.php) | Role
- [Roles](src/Roles.php) | Roles
- [Root](src/Root.php) | Root
- [Service](src/Service.php) | Service
- [ServiceMailer](src/ServiceMailer.php) | ServiceMailer
- [ServiceRequest](src/ServiceRequest.php) | ServiceRequest
- [ServiceVideo](src/ServiceVideo.php) | ServiceVideo
- [Services](src/Services.php) | Services
- [Session](src/Session.php) | Session
- [Std](src/Std.php) | Std
- [Timeout](src/Timeout.php) | Timeout
- [Update](src/Update.php) | Update
- [Video](src/Video.php) | Video
- [Widget](src/Widget.php) | Widget
    - [_calendar](src/Widget/_calendar.php) | _calendar
- [Xml](src/Xml.php) | Xml
- [_arrObj](src/_arrObj.php) | _arrObj
- [_attr](src/_attr.php) | _attr
- [_cache](src/_cache.php) | _cache
- [_email](src/_email.php) | _email
- [_inst](src/_inst.php) | _inst
- [_log](src/_log.php) | _log
- [_option](src/_option.php) | _option
- [_overload](src/_overload.php) | _overload
- [_queue](src/_queue.php) | _queue
- [_root](src/_root.php) | _root
- [_rootClone](src/_rootClone.php) | _rootClone
- [_throw](src/_throw.php) | _throw