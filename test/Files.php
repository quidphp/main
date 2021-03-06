<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// files
// class for testing Quid\Main\Files
class Files extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        Base\Dir::reset('[assertCurrent]');
        $mediaJpg = '[assertMedia]/jpg.jpg';
        $mediaPdf = '[assertMedia]/pdf.pdf';
        $image = Main\File::new($mediaJpg);
        $binary = Main\File::new($mediaPdf);
        $_file_ = Base\Finder::normalize('[assertCommon]/class.php');
        $_dir_ = dirname($_file_);
        $zip2 = Main\File::new('[assertCurrent]/archive2.zip',['create'=>true]);
        $files2 = new Main\Files($_file_,$image);

        // construct
        $files = new Main\Files($_file_,$image);
        $files3 = new Main\Files();
        $files4 = new Main\Files();

        // onPrepareReturns

        // safeBasename
        assert($files->safeBasename() === ['class.php','jpg.jpg']);

        // set
        assert($files->set(2,$binary) === $files);

        // add

        // dirMethod
        assert($files3->dirMethod('getPhp',$_dir_)->count() > 3);

        // dir
        assert($files->dir('[assertCommon]',true,['in'=>['visible'=>true]])->count() > 5);

        // dirVisible

        // dirExtension
        assert($files4->dirExtension($_dir_,'js')->isEmpty());
        assert($files4->dirExtension($_dir_,'php')->count() > 3);

        // concatenate
        assert($files3->unsetAfterCount(2)->concatenate(true)->size() > 0);

        // concatenateString
        assert(strlen($files3->concatenateString()) > 300);

        // unlink

        // makeUploadArray
        $files4->unset(0,3,4);
        assert(count($files4->makeUploadArray()) === 3);
        assert(count($files4->makeUploadArray(true)) === 6);
        $files4->unset(5);
        assert(count($files4->makeUploadArray(true)) === 3);
        assert($files4->makeUploadArray(true)[0]['error'] === 4);

        // zip
        // problème avec commit du zip sous Windows
        if(!Base\Server::isWindows())
        {
            $zip = $files2->zip('[assertCurrent]/zip.zip');
            assert($zip instanceof Main\File\Zip);
            assert(count($zip->all()) === 2);
            $zip3 = $files2->zip($zip2);
            assert($zip3 === $zip2);
            assert(count($zip3->all()) === 2);
        }

        // uploadArrayReformat

        // obj
        assert($files->filter(fn($file) => $file->extension() === 'pdf')->keys() === [2,11]);
        assert(count($files->pair('basename')) > 5);

        // map
        $files[8] = $_file_;
        assert($files[8] instanceof Main\File);
        assert(is_resource($files[8]->resource()));
        assert($files->gets(1,3,2,4,10101010)->isCount(4));
        assert($files->gets(3,1,2,4,10101010)->keys() === [3,1,2,4]);
        assert($files->clone() !== $files);
        assert($files->gets(3,1,2,4,10101010)->sequential()->keys() === [0,1,2,3]);

        // cleanup
        Base\Dir::empty('[assertCurrent]');

        return true;
    }
}
?>