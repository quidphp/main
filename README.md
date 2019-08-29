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
- [ArrMap](src/ArrMap.php) | Class that provides base methods to make a collection
- [ArrObj](src/ArrObj.php) | Class that implements the methods necessary for the ArrayAccess, Countable and Iterator interfaces
- [Arrs](src/Arrs.php) | Class for a collection containing a multidimensional array
- [Autoload](src/Autoload.php) | Class that provides custom autoloading logic, including alias autoloading and class within closures
- [CatchableException](src/CatchableException.php) | Class for a catchable exception
- [Com](src/Com.php) | Class that provides the logic to store positive, negative or neutral communication messages
- [Concatenator](src/Concatenator.php) | Class used to concatenate the content of many files or directories
- [Contract](src/Contract)
    - [Catchable](src/Contract/Catchable.php) | Interface to mark an exception as catchable
    - [Email](src/Contract/Email.php) | Interface to describe methods of an objet containing an email
    - [FileStorage](src/Contract/FileStorage.php) | Interface to describe a file objet which has a defined storage folder
    - [Hierarchy](src/Contract/Hierarchy.php) | Interface to describe the methods required to access the hierarchy of an objet
    - [Import](src/Contract/Import.php) | Interface to precise the methods required for being able to import and export to an objet
    - [Log](src/Contract/Log.php) | Interface to detail the methods required for implementing logging functionality to an object
    - [Meta](src/Contract/Meta.php) | Interface to describe methods making an objet a source of meta-data
    - [Queue](src/Contract/Queue.php) | Interface to detail the methods required for implementing queuing functionality to an object
    - [Session](src/Contract/Session.php) | Interface to detail the methods required for an objet to be a session storage
    - [User](src/Contract/User.php) | Interface to detail the methods required for an objet to represent a user
- [Error](src/Error.php) | Class used as a basic error handler
- [Exception](src/Exception.php) | Class for a default exception
- [Extender](src/Extender.php) | Class for a collection containing a listing of classes extending other ones
- [Extenders](src/Extenders.php) | Class for a collection containing many extender objects
- [File](src/File.php) | Class for a basic file object
    - [_csv](src/File/_csv.php) | Trait that provides methods to allow a file object to manage csv data
    - [_email](src/File/_email.php) | Trait that provides methods to permit a file object to represent an email
    - [_log](src/File/_log.php) | Trait that grants methods to allow a file object to do logging
    - [_session](src/File/_session.php) | Trait that provides methods to allow a file object to become a session storage
    - [_storage](src/File/_storage.php) | Trait that provides methods to link a file object to a specific storage directory
- [Files](src/Files.php) | Class for a collection containing many file objects
- [Flash](src/Flash.php) | Class for a collection containing flash-like data (delete on read)
- [Importer](src/Importer.php) | Class providing the logic to export data from an objet and importing into another one
- [Insensitive](src/Insensitive.php) | Class for a collection containing an insensitive array
- [Insert](src/Insert.php) | Class for a collection containing an insert-only array (cannot update)
- [Lang](src/Lang.php) | Class for a collection object containing language texts and translations
- [Localization](src/Localization.php) | Class for storing localization data, like latitude and longitude
- [Map](src/Map.php) | Class that provides more complete methods for a collection
    - [_arr](src/Map/_arr.php) | Trait that provides methods to allow advanced unidimensional array manipulation to a collection
    - [_arrs](src/Map/_arrs.php) | Trait that provides methods to allow advanced multidimensional array manipulation to a collection
    - [_basic](src/Map/_basic.php) | Trait that provides simple boolean methods to analyze the type of array within the collection
    - [_classe](src/Map/_classe.php) | Trait that grants methods to work with a collection containing fully qualified class name strings
    - [_classeObj](src/Map/_classeObj.php) | Trait that gives methods to work with a collection containing fqcn strings or objects
    - [_cloner](src/Map/_cloner.php) | Trait that provides a method to make sure the collection is cloned on every update
    - [_count](src/Map/_count.php) | Trait that provides a method to limit the length of a collection
    - [_filter](src/Map/_filter.php) | Trait that provides a method to filter a collection by providing a condition or callback
    - [_flash](src/Map/_flash.php) | Trait that grants methods for the collection to delete an entry once it has been retrieved
    - [_insensitive](src/Map/_insensitive.php) | Trait that transforms the collection from case sensitive to insensitive
    - [_map](src/Map/_map.php) | Trait that provides a method to recursively change the entries (map) by providing a callback
    - [_nav](src/Map/_nav.php) | Trait that provides methods to a collection in order to work with pagination
    - [_obj](src/Map/_obj.php) | Trait that grants methods to work with a collection containing instantiated objects
    - [_prepend](src/Map/_prepend.php) | Trait that replaces methods to make the collection prepend per default (add at the beginning)
    - [_readOnly](src/Map/_readOnly.php) | Trait that provides a simple readOnly lock to a collection
    - [_reference](src/Map/_reference.php) | Trait that permits the collection to work from a reference array source
    - [_sequential](src/Map/_sequential.php) | Trait that makes sure the keys of the collection are always sequential
    - [_sort](src/Map/_sort.php) | Trait that provides methods to change the order of entries within the collection
- [Redirection](src/Redirection.php) | Class managing a URI redirection array
- [Request](src/Request.php) | Class with methods to manage an HTTP request
- [RequestHistory](src/RequestHistory.php) | Class for a collection containing an history of requests
- [Res](src/Res.php) | Class with methods to manage a resource
- [Response](src/Response.php) | Class with methods to manage an HTTP response
- [Role](src/Role.php) | Class that provides basic logic for a role
- [Roles](src/Roles.php) | Class for a collection containing many roles
- [Root](src/Root.php) | Class extended by all others
- [Service](src/Service.php) | Class that provides basic methods to manage a third-party service
- [ServiceMailer](src/ServiceMailer.php) | Class with basic methods that needs to be extended by a mailing service
- [ServiceRequest](src/ServiceRequest.php) | Class with basic methods for a service that works with HTTP request
- [ServiceVideo](src/ServiceVideo.php) | Class with basic methods for a service that provides a video object after an HTTP request
- [Services](src/Services.php) | Class for a collection containing many services objects
- [Session](src/Session.php) | Class that implements the methods necessary for the SessionHandlerInterface and SessionUpdateTimestampHandlerInterface interfaces
- [Std](src/Std.php) | Class for a collection with a lot of used traits
- [Timeout](src/Timeout.php) | Class for a collection containing timeout-related data (used for denying an action if it already has happened too much)
- [Update](src/Update.php) | Class for a collection containing an update-only array (cannot insert after initialization)
- [Video](src/Video.php) | Class for an object representing a video with some meta-data
- [Widget](src/Widget.php) | Class that provides basic methods for a widget
    - [_calendar](src/Widget/_calendar.php) | Trait that provides all methods required to make a calendar widget
- [Xml](src/Xml.php) | Class that provides basic methods to make an XML sitemap
- [_arrObj](src/_arrObj.php) | Trait that provides methods to respect the ArrayAccess, Countable and Iterator native interfaces
- [_attr](src/_attr.php) | Trait that grants methods to work with the dynamic property attr
- [_cache](src/_cache.php) | Trait that provides methods to get or set a cached value from an object
- [_email](src/_email.php) | Trait that provides methods to use the object as an email
- [_inst](src/_inst.php) | Trait that provides methods to store an instantiated objet within a static property
- [_log](src/_log.php) | Trait that provides a required method to allow logging with the object
- [_option](src/_option.php) | Trait that grants methods to work with the dynamic property option
- [_overload](src/_overload.php) | Trait that allows a class to retrieve its overloaded version - higher up in the class hierarchy
- [_queue](src/_queue.php) | Trait that provides methods required for implementing queuing functionality to an object
- [_root](src/_root.php) | Trait that provides basic object methods and magic methods
- [_rootClone](src/_rootClone.php) | Trait that provides a default __clone magic function
- [_throw](src/_throw.php) | Trait that provides static methods to throw exception from an object