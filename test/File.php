<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// file
// class for testing Quid\Main\File
class File extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
    {
        // construct
        $storage = '[assertCurrent]';
        $file = new Main\File($storage.'/test.php',['create'=>true]);
        $_file_ = Base\Finder::normalize('[assertCommon]/class.php');
        $_dir_ = dirname($_file_);
        assert($file->write("test\ntest2\n3") === $file);
        assert(strlen($file->jsonSerialize()) === 12);
        assert(count($file->toArray()) === 3);

        // isResourceValid
        assert($file->isResourceValid());

        // checkResourceValid
        assert($file->checkResourceValid() === $file);

        // prepareOption

        // readOption

        // writeOption

        // unlinkOnShutdown

        // files
        assert($file->files()->count() === 1);

        // defaultMimeGroup
        assert($file::defaultMimeGroup() === null);

        // defaultExtension
        assert($file::defaultExtension() === null);

        // getClass

        // getClassFromGroup

        // getDirnameFromValue
        assert(Main\File::getDirnameFromValue($file) === $file->dirname());
        assert(Main\File::getDirnameFromValue($_file_) === $_dir_);

        // getClassFromDirname
        assert($file::getClassFromDirname($_dir_) === null);

        // new

        // newCreate

        // newOverload

        // newFiles
        assert($file::newFiles() instanceof Main\Files);

        // registerMime

        // registerClass

        // registerGroup

        // registerStorage

        // registerUtil

        // registerType

        // res
        assert(is_resource($file->resource()));
        assert(!empty($file->_cast()));

        return true;
    }
}
?>