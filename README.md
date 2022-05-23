# QuidPHP/Main
[![Release](https://img.shields.io/github/v/release/quidphp/main)](https://packagist.org/packages/quidphp/main)
[![License](https://img.shields.io/github/license/quidphp/main)](https://github.com/quidphp/main/blob/master/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/quidphp/main)](https://www.php.net)
[![Style CI](https://styleci.io/repos/203668077/shield)](https://styleci.io)
[![Code Size](https://img.shields.io/github/languages/code-size/quidphp/main)](https://github.com/quidphp/main)

## About
**QuidPHP/Main** is a PHP library that provides a set of base objects and collections that can be extended to build something more specific. It is part of the [QuidPHP](https://github.com/quidphp/project) package and can also be used standalone. 

## License
**QuidPHP/Main** is available as an open-source software under the [MIT license](LICENSE).

## Documentation
**QuidPHP/Main** documentation is available at [QuidPHP/Docs](https://github.com/quidphp/docs).

## Installation
**QuidPHP/Main** can be easily installed with [Composer](https://getcomposer.org). It is available on [Packagist](https://packagist.org/packages/quidphp/main).
``` bash
$ composer require quidphp/main
```
Once installed, the **Quid\Main** namespace will be available within your PHP application.

## Requirement
**QuidPHP/Main** requires the following:
- PHP 8.1
- All requirements of [quidphp/base](https://github.com/quidphp/base)

## Dependency
**QuidPHP/Main** has the following dependency:
- [quidphp/base](https://github.com/quidphp/base) - Quid\Base - PHP library that provides a set of low-level static methods

The dependency will be resolved by using the [Composer](https://getcomposer.org) installation process.

## Comment
**QuidPHP/Main** code is commented and all methods are explained (in French).

## Convention
**QuidPHP/Main** is built on the following conventions:
- *Coding*: No curly braces are used in a IF statement if the condition can be resolved in only one statement.
- *Config*: A special $config static property exists in all classes. This property gets recursively merged with the parents' property on initialization.
- *Dynamic*: Classes are dynamic and objects need to be instantiated, very few static methods
- *Interfaces*: Interfaces are stored within the Contract subdirectory.
- *Traits*: Traits filenames start with an underscore (_).
- *Type*: Files, function arguments and return types are strict typed.

## Overview
**QuidPHP/Main** contains 118 classes, traits and interfaces. Here is an overview:
- [ArrMap](src/ArrMap.php) - Abstract class that provides base methods to make a collection
- [ArrObj](src/ArrObj.php) - Abstract class that implements the methods necessary for the ArrayAccess, Countable and Iterator interfaces
- [Arrs](src/Arrs.php) - Class for a collection containing a multidimensional array
- [Autoload](src/Autoload.php) - Class that provides custom autoloading logic, including alias autoloading and class within closures
- [Calendar](src/Calendar.php) - Class that provides logic for the calendar widget
- [Cart](src/Cart.php) - Class used to to manage a cart containing different items
- [CatchableException](src/CatchableException.php) - Class for a catchable exception
- [Com](src/Com.php) - Class that provides the logic to store positive, negative or neutral communication messages
- [Concatenator](src/Concatenator.php) - Class used to concatenate the content of many files or directories
- [Contract](src/Contract)
    - [Cache](src/Contract/Cache.php) - Interface to detail the methods required for implementing caching functionality to an object
    - [Catchable](src/Contract/Catchable.php) - Interface to mark an exception as catchable
    - [Email](src/Contract/Email.php) - Interface to describe methods of an objet containing an email
    - [FileStorage](src/Contract/FileStorage.php) - Interface to describe a file objet which has a defined storage folder
    - [Hierarchy](src/Contract/Hierarchy.php) - Interface to describe the methods required to access the hierarchy of an objet
    - [Import](src/Contract/Import.php) - Interface to precise the methods required for being able to import and export to an objet
    - [Log](src/Contract/Log.php) - Interface to detail the methods required for implementing logging functionality to an object
    - [Meta](src/Contract/Meta.php) - Interface to describe methods making an objet a source of meta-data
    - [Queue](src/Contract/Queue.php) - Interface to detail the methods required for implementing queuing functionality to an object
    - [Session](src/Contract/Session.php) - Interface to detail the methods required for an objet to be a session storage
- [Error](src/Error.php) - Class used as a basic error handler
- [Exception](src/Exception.php) - Class for a default exception
- [Extender](src/Extender.php) - Class for a collection containing a list of classes extending others
- [Extenders](src/Extenders.php) - Class for a collection containing many extender objects
- [File](src/File.php) - Class for a basic file object
    - [Audio](src/File/Audio.php) - Class for an audio file (like mp3)
    - [Binary](src/File/Binary.php) - Abstract class for a binary file
    - [Cache](src/File/Cache.php) - Class for a cache storage file
    - [Calendar](src/File/Calendar.php) - Class for a calendar file (like ics)
    - [Css](src/File/Css.php) - Class for a css or scss file
    - [Csv](src/File/Csv.php) - Class for a csv file
    - [Doc](src/File/Doc.php) - Class for a doc file, like microsoft word
    - [Dump](src/File/Dump.php) - Class for file which contains an exported value (similar to var_export)
    - [Email](src/File/Email.php) - Class for a file which is an email (in json format)
    - [Error](src/File/Error.php) - Class for an error storage file
    - [Font](src/File/Font.php) - Class for a font file (like ttf)
    - [Html](src/File/Html.php) - Class for an html file
    - [Image](src/File/Image.php) - Abstract class for an image file (raster or vector)
    - [ImageRaster](src/File/ImageRaster.php) - Class for a pixelated image file
    - [ImageVector](src/File/ImageVector.php) - Class for a vector image file (like svg)
    - [Js](src/File/Js.php) - Class for a js file
    - [Json](src/File/Json.php) - Class for a json file
    - [Log](src/File/Log.php) - Class for a log storage file
    - [Pdf](src/File/Pdf.php) - Class for pdf file
    - [Php](src/File/Php.php) - Class for a php file
    - [Queue](src/File/Queue.php) - Class for a queue storage file
    - [Serialize](src/File/Serialize.php) - Class for a file with content that should be serialized
    - [Session](src/File/Session.php) - Class for a session storage file, which is serialized
    - [Text](src/File/Text.php) - Abstract class for a text file
    - [Txt](src/File/Txt.php) - Class for txt file (like txt)
    - [Video](src/File/Video.php) - Class for a video file (like mp4)
    - [Xml](src/File/Xml.php) - Class for an xml file
    - [Zip](src/File/Zip.php) - Class for a zip file
    - [_concatenate](src/File/_concatenate.php) - Trait with methods to concatenate files and directories
    - [_log](src/File/_log.php) - Trait that grants methods to allow a file object to do logging
    - [_storage](src/File/_storage.php) - Trait that provides methods to link a file object to a specific storage directory
- [Files](src/Files.php) - Class for a collection containing many file objects
- [Flash](src/Flash.php) - Class for a collection containing flash-like data (delete on read)
- [Importer](src/Importer.php) - Class providing the logic to export data from an objet and importing into another one
- [Insensitive](src/Insensitive.php) - Class for a collection containing an insensitive array
- [Insert](src/Insert.php) - Class for a collection containing an insert-only array (cannot update)
- [Lang](src/Lang.php) - Class for a collection object containing language texts and translations
    - [En](src/Lang/En.php) - English language content used by this namespace
    - [Fr](src/Lang/Fr.php) - French language content used by this namespace
    - [_overload](src/Lang/_overload.php) - Trait which implements the overload logic for the lang classes
- [Localization](src/Localization.php) - Class for storing localization data, like latitude and longitude
- [Map](src/Map.php) - Class that provides a set of methods for a collection
    - [_arr](src/Map/_arr.php) - Trait that provides methods to allow advanced unidimensional array manipulation to a collection
    - [_arrs](src/Map/_arrs.php) - Trait that provides methods to allow advanced multidimensional array manipulation to a collection
    - [_basic](src/Map/_basic.php) - Trait that provides simple boolean methods to analyze the type of array within the collection
    - [_classe](src/Map/_classe.php) - Trait that grants methods to work with a collection containing fully qualified class name strings
    - [_classeObj](src/Map/_classeObj.php) - Trait that gives methods to work with a collection containing fqcn strings or objects
    - [_cloner](src/Map/_cloner.php) - Trait that provides a method to make sure the collection is cloned on every update
    - [_count](src/Map/_count.php) - Trait that provides a method to limit the length of a collection
    - [_filter](src/Map/_filter.php) - Trait that provides a method to filter a collection by providing a condition or callback
    - [_flash](src/Map/_flash.php) - Trait that grants methods for the collection to delete an entry once it has been retrieved
    - [_insensitive](src/Map/_insensitive.php) - Trait that transforms the collection from case sensitive to insensitive
    - [_map](src/Map/_map.php) - Trait that provides a method to recursively change the entries (map) by providing a callback
    - [_nav](src/Map/_nav.php) - Trait that provides methods to a collection in order to work with pagination
    - [_obj](src/Map/_obj.php) - Trait that grants methods to work with a collection containing instantiated objects
    - [_prepend](src/Map/_prepend.php) - Trait that replaces methods to make the collection prepend per default (add at the beginning)
    - [_readOnly](src/Map/_readOnly.php) - Trait that provides a simple readOnly lock to a collection
    - [_reference](src/Map/_reference.php) - Trait that permits the collection to work from a reference array source
    - [_sequential](src/Map/_sequential.php) - Trait that makes sure the keys of the collection are always sequential
    - [_sort](src/Map/_sort.php) - Trait that provides methods to change the order of entries within the collection
- [MapObj](src/MapObj.php) - Class for a collection containing objects
- [Request](src/Request.php) - Class with methods to manage an HTTP request
- [RequestHistory](src/RequestHistory.php) - Class for a collection containing an history of requests
- [Res](src/Res.php) - Class with methods to manage a resource
- [Response](src/Response.php) - Class with methods to manage an HTTP response
- [ResponseCurrent](src/ResponseCurrent.php) - Class for an object that acts as representation of current response
- [Role](src/Role.php) - Class that provides basic logic for a role
- [Roles](src/Roles.php) - Class for a collection containing many roles
- [Root](src/Root.php) - Abstract class extended by all others
- [Service](src/Service.php) - Abstract class that provides basic methods to manage a third-party service
- [ServiceMailer](src/ServiceMailer.php) - Abstract class with basic methods that needs to be extended by a mailing service
- [ServiceRequest](src/ServiceRequest.php) - Abstract class with basic methods for a service that works with HTTP request
- [ServiceVideo](src/ServiceVideo.php) - Abstract class with basic methods for a service that provides a video object after an HTTP request
- [Services](src/Services.php) - Class for a collection containing many service objects
- [Session](src/Session.php) - Class that implements the methods necessary for the SessionHandlerInterface interface
- [Std](src/Std.php) - Class for a collection with a complete set of methods
- [Timeout](src/Timeout.php) - Class for a collection containing timeout data (to deny an action if it already has happened too much)
- [Update](src/Update.php) - Class for a collection containing an update-only array (cannot insert after initialization)
- [Video](src/Video.php) - Class for an object representing a video with some meta-data
- [Widget](src/Widget.php) - Abstract class that provides basic methods for a widget
- [Xml](src/Xml.php) - Class that provides basic methods to make an XML sitemap
- [_arrObj](src/_arrObj.php) - Trait that provides methods to respect the ArrayAccess, Countable and Iterator native interfaces
- [_attr](src/_attr.php) - Trait that grants methods to work with the dynamic property attr
- [_attrPermission](src/_attrPermission.php) - Trait that provides methods to work with permissions in attr
- [_cache](src/_cache.php) - Trait that provides methods to get or set a cached value from an object
- [_email](src/_email.php) - Trait that provides methods to use the object as an email
- [_inst](src/_inst.php) - Trait that provides methods to store an instantiated objet within a static property
- [_log](src/_log.php) - Trait that provides a required method to allow logging with the object
- [_overload](src/_overload.php) - Trait that allows a class to retrieve its overloaded version - higher up in the class hierarchy
- [_queue](src/_queue.php) - Trait that provides methods required for implementing queuing functionality to an object
- [_root](src/_root.php) - Trait that provides basic object methods and magic methods
- [_rootClone](src/_rootClone.php) - Trait that provides a default __clone magic function
- [_serialize](src/_serialize.php) - Trait that provides methods for serializing and unserializing object
- [_throw](src/_throw.php) - Trait that provides static methods to throw exception from an object

## Testing
**QuidPHP/Main** contains 34 test classes:
- [Arrs](test/Arrs.php) - Class for testing Quid\Main\Arrs
- [Autoload](test/Autoload.php) - Class for testing Quid\Main\Autoload
- [Calendar](test/Calendar.php) - Class for testing Quid\Main\Calendar
- [Cart](test/Cart.php) - Class for testing Quid\Main\Cart
- [CatchableException](test/CatchableException.php) - Class for testing Quid\Main\CatchableException
- [Com](test/Com.php) - Class for testing Quid\Main\Com
- [Concatenator](test/Concatenator.php) - Class for testing Quid\Main\Concatenator
- [Error](test/Error.php) - Class for testing Quid\Main\Error
- [Exception](test/Exception.php) - Class for testing Quid\Main\Exception
- [Extender](test/Extender.php) - Class for testing Quid\Main\Extender
- [Extenders](test/Extenders.php) - Class for testing Quid\Main\Extenders
- [File](test/File.php) - Class for testing Quid\Main\File
- [Files](test/Files.php) - Class for testing Quid\Main\Files
- [Flash](test/Flash.php) - Class for testing Quid\Main\Flash
- [Importer](test/Importer.php) - Class for testing Quid\Main\Importer
- [Insensitive](test/Insensitive.php) - Class for testing Quid\Main\Insensitive
- [Insert](test/Insert.php) - Class for testing Quid\Main\Insert
- [Lang](test/Lang.php) - Class for testing Quid\Main\Lang
- [Localization](test/Localization.php) - Class for testing Quid\Main\Localization
- [Map](test/Map.php) - Class for testing Quid\Main\Map
- [MapObj](test/MapObj.php) - Class for testing Quid\Main\MapObj
- [Request](test/Request.php) - Class for testing Quid\Main\Request
- [RequestHistory](test/RequestHistory.php) - Class for testing Quid\Main\RequestHistory
- [Res](test/Res.php) - Class for testing Quid\Main\Res
- [Response](test/Response.php) - Class for testing Quid\Main\Response
- [ResponseCurrent](test/ResponseCurrent.php) - Class for testing Quid\Main\ResponseCurrent
- [Role](test/Role.php) - Class for testing Quid\Main\Role
- [Roles](test/Roles.php) - Class for testing Quid\Main\Roles
- [Session](test/Session.php) - Class for testing Quid\Main\Session
- [Std](test/Std.php) - Class for testing Quid\Main\Std
- [Timeout](test/Timeout.php) - Class for testing Quid\Main\Timeout
- [Update](test/Update.php) - Class for testing Quid\Main\Update
- [Video](test/Video.php) - Class for testing Quid\Main\Video
- [Xml](test/Xml.php) - Class for testing Quid\Main\Xml

**QuidPHP/Main** testsuite can be run by creating a new [QuidPHP/Assert](https://github.com/quidphp/assert) project.