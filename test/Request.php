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

// request
// class for testing Quid\Main\Request
class Request extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $uri = 'http://google.com/lavieestlaide?get=laol#lastchance';
        $complex = 'http://google.com/laviestlaide?get=laol#lastchancé';
        $encodedUri = 'http://google.com/test/%C3%A9ol/la%20vie/i.php?james=lala&ka=%C3%A9o&space=la%20uy#hash%C3%A9';
        $mediaJpg = '[assertMedia]/jpg.jpg';
        $mediaJpgUri = Base\Uri::output($mediaJpg);
        $_file_ = Base\Finder::normalize('[assertCommon]/class.php');
        $fileObj = Main\File::new($_file_);
        $filesObj = new Main\Files($_file_);

        // construct
        $r = new Main\Request($uri);
        $r2 = new Main\Request($uri);
        $r3 = new Main\Request('/lavieestbelle.jpg?test=ok&james=bla');
        $r4 = new Main\Request($uri);
        $r4->change(['host'=>'bla.com','fragment'=>null]);
        $r5 = new Main\Request($encodedUri,['decode'=>true]);
        $r6 = new Main\Request($mediaJpg);
        $r7 = new Main\Request('http://google.com/en/lavieestblel');
        $r8 = new Main\Request('/fr/lavieestblel.jpg');
        $r9 = new Main\Request('http://google.com/en/la/viees/tblel');
        $post = new Main\Request('/');
        $post->change(['post'=>['-captcha-'=>'abc','-csrf-'=>Base\Str::random(40),'test'=>'123','Ok'=>'LOL','james'=>'true']]);
        $filesArray = ['ok'=>['name'=>['test.jpg','ok.lala'],'tmp_name'=>['ok','ok'],'type'=>['ok','ok'],'size'=>[200,0],'error'=>[0,0]]];
        $files = new Main\Request(['uri'=>'/','post'=>['well'=>'no','ok'=>['bla.php']],'files'=>$filesArray]);
        $file = new Main\Request(['/ok','post'=>['well'=>'no','ok'=>'bla.php'],'files'=>['ok'=>['name'=>'test.jpg','error'=>'ok']]]);
        $setFile = new Main\Request(['uri'=>'/']);
        $setFiles = new Main\Request(['uri'=>'/']);
        $current = Main\Request::live();
        $current2 = Main\Request::live();
        $currentReset = Main\Request::live();
        $r10 = new Main\Request();
        $nl = new Main\Request('browserconfig.xml');
        $clone = $nl->clone();
        $null = new Main\Request();
        $arg = new Main\Request('-v');
        assert($current->id() === Base\Request::id());

        // invoke
        assert($r('host') === 'google.com');
        assert($r('lang') === 'en');

        // toString

        // onSetInst

        // onUnsetInst

        // cast
        assert($r->_cast() === $uri);

        // toArray
        assert(count($r->toArray()) === 23);

        // jsonSerialize
        assert(strlen($r->toJson()) >= 353);

        // isLive
        assert(!$r->isLive());
        assert($r10->isLive());

        // setLive

        // isCli
        assert(!$r->isCli());

        // setCli

        // getLogData
        assert($r->getLogData() === null);

        // setLogData

        // getAttrBase
        assert(is_array($r->getAttrBase('lang')));
        assert($r->getAttr('lang') === null);

        // setDefault
        assert($r4->uri() === 'http://bla.com/lavieestlaide?get=laol');
        assert($r4->setDefault(false) === $r4);
        assert($r4->setDefault(false) === $r4);
        assert($r4->host() === Base\Request::host());
        assert($r->setDefault(true) === $r);

        // str
        assert(!empty($r->str()));

        // uri
        assert($r->uri() === $uri);
        assert($r->uri(false) === '/lavieestlaide?get=laol#lastchance');
        assert($r5->uri() === 'http://google.com/test/éol/la vie/i.php?james=lala&ka=éo&space=la uy#hashé');
        assert($current->uri() === Base\Request::uri());

        // output
        assert($r->output() === $uri);
        assert($r5->output() === $encodedUri);

        // relative
        assert($r->relative() === '/lavieestlaide?get=laol#lastchance');
        assert($r3->relative() === '/lavieestbelle.jpg?test=ok&james=bla');
        assert($r5->relative() === '/test/%C3%A9ol/la%20vie/i.php?james=lala&ka=%C3%A9o&space=la%20uy#hash%C3%A9');

        // relativeExists
        assert($r6->relativeExists() === $mediaJpgUri);

        // absolute
        assert($r->absolute() === $uri);
        assert($r5->absolute() === $encodedUri);
        assert($null->absolute() === Base\Request::absolute());

        // setUri
        assert($r2->setUri('https://username:password@hostname.com:9090/path?arg=value#anchor') === $r2);
        assert($r2->uri() === 'https://username:password@hostname.com:9090/path?arg=value#anchor');
        assert($r2->relative() === '/path?arg=value#anchor');
        assert($r2->absolute() === 'https://username:password@hostname.com:9090/path?arg=value#anchor');

        // setAbsolute

        // info
        assert(count($current->info()) === (count(Base\Request::info()) + 2));
        assert(count($r->info()) === 27);
        assert(count($r->info(true)) === 28);

        // safeInfo
        assert(count($current->safeInfo()) === 21);
        assert(count($current->safeInfo(true)) === 22);
        $x = new Main\Request($current->info());
        assert($x->absolute() === $current->absolute());

        // export
        assert(Base\Arr::keyStrip('headers',$current->export()) === Base\Arr::keyStrip('headers',Base\Request::export()));
        assert(count($r->export()) === 16);
        $r4['ok'] = '<b>a</b>';
        $r4['password'] = '<b>a</b>';

        // parse
        assert($current->parse() === Base\Request::parse());
        assert(count($r->parse()) === 8);

        // change
        assert($r2->change(['user'=>'bla','pass'=>'LOL','ajax'=>true]) === $r2);

        // property

        // isSsl
        assert(!$r->isSsl());
        assert($r2->isSsl());

        // isAjax
        assert(!$r->isAjax());
        assert($r2->isAjax());

        // isGet
        assert($r->isGet());

        // isPost
        assert(!$r->isPost());
        assert($post->isPost());

        // isPostWithoutData
        assert(!$post->isPostWithoutData());

        // isRefererInternal
        $r2->setReferer('http://google.com/test');
        $r2->setMethod('post');
        assert(!$r2->isRefererInternal());
        assert($r2->isRefererInternal('google.com'));

        // isInternalPost
        assert($r2->isInternalPost('google.com'));
        assert(!$r2->isInternalPost());

        // isExternalPost
        assert($r2->isExternalPost());
        assert(!$r2->isExternalPost(['google.com']));

        // isStandard
        assert($r->isStandard());

        // isPathEmpty
        assert(!$r->isPathEmpty());
        assert($post->isPathEmpty());

        // isPathMatchEmpty
        assert(!$r->isPathMatchEmpty());
        assert($post->isPathMatchEmpty());

        // isSelected
        assert(!$r->isSelected());

        // isCachable
        assert($r->isCachable());
        assert(!$post->isCachable());

        // isRedirectable
        assert($r->isRedirectable());
        assert(!$post->isRedirectable());

        // isFailedFileUpload
        assert(!$r->isFailedFileUpload());
        assert(!$post->isFailedFileUpload());

        // hasExtension
        assert(!$r->hasExtension());
        assert($r8->hasExtension());

        // hasQuery
        assert($r->hasQuery());

        // hasLang
        assert($r->hasLang());
        assert($r8->hasLang());

        // hasPost
        assert(!$r->hasPost());
        assert($post->hasPost());
        $post['data'] = '2';
        assert($post['data'] === 2);
        assert($post->hasPost());

        // hasData
        assert($r->hasData());

        // hasValidGenuine
        assert(!$r->hasValidGenuine());

        // hasUser
        assert(!$r->hasUser());

        // hasPass
        assert(!$r->hasPass());

        // hasFragment
        assert($r->hasFragment());

        // hasIp
        assert($r->hasIp());

        // hasUserAgent
        assert(!$r->hasUserAgent());

        // hasHeaders
        assert($r->hasHeaders());
        assert($current->hasHeaders());

        // isHeader
        assert($current->isHeader('host'));
        assert(!$current->isHeader('host','Acceptz'));
        assert($current->isHeader('host','Accept-language'));

        // isDesktop
        assert(!$r->isDesktop());

        // isMobile
        assert(!$r->isMobile());

        // isOldIe
        assert(!$r->isOldIe());

        // isMac
        assert(!$r->isMac());

        // isLinux
        assert(!$r->isLinux());

        // isWindows
        assert(!$r->isWindows());

        // isBot
        assert(!$r->isBot());

        // isInternal
        assert(!$r->isInternal());
        assert(!$r2->isInternal());

        // isExternal
        assert($r->isExternal());
        assert($r2->isExternal());

        // isScheme
        assert($r->isScheme('http'));

        // isHost
        assert($r->isHost('google.com'));

        // isSchemeHost
        assert($r->isSchemeHost('http://google.com'));
        assert(!$r->isSchemeHost('https://google.com'));

        // isExtension
        assert(!$r->isExtension('txt'));
        assert($r8->isExtension('jpg'));

        // isQuery
        assert($r->isQuery('get'));

        // isLang
        assert(!$r->isLang('fr'));
        assert($r8->isLang('fr'));

        // isIp
        assert($r->isIp(Base\Server::addr()));
        assert(!$r->isIp('127.0.0.2'));

        // isIpLocal
        assert(is_bool($r->isIpLocal()));

        // isIpAllowed
        assert($r->isIpAllowed());

        // isPathSafe
        assert($r->isPathSafe());
        assert(!$r5->isPathSafe());

        // isPathArgument
        assert(!$r->isPathArgument());
        assert($arg->isPathArgument());

        // isPathArgumentNotCli
        assert(is_bool($arg->isPathArgumentNotCli()));

        // hasFiles
        assert(!$r->hasFiles());

        // checkPathSafe
        assert($r->checkPathSafe() === $r);

        // checkRequired
        assert($r->checkRequired() === $r);

        // setId
        assert($r->setId('abc'));
        assert($r->id() === 'abc');

        // id
        assert(strlen($r->id()));
        assert((new Main\Request($r->info()))->id() !== $r->id());
        assert((new Main\Request($r->info(true)))->id() === $r->id());

        // scheme
        assert($r->scheme() === 'http');
        assert($r2->scheme() === 'https');

        // setScheme
        assert($r2->setScheme('ftp') === $r2);
        assert($r2->scheme() === 'ftp');
        assert($r->port() === 80);
        assert($r->setScheme('https') === $r);
        assert($r->port() === 443);
        assert($r->setScheme('http') === $r);

        // user
        assert($r->user() === null);
        assert($r2->user() === 'bla');

        // setUser
        assert($r2->setUser('lil') === $r2);
        assert($r2->user() === 'lil');

        // pass
        assert($r->pass() === null);
        assert($r2->pass() === 'LOL');

        // setPass
        assert($r2->setPass('lal') === $r2);
        assert($r2->pass() === 'lal');

        // host
        assert($r->host() === 'google.com');
        assert($r3->host() === Base\Request::host());

        // setHost
        assert($r2->setHost('james.lol') === $r2);
        assert($r2->host() === 'james.lol');

        // isSchemeHostCurrent
        assert(!$r->isSchemeHostCurrent());
        assert($current->isSchemeHostCurrent());

        // port
        assert($r->port() === 80);
        assert($r2->port() === 9090);

        // setPort
        assert($r2->setPort(9089) === $r2);
        assert($r2->port() === 9089);

        // path
        assert($r->path() === '/lavieestlaide');
        assert($r5->path() === '/test/éol/la vie/i.php');
        assert($r5->path(true) === 'test/éol/la vie/i.php');
        assert($file->path() === '/ok');

        // setPath
        assert($r2->setPath('/rienNeVa/plus.txt') === $r2);
        assert($r2->path() === '/rienNeVa/plus.txt');

        // pathStripStart
        assert($r->pathStripStart() === 'lavieestlaide');

        // pathinfo
        assert(count($r->pathinfo()) === 3);

        // changePathinfo
        assert($r9->changePathinfo(['basename'=>'blop.text'])->path() === '/en/la/viees/blop.text');

        // keepPathinfo
        assert($r9->keepPathinfo(['dirname'])->path() === '/en/la/viees');

        // removePathinfo
        assert($r9->removePathinfo(['dirname'])->path() === '/viees');

        // dirname
        assert($r->dirname() === '/');

        // changeDirname
        assert($r9->changeDirname('lavie/est/belle')->path() === '/lavie/est/belle/viees');

        // addDirname
        assert($r9->addDirname('ok/toi')->path() === '/lavie/est/belle/ok/toi/viees');

        // removeDirname
        assert($r9->removeDirname()->path() === '/viees');

        // basename
        assert($r->basename() === 'lavieestlaide');

        // addBasename
        assert($r9->addBasename('lol.txt')->path() === '/viees/lol.txt');

        // changeBasename
        assert($r9->changeBasename('lol2.txt')->path() === '/viees/lol2.txt');

        // removeBasename
        assert($r9->removeBasename()->path() === '/viees');

        // filename
        assert($r->filename() === 'lavieestlaide');

        // addFilename
        assert($r9->addFilename('testa')->path() === '/viees/testa');

        // changeFilename
        assert($r9->changeFilename('testa2')->path() === '/viees/testa2');

        // removeFilename
        assert($r9->removeFilename()->path() === '/viees');

        // extension
        assert($r->extension() === null);
        assert($r8->extension() === 'jpg');

        // addExtension
        assert($r9->addExtension('jpg')->path() === '/viees/.jpg');

        // changeExtension
        assert($r9->changeExtension('png')->path() === '/viees/.png');

        // removeExtension
        assert($r9->removeExtension()->path() === '/viees');

        // mime
        assert($r->mime() === null);
        assert($r8->mime() === 'image/jpeg');

        // addLang
        assert($r9->addLang('fr')->path() === '/fr/viees');

        // changeLang
        assert($r9->changeLang('en')->path() === '/en/viees');

        // removeLang
        assert($r9->removeLang('fr')->path() === '/viees');

        // pathPrepend
        assert($r9->pathPrepend('ok/la/vie')->path() === '/ok/la/vie/viees');

        // pathAppend
        assert($r9->pathAppend('ok/la/vie')->path() === '/ok/la/vie/viees/ok/la/vie');

        // pathExplode
        assert($r->pathExplode() === ['lavieestlaide']);
        assert(count($r8->pathExplode()) === 2);

        // pathGet
        assert($r->pathGet(0) === 'lavieestlaide');

        // pathCount
        assert($r->pathCount() === 1);

        // pathSlice
        assert($r->pathSlice(0,2) === ['lavieestlaide']);
        assert(count($r8->pathSlice(0,2)) === 2);

        // pathSplice
        assert($r9->pathSplice(0,3,['lol'])->path() === '/lol/viees/ok/la/vie');

        // pathInsert
        assert($r9->pathInsert(1,['lol2'])->path() === '/lol/lol2/viees/ok/la/vie');

        // pathMatch
        assert($r8->pathMatch() === 'lavieestblel.jpg');
        assert($r->pathMatch() === 'lavieestlaide');

        // query
        assert($r->query() === 'get=laol');
        assert($r3->query() === 'test=ok&james=bla');
        assert($r5->query() === 'james=lala&ka=éo&space=la uy');

        // setQuery
        assert($r3->setQuery('test=2&bla=3&james=oui') === $r3);
        assert($r3->queryArray() === ['test'=>2,'bla'=>3,'james'=>'oui']);
        assert($r3->setQuery(['ok'=>'3','bla'=>3,'ok'=>'yes']));
        assert($r3->query() === 'ok=yes&bla=3');
        assert($r3->queryArray() === ['ok'=>3,'bla'=>3,'ok'=>'yes']);

        // queryArray
        assert($r3->queryArray() === ['ok'=>3,'bla'=>3,'ok'=>'yes']);
        assert($r5->queryArray() === ['james'=>'lala','ka'=>'éo','space'=>'la uy']);

        // getQuery
        assert($r3->getQuery('ok') === 'yes');

        // getsQuery
        assert($r3->getsQuery('ok','bla','z') === ['ok'=>'yes','bla'=>3,'z'=>null]);

        // addQuery
        assert($r3->addQuery('ok2',2)->query() === 'ok=yes&bla=3&ok2=2');

        // setsQuery
        assert($r3->setsQuery(['james'=>'NO'])->query() === 'ok=yes&bla=3&ok2=2&james=NO');

        // unsetQuery
        assert($r3->unsetQuery('james','ok2')->query() === 'ok=yes&bla=3');

        // setArgv
        assert($r7->setArgv(['james','--username=2','--tets=lol','--test=3','--test','-james','ok.php']) === $r7);
        assert($r7->query() === 'username=2&tets=lol&test=');
        assert($r7->queryArray() === ['username'=>2,'tets'=>'lol','test'=>'']);

        // fragment
        assert($r->fragment() === 'lastchance');

        // setFragment
        assert($r2->setFragment('welll') === $r2);
        assert($r2->fragment() === 'welll');

        // lang
        assert($r7->lang() === 'en');
        assert($r8->lang() === 'fr');
        assert($r->lang() === 'en');

        // setLang
        assert($r7->setLang('fr')->lang() === 'fr');
        assert($r7->setLang('zaa')->lang() === null);

        // langHeader

        // setLangHeader

        // schemeHost
        assert($r7->schemeHost() === 'http://google.com');

        // schemeHostPath
        assert($r7->schemeHostPath() === 'http://google.com/en/lavieestblel');

        // hostPath
        assert($r7->hostPath() === 'google.com/en/lavieestblel');

        // pathQuery
        assert($r->pathQuery() === '/lavieestlaide?get=laol');

        // method
        assert($r->method() === 'get');
        assert($post->method() === 'post');

        // setMethod
        assert($r2->setMethod('POST') === $r2);
        assert($r2->method() === 'post');

        // setAjax
        assert($r2->setAjax(false) === $r2);
        assert($r2->isAjax() === false);
        assert($r2->setAjax(true)->header('X-Requested-With') === 'XMLHttpRequest');

        // setSsl
        assert($r3->isSsl() === Base\Request::isSsl());
        assert($r3->setSsl(true) === $r3);
        assert($r3->isSsl());
        assert($r3->scheme() === 'https');

        // post
        assert($r->post() === []);
        $r['-genuine-'] = 'what';
        $r['password'] = 123;
        $r['bla'] = '<b>OK</b>';
        assert($r->post(false,true) === ['-genuine-'=>'what','password'=>123,'bla'=>'OK']);
        assert($r->post(true,true) === ['password'=>123,'bla'=>'OK']);
        assert($post->post()['test'] === 123);
        assert($post->post()['james'] === 'true');
        assert($files->post(true,true,true)['ok'][0] === 'bla.php');
        assert($files->post(true,true,true)['ok'][1]['name'] === 'ok.lala');

        // postJson
        assert(strlen($r->postJson()) === 53);

        // postQuery
        assert(strlen($r->postQuery()) === 51);

        // csrf
        assert(strlen($post->csrf()) === 40);

        // captcha
        assert($post->captcha() === 'abc');

        // setPost
        $r3->setPost(['james'=>'noWay']);
        assert($r3->post() === ['james'=>'noWay']);

        // ip
        assert(Base\Ip::is($r->ip()));

        // setIp
        assert($r2->setIp('192.168.1.1') === $r2);
        assert($r2->ip() === '192.168.1.1');

        // userAgent
        assert($r->userAgent() === null);

        // setUserAgent
        assert($r2->setUserAgent('quid995') === $r2);
        assert($r2->userAgent() === 'quid995');

        // referer
        assert($r->referer() === null);

        // setReferer
        assert($r2->setReferer('https://google.com/test') === $r2);
        assert($r2->referer() === 'https://google.com/test');
        assert($r2->referer(true) === null);
        assert($r2->referer(true,['abc.com','google.com']) === 'https://google.com/test');
        assert($r2->referer(true,['abc.com','google.comz']) === null);
        assert($r2->referer(true,'google.com') === 'https://google.com/test');
        assert(count($r2->headers()) === 4);
        assert($r2->setReferer(null) === $r2);
        assert(count($r2->headers()) === 3);

        // timestamp
        assert(is_int($r->timestamp()));

        // setTimestamp
        assert($r2->setTimestamp(134) === $r2);
        assert($r2->timestamp() === 134);
        assert($r2->absolute() === 'ftp://lil:lal@james.lol:9089/rienNeVa/plus.txt?arg=value#welll');

        // headers
        $count = count($current->headers());
        assert($current->headers());

        // header
        assert(!empty($current->header('accept-language')));

        // setHeaders
        assert(!empty($r->setHeaders(Base\Request::headers())->headers()));

        // addHeaders
        assert($current->addHeaders(['ok'=>'bla']) === $current);

        // setHeader
        assert($current->setHeader('accept-language','*') === $current);
        assert($current->setHeader('bla','*') === $current);
        assert($current->header('BLA') === '*');

        // unsetHeader
        assert($current->unsetHeader('accept-language','host') === $current);
        assert(count($current->headers()) === $count);

        // fingerprint
        assert(!empty($current->fingerprint(['User-Agent'])));
        assert($current->fingerprint(['User-Agent']) === Base\Request::fingerprint(['User-Agent']));

        // browserCap
        assert(count($r2->browserCap()) === 10);

        // browserName
        assert($r2->browserName() === 'Default Browser');

        // browserPlatform
        assert($r2->browserPlatform() === 'unknown');

        // browserDevice
        assert($r2->browserDevice() === 'unknown');

        // setFiles
        assert($setFiles->file('test') === null);
        assert($setFiles->setFiles(['test'=>$filesObj]) === $setFiles);
        assert($setFile->setFiles(['test'=>[4=>$fileObj]]) === $setFile);

        // filesArray
        assert($files->filesArray()['ok'][0]['name'] === 'test.jpg');
        assert(count($files->filesArray()['ok']) === 2);
        assert(count($file->filesArray()['ok']) === 2);
        assert($file->filesArray()['ok']['name'] === 'test.jpg');
        assert(count($setFile->filesArray()['test']) === 5);
        assert(count($setFiles->filesArray()['test']) === 1);
        assert(count($setFile->post(true,true,true)['test']) === 5);
        assert(count($setFiles->post(true,true,true)['test']) === 1);
        assert($setFile->post() === []);

        // files
        assert($setFiles->files('test') instanceof Main\Files);
        assert($setFile->files('test')->isCount(1));

        // file
        assert($setFiles->file('test') instanceof Main\File);
        assert($setFiles->file('test',1) === null);
        assert($setFile->file('test') === null);
        assert($setFile->file('test',4) instanceof Main\File);

        // redirect
        assert($r->redirect() === '/en/lavieestlaide');
        assert($current->redirect() === null);

        // curl
        $curl = $r->curl();
        assert($curl instanceof Main\Res);
        assert(Base\Res::isCurl($curl->resource()));

        // curlExec

        // trigger

        // default
        $currentReset->setHeaders($currentReset->headers());
        assert(count($currentReset->default(Main\Request::$config['default'])) === 8);
        assert($r->default(['method'=>'post'])['method'] === 'post');

        // live
        assert(Main\Request::live() instanceof Main\Request);

        // checkPing

        // inst

        // map
        $r3['james'] = 'ok';
        assert($r3->post() === ['james'=>'ok']);
        $r3['james2'] = 'ok';
        assert($r3->exists('james','james2'));
        assert(!empty($serialize = serialize($r3)));
        assert(unserialize($serialize)->absolute() === $r3->absolute());

        return true;
    }
}
?>