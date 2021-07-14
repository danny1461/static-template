<?php

/**
 * Static Template
 *   By Daniel Flynn - 5/11/2018
 */

define('WEB_ROOT', dirname($_SERVER['SCRIPT_FILENAME']));
define('ISLOCAL',
	in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1'))
);

require WEB_ROOT . '/functions.php';

global $_SITE;
$_SITE = new stdClass();

/*


`7MM"""YMM
  MM    `7
  MM   d    `7Mb,od8 `7Mb,od8 ,pW"Wq.`7Mb,od8
  MMmmMM      MM' "'   MM' "'6W'   `Wb MM' "'
  MM   Y  ,   MM       MM    8M     M8 MM
  MM     ,M   MM       MM    YA.   ,A9 MM
.JMMmmmmMMM .JMML.   .JMML.   `Ybmd9'.JMML.



														,,
`7MM"""Mq.                                       mm     db
  MM   `MM.                                      MM
  MM   ,M9  .gP"Ya `7MMpdMAo.  ,pW"Wq.`7Mb,od8 mmMMmm `7MM  `7MMpMMMb.  .P"Ybmmm
  MMmmdM9  ,M'   Yb  MM   `Wb 6W'   `Wb MM' "'   MM     MM    MM    MM :MI  I8
  MM  YM.  8M""""""  MM    M8 8M     M8 MM       MM     MM    MM    MM  WmmmP"
  MM   `Mb.YM.    ,  MM   ,AP YA.   ,A9 MM       MM     MM    MM    MM 8M
.JMML. .JMM.`Mbmmd'  MMbmmd'   `Ybmd9'.JMML.     `Mbmo.JMML..JMML  JMML.YMMMMMb
					 MM                                                6'     dP
				   .JMML.                                              Ybmmmd'
*/

$_SITE->errorLevel = E_ALL ^ E_NOTICE;
if (!ISLOCAL) {
	$_SITE->errorLevel = $_SITE->errorLevel ^ E_WARNING ^ E_STRICT;
}
error_reporting($_SITE->errorLevel);

/*

									 ,,                                ,,                   ,,
`7MMF'                             `7MM      `7MMF'                  `7MM                 `7MM
  MM                                 MM        MM                      MM                   MM
  MM         ,pW"Wq.   ,6"Yb.   ,M""bMM        MM  `7MMpMMMb.  ,p6"bo  MM `7MM  `7MM   ,M""bMM  .gP"Ya  ,pP"Ybd
  MM        6W'   `Wb 8)   MM ,AP    MM        MM    MM    MM 6M'  OO  MM   MM    MM ,AP    MM ,M'   Yb 8I   `"
  MM      , 8M     M8  ,pm9MM 8MI    MM        MM    MM    MM 8M       MM   MM    MM 8MI    MM 8M"""""" `YMMMa.
  MM     ,M YA.   ,A9 8M   MM `Mb    MM        MM    MM    MM YM.    , MM   MM    MM `Mb    MM YM.    , L.   I8
.JMMmmmmMMM  `Ybmd9'  `Moo9^Yo.`Wbmd"MML.    .JMML..JMML  JMML.YMbmd'.JMML. `Mbod"YML.`Wbmd"MML.`Mbmmd' M9mmmP'


*/

foreach (glob(WEB_ROOT . '/inc/*.php') as $path) {
	require $path;
}

if (file_exists(WEB_ROOT . '/libs/vendor/autoloader.php')) {
	require WEB_ROOT . '/libs/vendor/autoloader.php';
}

spl_autoload_register(function($classOrFunction) {
	$filePath = WEB_ROOT . '/libs/' . $classOrFunction . '.php';
	if (is_file($filePath)) {
		include $filePath;
	}
});

/*

					  ,,
  .g8""8q.     mm   `7MM                            `7MMF'   `7MF'
.dP'    `YM.   MM     MM                              `MA     ,V
dM'      `MM mmMMmm   MMpMMMb.  .gP"Ya `7Mb,od8        VM:   ,V ,6"Yb.  `7Mb,od8 ,pP"Ybd
MM        MM   MM     MM    MM ,M'   Yb  MM' "'         MM.  M'8)   MM    MM' "' 8I   `"
MM.      ,MP   MM     MM    MM 8M""""""  MM             `MM A'  ,pm9MM    MM     `YMMMa.
`Mb.    ,dP'   MM     MM    MM YM.    ,  MM              :MM;  8M   MM    MM     L.   I8
  `"bmmd"'     `Mbmo.JMML  JMML.`Mbmmd'.JMML.             VF   `Moo9^Yo..JMML.   M9mmmP'


*/
$_SITE->headMeta = array();
$_SITE->titleSeparator = ' | ';
$_SITE->titleParts = array();
$_SITE->scripts = array(
	'collection' => new Dep_Collection(),
	'header' => array(),
	'footer' => array()
);
$_SITE->stylesheets = array(
	'collection' => new Dep_Collection(),
	'header' => array(),
	'footer' => array()
);
$_SITE->dpmResourcesAdded = false;
$_SITE->elementClasses = array();
$_SITE->config = json_decode(file_get_contents(WEB_ROOT . '/config.json'), true);
$_SITE->all_languages = array_merge(array($_SITE->config['default_language']), $_SITE->config['other_languages']);
$_SITE->is_multilingual = count($_SITE->all_languages) > 1;
$_SITE->current_lang = $_SITE->config['default_language'];

// Process aliases
if ($_SITE->is_multilingual && isset($_SITE->config['aliases'])) {
	$_SITE->aliasBackTrace = array();
	_analyzeAliases($_SITE->config['aliases']);
}

/*


`7MM"""Mq.                                        .M"""bgd
  MM   `MM.                                      ,MI    "Y
  MM   ,M9 ,6"Yb.  `7Mb,od8 ,pP"Ybd  .gP"Ya      `MMb.      .gP"Ya `7Mb,od8 `7M'   `MF'.gP"Ya `7Mb,od8
  MMmmdM9 8)   MM    MM' "' 8I   `" ,M'   Yb       `YMMNq. ,M'   Yb  MM' "'   VA   ,V ,M'   Yb  MM' "'
  MM       ,pm9MM    MM     `YMMMa. 8M""""""     .     `MM 8M""""""  MM        VA ,V  8M""""""  MM
  MM      8M   MM    MM     L.   I8 YM.    ,     Mb     dM YM.    ,  MM         VVV   YM.    ,  MM
.JMML.    `Moo9^Yo..JMML.   M9mmmP'  `Mbmmd'     P"Ybmmd"   `Mbmmd'.JMML.        W     `Mbmmd'.JMML.




`7MMF'   `7MF'
  `MA     ,V
   VM:   ,V ,6"Yb.  `7Mb,od8 ,pP"Ybd
	MM.  M'8)   MM    MM' "' 8I   `"
	`MM A'  ,pm9MM    MM     `YMMMa.
	 :MM;  8M   MM    MM     L.   I8
	  VF   `Moo9^Yo..JMML.   M9mmmP'


*/
$_SITE->siteDir = substr(WEB_ROOT, strlen($_SERVER['DOCUMENT_ROOT'])) . '/';
$_SITE->siteDir = str_replace('\\', '/', $_SITE->siteDir);

$_SITE->baseUrl = 'http' . ($_SERVER['SERVER_PORT'] === '443' ? 's' : '') . '://';
$_SITE->baseUrl .= $_SERVER['SERVER_NAME'];
if (!in_array($_SERVER['SERVER_PORT'], array('80', '443')))
	$_SITE->baseUrl .= ':' . $_SERVER['SERVER_PORT'];
$_SITE->baseUrl .= $_SITE->siteDir;

list($_SITE->requestUri) = explode('?', $_SERVER['REQUEST_URI'], 2);
$_SITE->requestUri = substr($_SITE->requestUri, strlen($_SITE->siteDir));
$_SITE->requestUri = rtrim($_SITE->requestUri, '/');
$_SITE->requestUri = urldecode($_SITE->requestUri);
$_SITE->rawRequestUri = $_SITE->requestUri;

// Multilingual checks
if ($_SITE->is_multilingual) {
	foreach ($_SITE->all_languages as $prefix) {
		if (preg_match("/^{$prefix}(?:\\/|$)/", $_SITE->requestUri)) {
			$_SITE->current_lang = $prefix;
			$_SITE->requestUri = substr($_SITE->requestUri, strlen($prefix));
			$_SITE->requestUri = ltrim($_SITE->requestUri, '/');
			break;
		}
	}

	$_SITE->rawRequestUri = $_SITE->requestUri;

	if ($_SITE->current_lang != $_SITE->config['code_language']) {
		$_SITE->requestUri = translateUrl($_SITE->requestUri);
	}
}

/*

																	,,
`7MM"""Yb.                          `7MMM.     ,MMF'              `7MM
  MM    `Yb.                          MMMb    dPMM                  MM
  MM     `Mb  .gP"Ya `7M'   `MF'      M YM   ,M MM  ,pW"Wq.    ,M""bMM  .gP"Ya
  MM      MM ,M'   Yb  VA   ,V        M  Mb  M' MM 6W'   `Wb ,AP    MM ,M'   Yb
  MM     ,MP 8M""""""   VA ,V         M  YM.P'  MM 8M     M8 8MI    MM 8M""""""
  MM    ,dP' YM.    ,    VVV          M  `YM'   MM YA.   ,A9 `Mb    MM YM.    ,
.JMMmmmdP'    `Mbmmd'     W         .JML. `'  .JMML.`Ybmd9'   `Wbmd"MML.`Mbmmd'


*/
if ($_SITE->config['dev_mode']) {
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'devMode') {
		try {
			$fileName = $_POST['file'];
			if (is_multilingual()) {
				$fileName = current_lang() . '/' . $fileName;
			}

			$fileName = WEB_ROOT . '/data/' . $fileName;
			$data = read_data($fileName, false);
			_drillDown($data, $_POST['varName'], $_POST['newContent']);
			file_put_contents($fileName, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			
			echo 'success';
		}
		catch (Exception $e) {
			http_response_code(500);
		}
		exit;
	}

	script_enqueue('dev-mode', publicUrl('/dev_mode/dev.js'));
	stylesheet_enqueue('dev-mode', publicUrl('/dev_mode/dev.css'));
}

/*

										 ,,    ,,
`7MMF'  `7MMF'                         `7MM  `7MM              `7MMF'   `7MF'`7MM"""Mq.  `7MMF'
  MM      MM                             MM    MM                MM       M    MM   `MM.   MM
  MM      MM   ,6"Yb.  `7MMpMMMb.   ,M""bMM    MM  .gP"Ya        MM       M    MM   ,M9    MM
  MMmmmmmmMM  8)   MM    MM    MM ,AP    MM    MM ,M'   Yb       MM       M    MMmmdM9     MM
  MM      MM   ,pm9MM    MM    MM 8MI    MM    MM 8M""""""       MM       M    MM  YM.     MM
  MM      MM  8M   MM    MM    MM `Mb    MM    MM YM.    ,       YM.     ,M    MM   `Mb.   MM
.JMML.  .JMML.`Moo9^Yo..JMML  JMML.`Wbmd"MML..JMML.`Mbmmd'        `bmmmmd"'  .JMML. .JMM..JMML.


*/
$_SITE->template = $_SITE->requestUri;

if (empty($_SITE->template)) {
	$_SITE->template = 'index';
}

$_SITE->templateCandidates = array(
	$_SITE->is_multilingual ? $_SITE->template . '--' . $_SITE->current_lang : '',
	$_SITE->template,

	$_SITE->is_multilingual ? $_SITE->template . '/' . 'index--' . $_SITE->current_lang : '',
	$_SITE->template . '/index',
);

foreach (array('_template', '404') as $toConsider) {
	$dir = $_SITE->template;

	while ($dir) {
		$dir = ltrim(dirname($dir), '.');

		if ($_SITE->is_multilingual) {
			$_SITE->templateCandidates[] = ($dir ? ($dir . '/') : '') . $toConsider . '--' . $_SITE->current_lang;
		}

		$_SITE->templateCandidates[] = ($dir ? ($dir . '/') : '') . $toConsider;
	}
}

$_SITE->templateCandidates = array_filter($_SITE->templateCandidates);

foreach ($_SITE->templateCandidates as $path) {
	if (is_file(WEB_ROOT . '/templates/pages/' . $path . '.php')) {
		$_SITE->template = $path;
		$_SITE->rejected_template = false;

		ob_start();
		require WEB_ROOT . '/templates/pages/' . $_SITE->template . '.php';

		if ($_SITE->rejected_template) {
			ob_end_clean();
			continue;
		}

		exit;
	}
}