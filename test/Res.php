<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// res
class Res extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		Base\Dir::reset("[assertCurrent]");
		$path = Base\File::path("[assertCurrent]/index.php");
		$pathNew = Base\File::path("[assertCurrent]/index2.php");
		$path3 = Base\File::path("[assertCurrent]/arrayAccess.php");

		// construct
		$res = new Main\Res($path,['create'=>true]);
		$resNew = new Main\Res($pathNew,['create'=>true]);
		$res3 = new Main\Res($path3,['create'=>true]);
		$temp = new Main\Res("php://temp");
		$tempConcat = new Main\Res("php://temp");
		$res->write("lorem ipsum lorem ipsum\nlorem ipsum lorem ipsum 2\nlorem ipsum lorem ipsum 3");
		$res->seekRewind();
		$res3->overwrite("lorem ipsum lorem ipsum\nlorem ipsum lorem ipsum 2\nlorem ipsum lorem ipsum 3");
		$res3->seekRewind();

		// toString

		// call
		\assert(!$res->isEmpty());
		\assert($res->isNotEmpty());
		\assert($res->isReadable());
		\assert($res->isWritable());
		\assert(!$res->isBinary());
		\assert($res->isStream());
		\assert($res->isRegularType());
		\assert(!$res->isCurl());
		\assert(!$res->isFinfo());
		\assert(!$res->isContext());
		\assert($res->isFile());
		\assert($res->isFileExists());
		\assert($res->isFileLike());
		\assert(!$res->isFileUploaded());
		\assert($res->isFileVisible());
		\assert(!$res->isFilePathToUri());
		\assert($res->isFileParentExists());
		\assert($res->isFileParentReadable());
		\assert($res->isFileParentWritable());
		\assert($res->isFileParentExecutable());
		\assert(!$res->isDir());
		\assert(!$res->isHttp());
		\assert(!$res->isPhp());
		\assert(!$res->isPhpWritable());
		\assert(!$res->isPhpInput());
		\assert(!$res->isPhpOutput());
		\assert(!$res->isPhpTemp());
		\assert(!$res->isPhpMemory());
		\assert($res->isResponsable());
		\assert($res->isLocal());
		\assert(!$res->isRemote());
		\assert(!$res->isTimedOut());
		\assert($res->isBlocked());
		\assert($res->isSeekable());
		\assert($res->isSeekableTellable());
		\assert($res->isLockable());
		\assert($res->isStart());
		\assert(!$res->isEnd());
		\assert($res->canStat());
		\assert($res->canLocal());
		\assert($res->canMeta());
		\assert($res->canContext());
		\assert(!$res->hasScheme());
		\assert($res->hasExtension());
		\assert($tempConcat->concatenate(null,PHP_EOL,$res,$resNew,$res));
		\assert(\strlen($tempConcat->read()) === 152);
		\assert($tempConcat->basename() === null);
		\assert($tempConcat->mime() === null);
		\assert($tempConcat->mimeGroup() === null);
		\assert($res->mimeGroup() === 'php');
		\assert($res->mimeFamilies() === ['text']);
		\assert($res->mimeFamily() === 'text');
		
		// jsonSerialize
		\assert($res->toJson() === '"lorem ipsum lorem ipsum\nlorem ipsum lorem ipsum 2\nlorem ipsum lorem ipsum 3"');

		// cast
		\assert(\is_resource($res->_cast()));

		// toArray
		\assert(\count($res->toArray()) === 3);

		// offsetSet

		// offsetUnset

		// arr

		// isResourceValid
		\assert($res->isResourceValid());

		// setResource

		// resource
		\assert(\is_resource($res->resource()));

		// base
		\assert(\count($res->stat()) === 26);
		\assert(\is_int($res->inode()));
		\assert(\is_int($res->permission()));
		\assert(\is_int($res->owner()));
		\assert(\is_array($res->group(true)));
		\assert(\is_int($res->dateAccess()));
		\assert(\is_string($res->dateModify(true)));
		\assert(\is_int($res->dateInodeModify()));
		\assert(\count($res->info()) === 18);
		\assert(\count($res->responseMeta()) === 4);
		\assert($res->type() === 'stream');
		\assert($res->kind() === 'file');
		\assert(\count($res->meta()) === 9);
		\assert($res->mode() === 'c+');
		\assert($res->wrapperType() === 'plainfile');
		\assert($res->wrapperData() === null);
		\assert($res->streamType() === 'STDIO');
		\assert($res->unreadBytes() === 0);
		\assert(!empty($res->uri()));
		\assert($res->headers() === null);
		\assert(\count($res->parse()) === 8);
		\assert($res->scheme() === null);
		\assert($res->host() === null);
		\assert(!empty($res->path()));
		\assert(\count($res->pathinfo()) === 4);
		\assert(!empty($res->dirname()));
		\assert($res->basename() === 'index.php');
		\assert($res->safeBasename() === 'index.php');
		\assert($res->mimeBasename() === 'index.php');
		\assert($res->mimeBasename('bla') === 'bla.php');
		\assert($res->filename() === 'index');
		\assert($res->extension() === 'php');
		\assert($res->size() === 75);
		\assert($res->size(true) === '75 Bytes');
		\assert($res->mime() === 'text/x-php');
		\assert($res->mime(true) === 'text/x-php');
		\assert($res->mimeGroup() === 'php');
		\assert($res->mimeExtension() === 'php');
		\assert(\is_array($res->param()));
		\assert(\is_array($res->option()));
		\assert($res->curlInfo() === null);
		\assert(\is_int($res->position()));
		\assert($res->lineCount() === 3);
		\assert(\strlen($res->pathToUriOrBase64()) === 123);
		\assert(\strlen($res->base64()) === 123);
		\assert(\strlen($res->base64(false)) === 100);

		// check
		\assert($res->check('isNotEmpty') === $res);

		// option

		// isScheme
		\assert(!$res->isScheme('file'));

		// isExtension
		\assert($res->isExtension(['php']));

		// isMimeGroup
		\assert($res->isMimeGroup('php'));
		
		// isMimeFamily
		\assert(!$res->isMimeFamily('php'));
		\assert($res->isMimeFamily('text'));
		
		// isFilePermission
		\assert($res->isFilePermission('readable'));

		// isOwner
		\assert($res->isOwner($res->owner()));

		// isGroup
		\assert($res->isGroup($res->group()));

		// permissionChange
		\assert($res->permissionChange(777));

		// ownerChange

		// groupChange

		// readOption

		// read
		\assert($res->read(1,2) === 'or');

		// readRaw
		\assert($res->read(1,2) === 'or');

		// seek
		\assert($res->seek(1) === $res);
		\assert($res->position() === 1);

		// seekCurrent
		\assert($res->seekCurrent(1) === $res);

		// seekEnd
		\assert($res->seekEnd() === $res);
		\assert($res->isEnd());

		// seekRewind
		\assert($res->seekRewind() === $res);
		\assert($res->isStart());

		// lines
		\assert($res->lines(0,0) === []);

		// line
		\assert($res->seekRewind()->line() === "lorem ipsum lorem ipsum");

		// lineRef
		$i = 0;
		\assert($res->seekRewind()->lineRef(0,1,$i) === 'lorem ipsum lorem ipsum');

		// lineFirst
		\assert($res->lineFirst() === 'lorem ipsum lorem ipsum');

		// lineLast
		\assert($res->lineLast() === 'lorem ipsum lorem ipsum 3');

		// lineChunk
		\assert(Base\Arrs::is($res->lineChunk(2)));

		// lineChunkWalk
		\assert(\count($res->lineChunkWalk(function() {
			return true;
		})) === 3);

		// lineReturns

		// lineReturn

		// passthruChunk

		// subCount
		\assert($res->subCount('ipsum') === 6);

		// writeOption

		// write
		\assert($temp->write('loremIpsum')->read() === 'loremIpsum');

		// writeRaw

		// overwrite
		\assert($temp->overwrite('loremIpsum2')->read() === 'loremIpsum2');

		// prepend
		\assert(\strlen($temp->prepend('ok',['newline'=>true])->read()) === 14);

		// append
		\assert(\strlen($temp->append('ok',['newline'=>true])->read()) === 17);

		// lineSplice
		\assert(\strlen($temp->lineSplice(0,1,['ookkk','line2'])->read()) === 26);

		// lineSpliceFirst
		\assert(\strlen($temp->lineSpliceFirst("first\nok")->read()) === 29);

		// lineSpliceLast
		\assert(\strlen($temp->lineSpliceLast('last')->read()) === 31);

		// lineInsert
		\assert(\strlen($temp->lineInsert(1,'insert')->read()) === 38);

		// lineFilter
		\assert(\strlen($temp->lineFilter(function($line) {
			return (\strlen($line) > 2)? true:false;
		})->read()) === 35);

		// lineMap
		\assert(\strlen($temp->lineMap(function($line) {
			return $line."A";
		})->read()) === 40);

		// setContextMime
		\assert($tempConcat->setContextMime('text/plain'));
		\assert($tempConcat->mime() === 'text/plain');
		\assert($tempConcat->mimeGroup() === 'txt');

		// setContextBasename
		\assert($tempConcat->setContextBasename('james.log'));
		\assert($tempConcat->basename() === 'james.log');

		// empty
		\assert($temp->empty()->read() === '');

		// touch
		\assert($resNew->write('OK')->read() === 'OK');
		\assert($resNew->touch() === $resNew);

		// rename
		\assert($resNew->rename($resNew->path()."ok") === $resNew);

		// changeDirname
		\assert($resNew->changeDirname($resNew->dirname()."/okok") === $resNew);

		// changeBasename
		\assert($resNew->changeBasename('well') === $resNew);

		// changeExtension
		\assert($resNew->changeExtension('jpg') === $resNew);

		// removeExtension
		\assert($resNew->removeExtension() === $resNew);

		// moveUploaded

		// copy
		\assert($resNew->copy($resNew->path()."2") === $resNew);

		// copyInDirname
		\assert($resNew->copyInDirname('james') === $resNew);

		// copyWithBasename
		\assert($resNew->copyWithBasename($resNew->dirname()."/higher") === $resNew);

		// unlink
		\assert($resNew->unlink() === true);

		// toFile
		$toFile = $res->toFile("[assertCurrent]/to_file.txt");
		\assert($toFile instanceof Main\File);
		\assert($toFile->read() === $res->read());
		\assert($toFile !== $res);

		// arrObj
		\assert(\count($toFile) === 3);
		\assert($toFile[1] === "lorem ipsum lorem ipsum 2");
		foreach ($toFile as $key => $value) {
			\assert(\is_string($value));
		}
		\assert(!isset($res[3]));
		$res3[] = "testa";
		\assert(\count($res3->lines()) === 4);
		\assert(\count($res3) === 4);
		\assert($res3->offsetExists(3));
		\assert(isset($res3[3]));
		\assert($res3[3] === 'testa');
		\assert(isset($res3[3]));
		unset($res3[3]);
		\assert(!isset($res3[3]));
		\assert(\strlen($res3->read()) === 75);
		$res3[1] = "OK";
		\assert(\strlen($res3->read()) === 52);

		// cleanup
		\assert(Base\Dir::empty('[assertCurrent]'));
		
		return true;
	}
}
?>