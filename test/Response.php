<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Main;
use Quid\Base;

// response
// class for testing Quid\Main\Response
class Response extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$mediaJpg = '[assertMedia]/jpg.jpg';
		$mediaJson = '[assertMedia]/json.json';

		// construct
		$response = new Main\Response($mediaJpg,['followLocation'=>true]);
		$response3 = new Main\Response($mediaJson);
		$response2 = clone $response;
		assert($response2 !== $response);

		// toString

		// clone

		// toArray
		assert(count($response2->toArray()) === 5);

		// cast
		assert(is_string($response->_cast()));

		// setRequest

		// request
		assert($response->request() instanceof Main\Request);

		// trigger

		// setExec

		// setHeaders

		// headers
		assert(in_array(count($response->headers()),[8,10],true));

		// setResource

		// resource
		assert(Base\Res::isPhpTemp($response->resource()));

		// setTimestamp

		// timestamp
		assert(!empty($response->timestamp()));

		// setId

		// id
		assert(strlen($response->id()) === 10);

		// body
		assert(!empty($response->body()));
		assert($response3->body() === '[1,2,3]');
		assert($response3->body(true) === [1,2,3]);

		// is200
		assert($response->is200());

		// isCodePositive
		assert($response->isCodePositive());

		// isCodeError
		assert(!$response->isCodeError());

		// isCodeServerError
		assert(!$response->isCodeServerError());

		// isHtml
		assert(!$response->isHtml());
		assert(!$response3->isHtml());

		// isJson
		assert(!$response->isJson());
		assert($response3->isJson());

		// isXml
		assert(!$response->isXml());

		// isCode
		assert($response->isCode(300,301,200));
		assert(!$response->isCode(300,301,201));

		// isCodeBetween
		assert(!$response->isCodeBetween(198,199));
		assert($response->isCodeBetween(199,200));
		assert($response->isCodeBetween(200,201));
		assert(!$response->isCodeBetween(201,202));

		// isCodeIn
		assert($response->isCodeIn(250));
		assert($response->isCodeIn(200));
		assert($response->isCodeIn(299));
		assert(!$response->isCodeIn(199));
		assert(!$response->isCodeIn(300));

		// isContentType
		assert($response3->isContentType('json'));
		assert(!$response3->isContentType('text/json'));
		assert($response3->isContentType('application/json'));

		// code
		assert($response->code() === 200);

		// protocol
		assert(in_array($response->protocol(),['HTTP/1.1','HTTP/2','HTTP/2.0'],true));

		// statusText
		assert($response->statusText() === 'OK');

		// status
		assert($response->status() === $response->protocol().' 200 OK');

		// contentType
		assert($response->contentType() === 'image/jpeg');
		assert($response->contentType(2) === 'jpg');

		// contentLength
		assert($response->contentLength() === $response->size());

		// absolute
		assert(!empty($response->absolute()));

		// trait resRead
		assert(count($response->info()) === 18);
		assert(count($response->stat()) === 26);
		assert(Base\Res::isPhpTemp($response->resource()));
		assert($response3->isMimeGroup('json'));
		assert($response->isMimeGroup('imageRaster'));
		assert(!$response->isEmpty());
		assert($response->isNotEmpty());
		assert($response3->mime() === 'application/json');
		assert($response3->mimeGroup() === 'json');
		assert($response3->size() === 7);
		assert($response3->uri() === 'php://temp');
		assert($response3->path() === 'json.json');
		assert($response3->basename() === 'json.json');
		assert($response->filename() === 'jpg');
		assert($response->extension() === 'jpg');
		assert(count($response->curlInfo()) >= 26);

		// arrObj
		assert(count($response) === 7);

		return true;
	}
}
?>