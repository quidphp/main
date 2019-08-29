# Quid\Main
[![Release](https://img.shields.io/github/v/release/quidphp/main)](https://packagist.org/packages/quidphp/main)
[![License](https://img.shields.io/github/license/quidphp/main)](https://github.com/quidphp/main/blob/master/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/quidphp/main)](https://www.php.net)
[![Style CI](https://styleci.io/repos/203668077/shield)](https://styleci.io)
[![Code Size](https://img.shields.io/github/languages/code-size/quidphp/main)](https://github.com/quidphp/main)

## About
**Quid\Main** is a mid-level library for PHP that provides a set of generic objects that can be extended to build something more specific. It is part of the [QuidPHP](https://github.com/quidphp/project) package and can also be used standalone. 

## License
**Quid\Main** is available as an open-source software under the [MIT license](LICENSE).

## Installation
**Quid\Main** can be easily installed with [Composer](https://getcomposer.org). It is available on [Packagist](https://packagist.org/packages/quidphp/main).
``` bash
$ composer require quidphp/main
```

## Requirement
**Quid\Main** requires the following:
- PHP 7.2+ with fileinfo, curl, openssl and posix

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
    - [Catchable](src/Contract/Catchable.php) | Interface to mark an exception as catchable
    - [Email](src/Contract/Email.php) | Interface to describe methods of an objet containing an email
    - [FileStorage](src/Contract/FileStorage.php) | Interface to describe a file objet which has a defined storage folder
    - [Hierarchy](src/Contract/Hierarchy.php) | Interface to describe the methods required to access the hierarchy of an objet
    - [Import](src/Contract/Import.php) | Interface to precise the methods required for being able to import and export to an objet
    - [Log](src/Contract/Log.php) | Interface to detail the methods required for implementing logging functionnality to an object
    - [Meta](src/Contract/Meta.php) | Interface to describe methods making an objet a source of meta-data
    - [Queue](src/Contract/Queue.php) | Interface to detail the methods required for implementing queuing functionnality to an object
    - [Session](src/Contract/Session.php) | Interface to detail the methods required for an objet to be a session storage
    - [User](src/Contract/User.php) | Interface to detail the methods required for an objet to represent a user
- [Error](src/Error.php)
- [Exception](src/Exception.php)
- [Extender](src/Extender.php)
- [Extenders](src/Extenders.php)
- [File](src/File.php)
    - [_csv](src/File/_csv.php) | Trait that provides methods to allow a file object to manage csv data
    - [_email](src/File/_email.php) | Trait that provides methods to permit a file object to represent an email
    - [_log](src/File/_log.php) | Trait that grants methods to allow a file object to do logging
    - [_session](src/File/_session.php) | Trait that provides methods to allow a file object to become a session storage
    - [_storage](src/File/_storage.php) | Trait that provides methods to link a file object to a specific storage directory
- [Files](src/Files.php)
- [Flash](src/Flash.php)
- [Importer](src/Importer.php)
- [Insensitive](src/Insensitive.php)
- [Insert](src/Insert.php)
- [Lang](src/Lang.php)
- [Localization](src/Localization.php)
- [Map](src/Map.php)
    - [_arr](src/Map/_arr.php) | Trait that provides methods to allow advanced unidemensionnal array manipulation to a collection
    - [_arrs](src/Map/_arrs.php) | Trait that provides methods to allow advanced multidimensional array manipulation to a collection
    - [_basic](src/Map/_basic.php) | Trait that provides simple boolean methods to analyze the type of array within the collection
    - [_classe](src/Map/_classe.php) | Trait that grants methods to work with a collection containing fully qualified class name strings
    - [_classeObj](src/Map/_classeObj.php) | Trait that gives methods to work with a collection containing fqcn strings or objects
    - [_cloner](src/Map/_cloner.php) | Trait that provides a method to make sure the collection is cloned on every update
    - [_count](src/Map/_count.php) | Trait that provides a method to limit the length of a collection
    - [_filter](src/Map/_filter.php) | Trait that provides a method to filter a collection by providing a condition or callback
    - [_flash](src/Map/_flash.php) | Trait that grands methods for the collection to delete an entry once it has been retrieved
    - [_insensitive](src/Map/_insensitive.php) | Trait that transforms the collection from case sensitive to insensitive
    - [_map](src/Map/_map.php) | Trait that provides a method to recursively change the entries (map) by providing a callback
    - [_nav](src/Map/_nav.php) | Trait that provides methods to a collection in order to work with pagination
    - [_obj](src/Map/_obj.php) | Trait that grants methods to work with a collection containing instantiated objects
    - [_prepend](src/Map/_prepend.php) | Trait that replaces methods to make the collection prepend per default (add at the beginning)
    - [_readOnly](src/Map/_readOnly.php) | Trait that provides a simple readOnly lock to a collection
    - [_reference](src/Map/_reference.php) | Trait that permits the collection to work from a reference array source
    - [_sequential](src/Map/_sequential.php) | Trait that makes sure the keys of the collection are always sequential
    - [_sort](src/Map/_sort.php) | Trait that provides methods to change the order of entries within the collection
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
    - [_calendar](src/Widget/_calendar.php) | Trait that provides all methods required to make a calendar widget
- [Xml](src/Xml.php)
- [_arrObj](src/_arrObj.php) | Trait that provides methods to respect the ArrayAccess, Countable and Iterator native interfaces
- [_attr](src/_attr.php) | Trait that grants methods to work with the dynamic property attr
- [_cache](src/_cache.php) | Trait that provides methods to get or set a cached value from an object
- [_email](src/_email.php) | Trait that provides methods to use the object as an email
- [_inst](src/_inst.php) | Trait that provide methods to store an instantiated objet within a static property
- [_log](src/_log.php) | Trait that provides a required method to allow logging with the object
- [_option](src/_option.php) | Trait that grants methods to work with the dynamic property option
- [_overload](src/_overload.php) | Trait that allows a class to retrieve its overloaded version - higher up in the class hierarchy
- [_queue](src/_queue.php) | Trait that provides methods required for implementing queuing functionnality to an object
- [_root](src/_root.php) | Trait that provides basic object methods and magic methods
- [_rootClone](src/_rootClone.php) | Trait that provides a default __clone magic function
- [_throw](src/_throw.php) | Trait that provides static methods to throw exception from an object