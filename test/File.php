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
    final public static function trigger(array $data):bool
    {
        // prepare
        $storage = '[assertCurrent]';
        $sessionDirname = Main\File\Session::storageDirname();
        Base\Dir::set($sessionDirname);
        $file = new Main\File($storage.'/test.php',['create'=>true]);
        $_file_ = Base\Finder::normalize('[assertCommon]/class.php');
        $_dir_ = dirname($_file_);
        $mediaJpg = '[assertMedia]/jpg.jpg';
        $mediaJpgUri = Base\Uri::relative($mediaJpg);
        $mediaVector = '[assertMedia]/svg.svg';
        $mediaVectorUri = Base\Uri::relative($mediaVector);
        $mediaPdf = '[assertMedia]/pdf.pdf';
        $mediaPng = '[assertMedia]/png.png';
        Base\File::unlink($storage.'/newZip.zip');
        $audio = Main\File::newCreate($storage.'/create.mp3');
        $audio2 = Main\File::new($audio);
        $font = Main\File::new($storage.'/create.ttf',['create'=>true]);
        $video = Main\File::new($storage.'/create.mp4',['create'=>true]);
        $create = Main\File::new($storage.'/create.jpg',['create'=>true]);
        $csv = Main\File::new('[assertCommon]/csv.csv',['toUtf8'=>true]);
        $text = Main\File::newCreate($storage.'/index.php');
        $text->write("lorem ipsum lorem ipsum\nlorem ipsum lorem ipsum2\nlorem ipsum lorem ipsum3\nlorem ipsum lorem ipsum4");
        $raster = Main\File::new($mediaJpg);
        $rasterStorage = Main\File::new('[assertCommon]/png.png');
        $vector = Main\File::new($mediaVector);
        $vectorStorage = Main\File::new('[assertCommon]/svg.svg');
        $doc = Main\File::new($storage.'/document345.doc');
        $pdf = Main\File::new($mediaPdf);
        $calendar = Main\File::new($storage.'/ics.ics',['create'=>true]);
        $calendarTemp = Main\File\Calendar::new(true);
        $captcha = Main\File::new(true,['mime'=>'jpg']);
        $res = Base\Res::temp('csv','ok.csv');
        $tempCsv = Main\File\Csv::new($res);
        $core = Main\File::new(true);
        $text2 = Main\File::new(true,['mime'=>'txt']);
        $raster2 = Main\File::new(true,['mime'=>'jpg']);
        $pngMime = Main\File::new(true,['mime'=>'image/png']);
        $jpgBasename = Main\File::new(true,['basename'=>'test.jpg']);
        $csv2 = Main\File\Csv::new(true);
        $pngTemp = Main\File::new(Base\Res::temp('png','temp.png'));
        $png = Main\File::newCreate($mediaPng);
        $serialize = Main\File\Serialize::new($storage.'/serialize.txt',['create'=>true]);
        $dump = Main\File\Dump::new($storage.'/serialize.html',['create'=>true]);
        $html = Main\File::new(true,['mime'=>'html']);
        $json = Main\File::new($storage.'/json.json',['create'=>true]);
        $xml = Main\File::new($storage.'/xml.xml',['create'=>true]);
        $zip = Main\File::new('[assertCommon]/zip.zip');
        $js = Main\File::new('[assertCommon]/test.js');
        $css = Main\File::new('[assertCommon]/test.css');
        $newZip = Main\File::newCreate($storage.'/newZip.zip');
        $newXml = Main\File::new($storage."/xml2éèàsad l'article.xml",['create'=>true]);
        $imgMime = new Main\File($storage.'/test.jpg',['mime'=>'jpg','create'=>true]);
        $temp = new Main\File(true);

        // isResourceValid
        assert($file->isResourceValid());

        // checkResourceValid
        assert($file->checkResourceValid() === $file);

        // prepareOption

        // readOption

        // writeOption

        // unlinkOnShutdown

        // makeUploadArray
        assert(count($file->makeUploadArray()) === 5);

        // files
        assert($file->files()->count() === 1);

        // defaultMimeGroup
        assert($file::defaultMimeGroup() === null);

        // defaultExtension
        assert($file::defaultExtension() === null);

        // getClass
        assert(is_a($file::getClass($storage.'/test.php'),Main\File\Php::class,true));

        // getClassFromGroup
        assert($file::getClassFromGroup('pdf') === Main\File\Pdf::class);

        // getDirnameFromValue
        assert(Main\File::getDirnameFromValue($file) === $file->dirname());
        assert(Main\File::getDirnameFromValue($_file_) === $_dir_);
        assert($file::getClassFromDirname($sessionDirname) === Main\File\Session::class);
        assert($file::getClassFromDirname($sessionDirname.'/new') === Main\File\Session::class);

        // getClassFromDirname
        assert($file::getClassFromDirname($_dir_) === null);

        // new

        // newCreate

        // newOverload

        // newFiles
        assert($file::newFiles() instanceof Main\Files);

        // registerClass

        // registerGroup

        // registerStorage

        // registerUtil

        // getOverloadKeyPrepend

        // res
        assert($file->write("test\ntest2\n3") === $file);
        assert(is_resource($file->resource()));
        assert(!empty($file->_cast()));
        assert(strlen($file->jsonSerialize()) === 12);
        assert(count($file->toArray()) === 3);

        // temp
        assert($tempCsv instanceof Main\File\Csv);
        assert($tempCsv->isPhp());
        assert($tempCsv->isPhpTemp());
        assert($tempCsv->isPhpWritable());
        assert($tempCsv->kind() === 'phpTemp');
        assert($tempCsv->mode() === 'w+b');
        assert($tempCsv->mode(true) === 'w+');

        // binary
        assert($audio instanceof Main\File\Binary);

        // audio
        assert($audio instanceof Main\File\Audio);
        assert($audio->isResourceValid());
        assert($audio === $audio2);
        assert($audio::defaultExtension() === 'mp3');
        $audioTest = Main\File::newCreate($audio);
        assert($audioTest === $audio);
        $audioTest = Main\File::new($audio);
        assert($audioTest === $audio);

        // cache
        $new = Main\File\Cache::storage([1,2,3]);
        assert($new instanceof Main\File\Cache);
        $cache = Main\File\Cache::storageAll()->first();
        assert($cache instanceof Main\File\Cache);
        assert($new->read() === [1,2,3]);
        assert($new->unlink());

        // calendar
        assert($calendar instanceof Main\File\Calendar);
        assert($calendar instanceof Main\File\Text);
        assert($calendar::defaultMimeGroup() === 'calendar');
        assert($calendar->size() === 0);
        assert($calendar->unlink());
        assert($calendarTemp instanceof Main\File\Calendar);
        assert($calendarTemp->mime() === 'text/calendar');

        // css
        assert($css) instanceof Main\File\Css;

        // csv
        assert($csv instanceof Main\File\Csv);
        assert($csv2 instanceof Main\File\Csv);
        assert(is_resource($csv->resource()));
        assert(is_array($csv->read()));
        assert($csv->read()[0][0] === 'Item Code');
        assert($csv->writeBom() === true);
        assert($csv->read()[0][0] === 'm Code');
        assert($csv->same());
        assert($csv->read() !== $csv->clean());
        assert(is_string($csv->assoc()[0]['m Code']));
        assert($csv->assoc() !== $csv->assoc(true));
        assert(is_string($csv->readRaw()));
        assert(Base\Column::is($csv->lines()));
        assert($csv->line() === null);
        assert($csv->seek(0) === $csv);
        assert(count($csv->line()) === 12);
        assert($csv::defaultMimeGroup() === 'csv');
        $csv->seek(0);
        $i = 20;
        assert(count($csv->lineRef(true,true,$i)) === 12);

        // doc
        assert($doc instanceof Main\File\Doc);

        // dump
        assert($dump instanceof Main\File\Dump);
        $write = new Main\Map([2=>'ok','yes',4]);
        assert($dump->write($write) === $dump);
        assert(empty($dump->readOption()['callback']));
        assert(!empty($dump->writeOption()));
        assert(is_string($dump->read()));
        assert($dump->extension() === 'html');

        // email
        $email = Main\File\Email::newCreate($storage.'/email.json');
        $email->writeRaw('{
			"contentType": "txt",
			"subject": "OK",
			"body": "Lorem ipsum"
		}');
        assert(is_array($email->read()));
        assert($email->contentType() === 'txt');
        assert($email->subject() === 'OK');
        assert($email->body() === 'Lorem ipsum');
        assert($email[0] === '{');
        foreach ($email as $key => $value) {
            assert(is_int($key));
            assert(is_string($value));
        }
        assert(count($email) === 5);

        // error
        $error = new Main\Error();
        $new = Main\File\Error::log($error);
        $new2 = Main\File\Error::log($error2 = new Main\Error());
        $new3 = Main\File\Error::log($error3 = new Main\Error());
        $new4 = Main\File\Error::log($error4 = new Main\Error());
        $new5 = Main\File\Error::log($error5 = new Main\Error());
        assert($new3->extension() === 'html');
        assert(Main\File::new($new5->path()) instanceof Main\File\Error);
        assert(Main\File::new($new5->path()) !== $new5);
        assert(Main\File::new($new5->resource()) instanceof Main\File\Error);
        assert(Main\File\Error::isStorageDataValid($error));
        assert(!Main\File\Error::isStorageDataValid('lol'));
        assert(is_array(Main\File\Error::storageData($error)));
        assert($new instanceof Main\File\Error);
        assert(Main\File\Error::logTrim() === 0);
        Base\Dir::empty(Main\File\Error::storageDirname());

        // font
        assert($font instanceof Main\File\Font);

        // html
        assert($html instanceof Main\File\Html);

        // imageRaster
        assert($pngMime instanceof Main\File\ImageRaster);
        assert($jpgBasename instanceof Main\File\ImageRaster);
        assert($create instanceof Main\File\ImageRaster);
        assert(is_resource($create->resource()));
        assert($create->unlink());
        assert($raster instanceof Main\File\Image);
        assert($raster instanceof Main\File\ImageRaster);
        assert($raster2 instanceof Main\File\ImageRaster);
        assert(!$raster->isEmpty());
        assert($raster2->isEmpty());
        assert($raster->isNotEmpty());
        assert($raster->isMimeGroup('imageRaster'));
        assert(count($raster->info()) === 18);
        assert(count($raster->stat()) === 26);
        assert($raster->size() > 0);
        assert($raster->mime() === 'image/jpeg; charset=binary');
        assert($raster->mimeGroup() === 'imageRaster');
        assert($raster->uri() === $raster->path());
        assert(!empty($raster->path()));
        assert($raster->basename() === 'jpg.jpg');
        assert($raster->filename() === 'jpg');
        assert($raster->extension() === 'jpg');
        assert($raster::defaultMimeGroup() === 'imageRaster');
        assert($png instanceof Main\File\Image);
        assert($png->mime() === 'image/png; charset=binary');
        assert($pngTemp instanceof Main\File\Image);
        assert($pngTemp->mime() === 'image/png');
        assert($pngTemp->write($png));
        assert(strlen(Base\Html::img($pngTemp)) > 2000);
        assert(Base\Html::a($raster) === "<a href='".$mediaJpgUri."'></a>");
        assert(Base\Html::aOpen($raster) === "<a href='".$mediaJpgUri."'>");
        assert($raster->safeBasename() === 'jpg.jpg');
        assert(strlen($rasterStorage->img()) > 2500);
        assert(strlen($captcha->captcha('test','[assertCommon]/ttf.ttf')->img()) > 2000);

        // imageVector
        assert($vector instanceof Main\File\Image);
        assert($vector instanceof Main\File\ImageVector);
        assert($vector->mimeGroup() === 'imageVector');
        assert($vector::defaultMimeGroup() === 'imageVector');
        assert($vector->img() === "<img alt='svg' src='".$mediaVectorUri."'/>");
        assert(strlen($vectorStorage->img()) === 425);

        // js
        assert($js instanceof Main\File\Js);

        // json
        assert($json instanceof Main\File\Json);
        $write = ['test'=>'ok',2,3];
        assert($json->write($write));
        assert($json->read() === $write);
        assert(is_string($json->readRaw()));
        assert($json->unlink());

        // log
        $write = new Main\Map([2=>'test',3,'ok']);
        assert(Main\File\Log::isStorageDataValid());
        assert(!empty(Main\File\Log::storageDirname()));
        assert(Main\File\Log::storageFilename() === Base\Response::id().'-0');
        assert(Base\File::isWritableOrCreatable(Main\File\Log::storagePath($write)));
        assert(Main\File\Log::storageData($write) === $write);
        assert(Main\File\Log::storageData($write,2) === [$write,2]);
        assert(($log = Main\File\Log::log($write)) instanceof Main\File\Log);
        assert(Main\File::new($log->resource()) instanceof Main\File\Log);
        assert(Main\File\Log::logTrim() === 0);
        assert($log->unlink());

        // pdf
        assert($pdf instanceof Main\File\Pdf);
        assert($pdf instanceof Main\File\Binary);
        assert($pdf::defaultMimeGroup() === 'pdf');

        // php
        assert($text instanceof Main\File\Php);

        // queue
        assert(Main\File\Queue::setUnqueueCallable(function() {
            assert($this instanceof Main\File\Queue);
            return 'test';
        }) === null);
        assert(is_int(Main\File\Queue::storageAll()->unlink()));
        $data = ['what'=>'ok'];
        $queue = Main\File\Queue::queue($data);
        assert($queue->extension() === null);
        $data = ['what2'=>'ok'];
        $queue2 = Main\File\Queue::queue($data);
        assert($queue instanceof Main\File\Queue);
        assert(Main\File\Queue::storageAll()->isNotEmpty());
        assert(Main\File\Queue::storageSort()->isNotEmpty());
        assert(Main\File\Queue::storageSkip(1)->isCount(1));
        assert(Main\File\Queue::getQueued()->isCount(2));
        assert(Main\File\Queue::storageAll()->first() instanceof Main\File\Queue);
        assert(Main\File\Queue::storageAll()->first()->read() === ['what'=>'ok']);
        assert(Main\File\Queue::triggerUnqueue(1) === ['test']);
        assert(Main\File\Queue::storageAll()->unlink() === 1);
        assert(Main\File\Queue::storageTrim(2) === 0);
        assert(Main\File\Queue::triggerUnqueue(1) === null);
        Base\Dir::empty(Main\File\Queue::storageDirname());

        // serialize
        $write = new Main\Map([2=>'ok','yes',4]);
        assert($serialize instanceof Main\File\Serialize);
        assert($serialize->extension() === 'txt');
        assert($serialize->write($write) === $serialize);
        assert(!empty($serialize->readOption()['callback']));
        assert(!empty($serialize->writeOption()['callback']));
        assert($serialize->read() instanceof Main\Map);
        assert(is_string($serialize->readRaw()));
        assert($serialize->read() !== $write);
        assert($serialize->unlink());
        assert(Main\File\Serialize::getClass($storage.'/serialize.txt',['create'=>true]) === Main\File\Serialize::class);

        // session
        $storageSession = '[storage]/session/main';
        $f = new Main\File\Session($storageSession.'/abcdef',['create'=>true]);
        $f->write([1,2,3]);
        assert(Main\File::new($f->path()) instanceof Main\File\Session);
        assert($f->read() === [1,2,3]);
        assert($f->sessionSid() === 'abcdef');
        assert(!empty($f->sessionData()));
        assert($f->sessionWrite(serialize([3,4,5])));
        assert($f->sessionUpdateTimestamp());
        assert($f->sessionDestroy());
        assert(!empty(Main\File\Session::sessionDir(Base\Finder::normalize($storageSession),'test')));
        assert(!empty(Main\File\Session::sessionPath(Base\Finder::normalize($storageSession),'test','abcde')));
        assert(!Main\File\Session::sessionExists(Base\Finder::normalize($storageSession),'test','abcde'));
        assert(Main\File\Session::sessionCreate(Base\Finder::normalize($storageSession),'test','abcde') instanceof Main\File\Session);
        assert(Main\File\Session::sessionRead(Base\Finder::normalize($storageSession),'test','abcde') instanceof Main\File\Session);
        assert(Main\File\Session::sessionGarbageCollect(Base\Finder::normalize($storageSession),'test',1000) === 0);
        assert(Base\Dir::emptyAndUnlink($storageSession));

        // text
        assert($text instanceof Main\File\Text);
        assert($text2 instanceof Main\File\Text);

        // txt
        assert($core instanceof Main\File);
        assert($text2 instanceof Main\File\Txt);
        assert(is_resource($text->resource()));
        assert(!$text->isFileUploaded());
        assert(count($text->lines()) === 4);
        assert($text->seek() === $text);
        assert($text->line() === 'lorem ipsum lorem ipsum');
        $i = 0;
        assert($text->seekRewind()->lineRef(true,true,$i) === 'lorem ipsum lorem ipsum');
        assert($text->lineRef(true,true,$i) === 'lorem ipsum lorem ipsum2');

        // video
        assert($video instanceof Main\File\Video);
        assert($video->mime() === 'inode/x-empty; charset=binary');

        // xml
        assert($xml instanceof Main\File\Xml);
        assert($xml->write('<?xml'));
        assert($xml->mime() === 'text/xml; charset=us-ascii');

        // zip
        assert($zip instanceof Main\File\Zip);
        assert($zip->mime() === 'application/zip; charset=binary');
        assert($zip->archive() instanceof \ZipArchive);
        assert(count($zip->all()) === 9);
        assert($zip->extract($storage.'/extract'));

        // problème avec commit du zip sous Windows
        if(!Base\Server::isWindows())
        {
            assert($newZip->all() === []);
            assert($newZip->addFile($newXml));
            assert($newZip->addFile($video));
            assert(count($newZip->all()) === 2);
            assert($newZip->commit());
            assert($newZip->extract($storage.'/extract2'));
        }

        // cleanup
        Base\Dir::empty('[assertCurrent]');

        return true;
    }
}
?>