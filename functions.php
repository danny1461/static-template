<?php

if (!defined('WEB_ROOT')) {
	http_response_code(404);
	exit;
}

/*

												 ,,
`7MM"""YMM                                mm     db
  MM    `7                                MM
  MM   d `7MM  `7MM  `7MMpMMMb.  ,p6"bo mmMMmm `7MM  ,pW"Wq.`7MMpMMMb.  ,pP"Ybd
  MM""MM   MM    MM    MM    MM 6M'  OO   MM     MM 6W'   `Wb MM    MM  8I   `"
  MM   Y   MM    MM    MM    MM 8M        MM     MM 8M     M8 MM    MM  `YMMMa.
  MM       MM    MM    MM    MM YM.    ,  MM     MM YA.   ,A9 MM    MM  L.   I8
.JMML.     `Mbod"YML..JMML  JMML.YMbmd'   `Mbmo.JMML.`Ybmd9'.JMML  JMML.M9mmmP'


*/

$_SITE->hooks = [];

/**
 * Queues a function to allow modification of hook data
 * 
 * @param mixed $name 
 * @param mixed $callable 
 * @param int $priority 
 * @return void 
 */
function addFilter($name, $callable, $priority = 10) {
	global $_SITE;

	if (!isset($_SITE->hooks[$name])) {
		$_SITE->hooks[$name] = [
			'sorted' => false,
			'calls' => [],
			'order' => []
		];
	}

	$_SITE->hooks[$name]['sorted'] = false;
	$_SITE->hooks[$name]['calls'][] = $callable;
	$_SITE->hooks[$name]['order'][] = $priority;
}

/**
 * Queues a function to be called on a particular hook
 * 
 * @param mixed $name 
 * @param mixed $callable 
 * @param int $priority 
 * @return void 
 */
function addAction($name, $callable, $priority = 10) {
	addFilter($name, $callable, $priority);
}

/**
 * Executes a hook and returns the original or modified data
 * 
 * @param mixed $name 
 * @param mixed $args 
 * @return mixed 
 */
function doFilter($name, ...$args) {
	global $_SITE;

	if (!isset($_SITE->hooks[$name])) {
		return reset($args);
	}

	if (!$_SITE->hooks[$name]['sorted']) {
		array_multisort($_SITE->hooks[$name]['order'], $_SITE->hooks[$name]['calls']);
		$_SITE->hooks[$name]['sorted'] = true;
	}

	foreach ($_SITE->hooks[$name]['calls'] as $fn) {
		$args[0] = $fn(...$args);
	}

	return $args[0];
}

/**
 * Executes a hook
 * 
 * @param mixed $name 
 * @param mixed $args 
 * @return void 
 */
function doAction($name, ...$args) {
	global $_SITE;

	if (!isset($_SITE->hooks[$name])) {
		return;
	}

	if (!$_SITE->hooks[$name]['sorted']) {
		array_multisort($_SITE->hooks[$name]['order'], $_SITE->hooks[$name]['calls']);
		$_SITE->hooks[$name]['sorted'] = true;
	}

	foreach ($_SITE->hooks[$name]['calls'] as $fn) {
		$fn(...$args);
	}
}

$_SITE->portals = [];
$_SITE->portalData = [];
$_SITE->portalDepth = [];

/**
 * Queues data to be output elsewhere
 * 
 * @param mixed $name 
 * @return void 
 */
function portal_to($name, $data = null) {
	global $_SITE;

	if (is_null($data)) {
		$_SITE->portals[] = $name;
		$_SITE->portalDepth[] = 0;
		ob_start();
	}
	else {
		if (!isset($_SITE->portalData[$name])) {
			$_SITE->portalData[$name] = '';
		}
	
		$_SITE->portalData[$name] .= $data;
	}
}

/**
 * Wipe out data that might have been queued
 * 
 * @param mixed $name 
 * @return void 
 */
function portal_clear_data($name) {
	global $_SITE;
	unset($_SITE->portalData[$name]);
}

/**
 * Ends a portal buffer
 * 
 * @return void 
 */
function portal_end() {
	global $_SITE;

	if (!count($_SITE->portals)) {
		while (ob_get_length()) {
			ob_get_clean();
		}

		throw new Exception('No portal buffer started');
	}

	$name = array_pop($_SITE->portals);
	array_pop($_SITE->portalDepth);

	if (!isset($_SITE->portalData[$name])) {
		$_SITE->portalData[$name] = '';
	}

	$_SITE->portalData[$name] .= ob_get_clean();
}

/**
 * Returns any queued data
 * 
 * @param mixed $name 
 * @return mixed 
 */
function portal_get_data($name) {
	global $_SITE;

	return isset($_SITE->portalData[$name])
		? $_SITE->portalData[$name]
		: '';
}

addAction('before_include', function() {
	global $_SITE;

	if (count($_SITE->portalDepth)) {
		$_SITE->portalDepth[count($_SITE->portalDepth) - 1]++;
	}
});

addAction('after_include', function() {
	global $_SITE;

	if (count($_SITE->portalDepth)) {
		$lastNdx = count($_SITE->portalDepth) - 1;
		$_SITE->portalDepth[$lastNdx]--;

		if ($_SITE->portalDepth[$lastNdx] < 0) {
			while (ob_get_length()) {
				ob_get_clean();
			}

			throw new Exception('Portal contents need to be ended in the same file they are started');
		}
	}
});

/**
 * Loads and echos a layout header.php file
 * 
 * @param string $name
 * @return void
 */
function get_header($name = 'default') {
	$candidates = array_filter(array(
		is_multilingual() ? 'header--' . current_lang() : '',
		'header'
	));

	foreach ($candidates as $fileName) {
		if (is_file(WEB_ROOT . "/templates/layouts/{$name}/{$fileName}.php")) {
			require WEB_ROOT . "/templates/layouts/{$name}/{$fileName}.php";
			break;
		}
	}
}

/**
 * Loads and echos a layout footer.php file
 * 
 * @param string $name 
 * @return void 
 */
function get_footer($name = 'default') {
	$candidates = array_filter(array(
		is_multilingual() ? 'footer--' . current_lang() : '',
		'footer'
	));

	foreach ($candidates as $fileName) {
		if (is_file(WEB_ROOT . "/templates/layouts/{$name}/{$fileName}.php")) {
			require WEB_ROOT . "/templates/layouts/{$name}/{$fileName}.php";
			break;
		}
	}
}

/**
 * Loads and returns a partial template from /templates/partials with optional data parameters
 * 
 * Tip: Use the ViewModel pattern by placing your class in /libs and rendering it with the data param to ['model' => VIEW_MODEL::from(['KEY' => ...])]
 * At the top of your partial, you can add a docblock with:
 * @var VIEW_MODEL $model
 * to enjoy intellisense autocomplete
 * 
 * ViewModels can extend Core\ViewModel for simplicity
 * 
 * @param mixed $name template name minus .php
 * @param array $data optional data payload
 * @return string
 */
function get_partial($name, $data = array()) {
	$_file = WEB_ROOT . "/templates/partials/{$name}.php";

	doAction('before_include', $_file);

	extract($data);
	require WEB_ROOT . "/templates/partials/{$name}.php";

	doAction('after_include', $_file);
}

/**
 * Prepends the site's baseurl to the $url parameter. If the $lang param is supplied, it will be prepended appropriately.
 * 
 * @param string $url 
 * @param false|string $lang 
 * @return string 
 */
function baseUrl($url = '', $lang = false) {
	global $_SITE;

	$url = ltrim($url, '/');

	if ($_SITE->is_multilingual) {
		if ($lang === false) {
			$lang = $_SITE->current_lang;
		}

		if ($lang != $_SITE->config['code_language']) {
			$url = translateUrl($url, $lang);
		}
		
		if ($lang != $_SITE->config['default_language'] || $_SITE->config['enforce_language_prefix']) {
			$url = $lang . '/' . $url;
		}
	}
	
	return $_SITE->baseUrl . $url;
}

/**
 * Prepends the site's baseurl and '/public/ to the $url parameter.
 * 
 * @param string $url 
 * @return string 
 */
function publicUrl($url = '') {
	global $_SITE;
	return $_SITE->baseUrl . 'public/' . ltrim($url, '/');
}

/**
 * Returns a file system path to requested path
 * 
 * @param string $path 
 * @return string
 */
function filePath($path = '') {
	return WEB_ROOT . '/' . ltrim($path, '/\\');
}

/**
 * Returns a file system path to the request public asset file
 * 
 * @param string $path 
 * @return string 
 */
function publicFilePath($path = '') {
	return WEB_ROOT . '/public/' . ltrim($path, '/\\');
}

/**
 * Prepends a site's baseurl and '/public/' and appends a timestamp query var to the $url parameter
 * 
 * @param string $url 
 * @return string 
 */
function publicHashedUrl($url = '') {
	return publicUrl($url) . '?t=' . filemtime(publicFilePath($url));
}

/**
 * Converts a multilingual url to another language
 * 
 * @param mixed $url 
 * @param false|string $lang 
 * @return string 
 */
function translateUrl($url, $lang = false) {
	global $_SITE;

	if ($lang === false) {
		// Translate from backtraces
		$url = explode('/', $url);

		$tmp = $_SITE->aliasBackTrace;
		foreach ($url as $ndx => $part) {
			if (isset($tmp[$part])) {
				if (is_array($tmp[$part])) {
					$url[$ndx] = $tmp[$part]['replacement'];
					$tmp = $tmp[$part]['aliases'];
					continue;
				}
				else {
					$url[$ndx] = $tmp[$part];
				}
			}
			
			break;
		}

		return implode('/', $url);
	}
	elseif ($lang != $_SITE->config['code_language']) {
		// Translate to from aliases
		$url = explode('/', $url);

		$tmp = $_SITE->config['aliases'];
		foreach ($url as $ndx => $part) {
			if (isset($tmp[$part])) {
				if (isset($tmp[$part][$lang])) {
					$url[$ndx] = $tmp[$part][$lang];
				}

				if (isset($tmp[$part]['aliases'])) {
					$tmp = $tmp[$part]['aliases'];
					continue;
				}
			}
			
			break;
		}

		return implode('/', $url);
	}

	return $url;
}

/**
 * Returns whether the site is setup to be multilingual
 * 
 * @return bool 
 */
function is_multilingual() {
	global $_SITE;
	return $_SITE->is_multilingual;
}

/**
 * Returns an array of all language codes supported
 * 
 * @return array 
 */
function get_languages() {
	global $_SITE;
	return $_SITE->config['language_names'];
}

function _analyzeAliases($data, &$parent = null, $lang = false) {
	global $_SITE;

	if ($parent === null) {
		unset($parent);
		$parent = &$_SITE->aliasBackTrace;
	}

	foreach ($data as $defaultPathPart => $translatedData) {
		if (isset($translatedData['aliases']) && count($translatedData) == 1) {
			$parent[$defaultPathPart] = array(
				'replacement' => $defaultPathPart,
				'aliases' => array()
			);

			_analyzeAliases($translatedData['aliases'], $parent[$defaultPathPart]['aliases'], $lang);
		}
		else {
			foreach (($lang ?: $_SITE->all_languages) as $langPrefix) {
				if ($langPrefix == $_SITE->config['code_language']) {
					continue;
				}
	
				if (isset($translatedData[$langPrefix])) {
					if (isset($translatedData['aliases'])) {
						$parent[$translatedData[$langPrefix]] = array(
							'replacement' => $defaultPathPart,
							'aliases' => array()
						);
	
						_analyzeAliases($translatedData['aliases'], $parent[$translatedData[$langPrefix]]['aliases'], $lang ?: array($langPrefix));
					}
					else {
						$parent[$translatedData[$langPrefix]] = $defaultPathPart;
					}
				}
			}
		}
	}
}

function _drillDown(&$data, $key, $val = null) {
	if (is_string($key)) {
		$key = explode('.', $key);
	}

	$len = count($key);
	for ($i = 0; $i < $len; $i++) {
		if (preg_match('/^([^\\[]+)\\[([^\\]]+)\\]$/', $key[$i], $matches)) {
			array_splice($key, $i + 1, 0, $matches[2]);
			$key[$i] = $matches[1];
			$i++;
			$len++;
		}
	}

	$lastNdx = count($key) - 1;
	foreach ($key as $ndx => $keyPart) {
		if ($ndx == $lastNdx) {
			if ($val === null) {
				return array_key_exists($keyPart, $data) ? $data[$keyPart] : null;
			}
			else {
				$data[$keyPart] = $val;
			}
		}
		else {
			if (!array_key_exists($keyPart, $data)) {
				if ($val !== null) {
					$data[$keyPart] = array();
				}
				else {
					return null;
				}
			}

			$tmp = &$data;
			unset($data);
			$data = &$tmp[$keyPart];
			unset($tmp);
		}
	}
}

/**
 * Fetches a value from config.json by key path
 * 
 * @param string|array $var 
 * @return mixed 
 */
function get_config($var) {
	global $_SITE;

	return _drillDown($_SITE->config['custom'], $var);
}

/**
 * Returns a part or set of parts from the request uri. Parts are separated by forward slashes
 * 
 * @param false|int $uriPart if provided, will fetch the numerically index part of the request uri
 * @param bool $includeFollowing if true, will fetch the indexed part and those following it
 * @return string
 */
function get_request_uri($uriPart = false, $includeFollowing = false) {
	return get_raw_request_uri($uriPart, $includeFollowing, 'requestUri');
}

/**
 * Returns a part or set of parts from the original request uri before language processing. Parts are separated by forward slashes
 * 
 * @param false|int $uriPart if provided, will fetch the numerically index part of the request uri
 * @param bool $includeFollowing if true, will fetch the indexed part and those following it
 * @return string
 */
function get_raw_request_uri($uriPart = false, $includeFollowing = false, $prop = 'rawRequestUri') {
	global $_SITE;

	if ($uriPart !== false) {
		$parts = explode('/', $_SITE->$prop);

		if ($uriPart < 0) {
			$uriPart = count($parts) + $uriPart;
		}

		if ($includeFollowing) {
			return implode('/', array_slice($parts, $uriPart));
		}

		return $parts[$uriPart];
	}

	return $_SITE->$prop;
}

/**
 * Fetches the currently active template file
 * 
 * @return string 
 */
function get_template() {
	global $_SITE;
	return $_SITE->template;
}

/**
 * Rejects the current template for rendering. Best paired with the magic _template.php file to probably force a 404 when invalid query parameters are provided.
 * 
 * @return void 
 */
function reject_template() {
	global $_SITE;
	$_SITE->rejected_template = true;
}

/**
 * Returns the list of template files being considered for the current route
 * 
 * @return array 
 */
function get_template_candidates() {
	global $_SITE;
	return $_SITE->templateCandidates;
}

/**
 * Returns the language code of the current request
 * 
 * @return string 
 */
function current_lang() {
	global $_SITE;
	return $_SITE->current_lang;
}

/**
 * Returns the language name of the current request
 * 
 * @return string 
 */
function current_lang_name() {
	global $_SITE;
	return $_SITE->config['language_names'][$_SITE->current_lang];
}

/**
 * Returns the language set in the config as the language format the code files are following. Usually 'en'
 * 
 * @return string 
 */
function code_lang() {
	global $_SITE;
	return $_SITE->config['code_language'];
}

/**
 * Returns whether the current language of the request matches $lang
 * 
 * @param string $lang 
 * @return bool 
 */
function is_lang($lang) {
	return current_lang() == $lang;
}

$_SITE->headMeta = array();

function _transformMetaKey($keyOrProps, $val) {
	if (is_string($keyOrProps)) {
		$key = $keyOrProps;

		if (strpos($keyOrProps, 'og:') === 0) {
			// Facebook
			$keyOrProps = array(
				'property' => $keyOrProps,
				'content' => $val
			);
		}
		else {
			// Twitter and most others
			$keyOrProps = array(
				'name' => $keyOrProps,
				'content' => $val
			);
		}
	}
	else {
		$key = http_build_query($keyOrProps);
	}

	return array(
		$keyOrProps,
		$key
	);
}

/**
 * Appends meta to the buffer
 * 
 * @param mixed $keyOrProps 
 * @param mixed|null $val 
 * @return void 
 */
function meta($keyOrProps, $val = null) {
	global $_SITE;

	list($keyOrProps, $key) = _transformMetaKey($keyOrProps, $val);

	$tag = '<meta';
	foreach ($keyOrProps as $k => $v) {
		$tag .= ' ' . $k . '="' . str_replace('"', '&quot;', $v) . '"';
	}
	$tag .= '/>';
	
	$_SITE->headMeta[$key] = $tag;
}

/**
 * Removes a meta tag from the buffer
 * 
 * @param mixed $keyOrProps 
 * @return void 
 */
function remove_meta($keyOrProps) {
	global $_SITE;

	list($keyOrProps, $key) = _transformMetaKey($keyOrProps, false);

	if (isset($_SITE->headMeta[$key])) {
		unset($_SITE->headMeta[$key]);
	}
}

$_SITE->titleSeparator = ' | ';
$_SITE->titleParts = array();

/**
 * Appends or prepends a part to the meta title
 * 
 * @param false|string|array $parts 
 * @param bool $prefix 
 * @param bool $reset 
 * @return string 
 */
function title_part($parts = false, $prefix = false, $reset = false) {
	global $_SITE;

	if ($reset) {
		$_SITE->titleParts = array();
	}

	if ($parts) {
		if (is_string($parts)) {
			$parts = array($parts);
		}
	
		if ($prefix) {
			$_SITE->titleParts = array_merge($parts, $_SITE->titleParts);
		}
		else {
			$_SITE->titleParts = array_merge($_SITE->titleParts, $parts);
		}
	}
	
	return implode($_SITE->titleSeparator, $_SITE->titleParts);
}

/**
 * The default separator between meta title parts
 * 
 * @param string $sep 
 * @return void 
 */
function title_separator($sep) {
	global $_SITE;
	$_SITE->titleSeparator = $sep;
}

$_SITE->elementClasses = array();

/**
 * Builds list of arbitrary classes for arbitrary elements
 * 
 * @param mixed $name 
 * @param string|array $classes 
 * @param bool $remove 
 * @return string 
 */
function elementClasses($name, $classes = false, $remove = false) {
	global $_SITE;
	if (!isset($_SITE->elementClasses[$name])) {
		$_SITE->elementClasses[$name] = new Core\ElementClasses();
	}

	if ($classes) {
		$action = $remove ? 'remove' : 'add';
		$_SITE->elementClasses[$name]->$action($classes);
	}
	
	return $_SITE->elementClasses[$name]->getClassString();
}

/**
 * Builds list of arbitrary classes for the head element
 * 
 * @param string|array $classes 
 * @param bool $remove 
 * @return string 
 */
function htmlClass($classes = false, $remove = false) {
	return elementClasses('html', $classes, $remove);
}

/**
 * Builds list of arbitrary classes for the body element
 * 
 * @param string|array $classes 
 * @param bool $remove 
 * @return string 
 */
function bodyClass($classes = false, $remove = false) {
	return elementClasses('body', $classes, $remove);
}

$_SITE->scripts = array(
	'collection' => new Core\Dep_Collection(),
	'header' => array(),
	'footer' => array()
);

/**
 * Enqueues a script to be rendered into the head area
 * 
 * @param mixed $name 
 * @param mixed $urlOrSrc 
 * @param array $deps 
 * @param bool $header 
 * @return void 
 */
function script_enqueue($name, $urlOrSrc, $deps = array(), $header = true) {
	global $_SITE;
	
	if (is_bool($urlOrSrc)) {
		$_SITE->scripts[$urlOrSrc ? 'header' : 'footer'][] = $name;
	}
	else {
		$_SITE->scripts['collection']->addResource($name, $urlOrSrc, $deps);
		$_SITE->scripts[$header ? 'header' : 'footer'][] = $name;
	}
}

/**
 * Teaches the framework about a script and any dependencies it has
 * 
 * @param mixed $name 
 * @param mixed $urlOrSrc 
 * @param array $deps 
 * @return void 
 */
function script_register($name, $urlOrSrc, $deps = array()) {
	global $_SITE;
	$_SITE->scripts['collection']->addResource($name, $urlOrSrc, $deps);
}

$_SITE->stylesheets = array(
	'collection' => new Core\Dep_Collection(),
	'header' => array(),
	'footer' => array()
);

/**
 * Enqueues a stylesheet to be rendered into the head area
 * 
 * @param mixed $name 
 * @param mixed $url 
 * @param array $deps 
 * @param bool $header 
 * @return void 
 */
function stylesheet_enqueue($name, $url, $deps = array(), $header = true) {
	global $_SITE;
	
	if (is_bool($url)) {
		$_SITE->stylesheets[$url ? 'header' : 'footer'][] = $name;
	}
	else {
		$_SITE->stylesheets['collection']->addResource($name, $url, $deps);
		$_SITE->stylesheets[$header ? 'header' : 'footer'][] = $name;
	}
}

/**
 * Teaches the framework about a stylesheet and any dependencies it has
 * @param mixed $name 
 * @param mixed $url 
 * @param array $deps 
 * @return void 
 */
function stylesheet_register($name, $url, $deps = array()) {
	global $_SITE;
	$_SITE->stylesheets['collection']->addResource($name, $url, $deps);
}

function _output_scripts($needed) {
	global $_SITE;
	
	$resources = $_SITE->scripts['collection']->getOrderedList($needed);
	foreach ($resources as $res) {
		if (strpos($res, 'http') === 0 || strpos($res, '//') === 0) {
			echo '<script' . ' type="text/javascript" src="' . $res . '"></script>' . PHP_EOL;
		}
		else {
			echo '<script' . ' type="text/javascript">' . PHP_EOL . $res . PHP_EOL . '</script>' . PHP_EOL;
		}
	}
}

function _output_stylesheets($needed) {
	global $_SITE;
	
	$resources = $_SITE->stylesheets['collection']->getOrderedList($needed);
	foreach ($resources as $res) {
		if (strpos($res, 'http') === 0 || strpos($res, '//') === 0) {
			echo '<link rel="stylesheet" href="' . $res . '">' . PHP_EOL;
		}
		else {
			echo '<style>' . PHP_EOL . $res . PHP_EOL . '</style>' . PHP_EOL;
		}
	}
}

/**
 * Echoes all buffered head content
 * 
 * @return void 
 */
function header_resources() {
	global $_SITE;
	_output_stylesheets($_SITE->stylesheets['header']);
	_output_scripts($_SITE->scripts['header']);
	echo implode(PHP_EOL, $_SITE->headMeta);
	echo portal_get_data('header');
}

/**
 * Echoes all buffered footer content
 * @return void 
 */
function footer_resources() {
	global $_SITE;
	_output_stylesheets($_SITE->stylesheets['footer']);
	_output_scripts($_SITE->scripts['footer']);
	echo portal_get_data('footer');
}

/**
 * Loads a CSV, JSON, or INI file from the /data directory and returns the data
 * 
 * @param mixed $fileName 
 * @param bool $useCache 
 * @param mixed|null $options 
 * @return mixed 
 */
function read_data($fileName, $useCache = true, $options = null) {
	global $_SITE;

	if ($useCache && isset($_SITE->dataCache[$fileName])) {
		return $_SITE->dataCache[$fileName];
	}

	$tmp = new Core\Static_Data($fileName, $options);
	$tmp = $tmp->readFile();

	if ($useCache) {
		$_SITE->dataCache[$fileName] = $tmp;
	}

	return $tmp;
}

$_SITE->dpmResourcesAdded = false;

/**
 * Debugs the given input by echoing debug content for viewing by the dev in the browser
 * 
 * @param mixed $input 
 * @param int $depth 
 * @return void 
 */
function dpm($input, $depth = 5)
{
	if (!ISLOCAL)
		return;
	
	global $_SITE;
	
	if (!$_SITE->dpmResourcesAdded) {
		$txt = <<<INLINE
	body .da-devel {
		display: none;
		border: 1px solid #f00;
		font-family: Menlo,Monaco,Consolas,"Courier New",monospace;
		font-size: 1em;
		background-color: #f5f5f5;
		padding: 8px;
		overflow: auto;
	}
	body .da-devel pre {
		border: 0;
		padding: 0;
		overflow: visible;
	}
	body .da-devel .trace {
		display: block;
		background-color: #e8bc2c;
		padding: 0 8px;
	}
	body .da-devel .the-rest {
		display: none;
	}
	body .da-devel .trace.reveal .the-rest {
		display: block;
	}
	body .da-devel button {
		float: right;
		border: 0;
		background-color: transparent;
		color: #00F;
		outline: 0;
	}
	body.da-devel .da-devel {
		display: block;
	}
INLINE;
		stylesheet_enqueue('devel', $txt, array(), false);
		$txt = <<<SCRIPT
	(function(d) {
		var old = d.onkeydown;
		d.onkeydown = function(e) {
			if (old)
				old(e);
			if (e.ctrlKey && e.keyCode == 123)
			{
				var classes = d.body.className.split(' ');
				if (classes.indexOf('da-devel') != -1)
				{
					classes.splice(classes.indexOf('da-devel'), 1);
					d.cookie = 'da-devel=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
				}
				else
				{
					classes.push('da-devel');
					d.cookie = 'da-devel=1';
				}
				d.body.className = classes.join(' ');
			}
		};

		d.addEventListener('DOMContentLoaded', function() {
			if (d.cookie.indexOf('da-devel=1') != -1) {
				d.body.className += ' da-devel';
			}

			Array.prototype.slice.call(d.querySelectorAll('div.da-devel .show-trace')).forEach(function(el) {
				el.addEventListener('click', function() {
					var parent = el.parentNode.parentNode;
					if (parent.className.indexOf('reveal') == -1)
					{
						el.innerText = 'Hide Trace';
						parent.className += ' reveal';
					}
					else
					{
						el.innerText = 'Show Trace';
						parent.className = 'trace';
					}
				});
			});
		});
	})(document);
SCRIPT;
		script_enqueue('devel', $txt, array(), false);
		$_SITE->dpmResourcesAdded = true;
	}
	
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$orig = array(
		'xdebug.var_display_max_depth',
		'xdebug.overload_var_dump'
	);
	foreach ($orig as $key => $val)
	{
		$orig[$val] = ini_get($val);
		unset($orig[$key]);
	}
	
	ini_set('xdebug.var_display_max_depth', $depth);
	ini_set('xdebug.overload_var_dump', 1);
	ob_start();
	var_dump($input);
	
	foreach ($orig as $key => $val)
	{
		if ($val !== false)
			ini_set($key, $val);
	}
	
	$output = ob_get_clean();
	
	echo '<div class="da-devel">';
	echo '<div class="trace">';
	foreach ($trace as $ndx => $item)
	{
		echo '<div';
		if ($ndx)
			echo ' class="the-rest"';
		echo '>';
		
		// Is it from our theme? Those will be highlighted
		$themeCall = preg_match('/[\\/\\\\]templates[\\/\\\\]/', $item['file']);
		if ($themeCall)
			echo '<strong>';
		echo $item['file'];
		if ($themeCall)
			echo '</strong>';
		
		echo ':' . $item['line'] . ':' . $item['function'];
		if (!$ndx && count($trace) > 1)
			echo '<button class="show-trace">Show Trace</button>';
		echo '</div>';
	}
	echo '</div>';
	echo $orig['xdebug.overload_var_dump'] ? $output : ('<pre>' . htmlentities($output));
	if (!$orig['xdebug.overload_var_dump'])
		echo '</pre>';
	echo '</div>';
}

/**
 * Uses the current request path to fetch stored content for a page by variable name
 * 
 * @param mixed $var 
 * @param string $fileName 
 * @param mixed|null $default 
 * @return mixed 
 */
function getPageData($var, $fileName = '', $default = null) {
	global $_SITE;

	if (is_array($var)) {
		$var = implode('.', $var);
	}

	$filePath = ($fileName ?: get_request_uri() ?: 'index') . '.json';
	$realPath = WEB_ROOT . '/data/' . $filePath;
	$langDirPrefixes = array('');
	if (is_multilingual()) {
		$realPath = WEB_ROOT . '/data/' . current_lang() . '/' . $filePath;
		$langDirPrefixes = array_map(function($item) {
			return $item . '/';
		}, $_SITE->all_languages);
	}

	$data = null;

	if (!is_file($realPath)) {
		if ($_SITE->config['dev_mode']) {
			foreach ($langDirPrefixes as $langDirPrefix) {
				$dirs = ltrim(dirname($langDirPrefix . $filePath), '.');
				if ($dirs) {
					$dirs = explode('/', $dirs);
					$tmp = '';
					foreach ($dirs as $dir) {
						$tmp .= '/' . $dir;
						if (!is_dir(WEB_ROOT . '/data' . $tmp)) {
							mkdir(WEB_ROOT . '/data' . $tmp);
						}
					}
				}
			}
		}
		else {
			return '';
		}
	}
	else {
		$data = read_data($realPath, !$_SITE->config['dev_mode']);
	}
	
	$result = null;

	if ($data !== null) {
		$result = _drillDown($data, $var);
	}

	if ($result === null && $_SITE->config['dev_mode']) {
		if ($default === null) {
			$result = '<strong>{' . $var . '}</strong>';
		}
		else {
			$result = $default;
		}

		foreach ($langDirPrefixes as $langDirPrefix) {
			$langFilePath = WEB_ROOT . '/data/' . $langDirPrefix . $filePath;

			if (is_file($langFilePath)) {
				$data = read_data($langFilePath, false);
			}
			else {
				$data = array();
			}
			
			_drillDown($data, $var, $result);
			file_put_contents($langFilePath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		}
	}

	if ($_SITE->config['dev_mode'] && is_string($result)) {
		if ($result[strlen($result) - 1] != '>') {
			$result = '<span class="page-data--wrapper">' . $result . '</span>';
		}
		$result = '<span class="begin--mark"></span>' . $result . '<span class="end--mark" data-var="' . $var .'" data-file="' . $filePath . '">Edit</span>';
	}

	return $result;
}

/*

			  ,,
  .g8"""bgd `7MM
.dP'     `M   MM
dM'       `   MM   ,6"Yb.  ,pP"Ybd ,pP"Ybd  .gP"Ya  ,pP"Ybd
MM            MM  8)   MM  8I   `" 8I   `" ,M'   Yb 8I   `"
MM.           MM   ,pm9MM  `YMMMa. `YMMMa. 8M"""""" `YMMMa.
`Mb.     ,'   MM  8M   MM  L.   I8 L.   I8 YM.    , L.   I8
  `"bmmmd'  .JMML.`Moo9^Yo.M9mmmP' M9mmmP'  `Mbmmd' M9mmmP'


*/

/**
 * Class to load environment variables from local .env files
 */
class EnvShim implements ArrayAccess, JsonSerializable {
	private $data = null;

	public function jsonSerialize() {
		$this->load();

		return $this->data;
	}

	public function offsetExists($offset) {
		$this->load();

		return array_key_exists($offset, $this->data);
	}

	public function offsetGet($offset) {
		$this->load();

		return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		throw new Exception('Should not set environment variables');
	}

	public function offsetUnset($offset) {
		throw new Exception('Should not unset environment variables');
	}

	public function __get($offset) {
		$this->load();

		return $this->data[$offset];
	}

	public function __set($offset, $val) {
		throw new Exception('Should not set environment variables');
	}

	public function __isset($name) {
		$this->load();

		return isset($this->data[$name]);
	}

	public function __unset($name) {
		throw new Exception('Should not unset environment variables');
	}

	private function load() {
		if (!is_null($this->data)) {
			return;
		}

		$filePaths = [
			WEB_ROOT . '/local.env',
			WEB_ROOT . '/' . ENVIRONMENT . '.env'
		];

		foreach ($filePaths as $path) {
			if (file_exists($path)) {
				$this->data = parse_ini_file($path, true, INI_SCANNER_TYPED) ?: [];
				break;
			}
		}
	}

	public static function init() {
		global $_ENV;
		$_ENV = new self();
	}
}