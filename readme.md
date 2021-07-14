See an example of this template in the **sample** branch

# Version 2.0.0:

- [Version 2.0.0:](#version-200)
	- [Layouts ](#layouts)
			- [Default Layouts](#default-layouts)
			- [Custom Layouts](#custom-layouts)
	- [Templates ](#templates)
	- [Assets ](#assets)
	- [Data ](#data)
		- [Options](#options)
	- [Helper Functions ](#helper-functions)

## Layouts <a name="layouts"></a>

#### Default Layouts
- Layouts consist of a **header.php** and **footer.php** file located in **layouts/default**
- These files are automatically included by calling the functions `get_header()` and `get_footer()` on a template file.

#### Custom Layouts
- To create a new layout simply duplicate your **default** folder, rename it *(ex: legal)* then update the code. 
- Your custom header and footer can be called using the functions `get_header('legal')` and `get_footer('legal')`

## Templates <a name="templates"></a>
- Templates are created in the **templates** directory.
- **index.php** is your home page.
- Routes are automatic. For example: **templates/legal/copyright.php** can be accessed at **your-domain.com/legal/copyright**
- Templates with the name **404.php** can be placed within any route folder to provide a custom 404 page when a template file the system searches for doesn't exist.
- Templates with the name **\_template.php** are functionally identical to a **404.php** file. This is used more for the developers' benefit. It implies that non matching routes can be handled without a 404 being thrown. **This template takes priority over the 404.php file**
- When handling routes within the **_template.php**, there might be a case when the route really shouldn't be handled in that way and you desire to instead show the 404 page. You can make a call using the function `reject_template()` to return back to the template resolver to have it fetch the next likely template (probably the **404.php**).
	```php
		// Within _template.php (or any template really)
		if (!is_numeric(get_request_uri(-1)) {	// EX: Check that last part of request uri is a number
			reject_template();
			return;
		}
	```

## Assets <a name="assets"></a>
All css, fonts, images and JavaScript will go in the **public** folder, ideally in their respective sub-folder.

## Data <a name="data"></a>
Data can be stored in INI, CSV or JSON in the **data** folder. 

Example:

```php
	// Usage:
	// read_data(PATH, [OPTIONS])

	// Data file exists elsewhere on the machine
	$data = read_data('/usr/desktop/db.csv');

	// Data file exists in /data directory
	$data = read_data('db');	// When omitting the extension, it will still find it and correctly read it
```

### Options
INI: none

CSV: Defaults to:
```php
array(
    'headers' => false, // If true, will use the first row as column names
	'objects' => false  // If true, each row will be returned as an instance of stdClass
)
```

JSON: Defaults to:
    `true` // If true, returns data as associated array. Falsy will return as instances of stdClass

## Helper Functions <a name="functions"></a>
- `get_header([$name = 'default'])` will include the header layout file. The `$name` parameter will switch layouts
- `get_footer([$name = 'default'])` functions the same as `get_header()` but for the footer
- `get_partial($name, [$data = array()])` Will include the helper template within the partials folder. If $data is provided, it will be [extract](https://www.php.net/manual/en/function.extract.php)ed into the context of the partial
- `baseUrl([$url = '', $lang = false])` returns the url of the site and takes an optional $url and $lang parameter. Example: `baseUrl('wines/chardonnay)`. If $lang is provided, the url will be for that $lang
- `translateUrl([$url, $lang = false])` if $lang is provided, converts code_language url to that. Else, converts any other_language to code_language
- `publicUrl([$url = ''])` returns the absolute url to your public folder where all the assets lie. This takes as option path parameter. Example: `<img src="<?= publicUrl('images/logo.png') ?>">`
- `get_request_uri([$uriPart = false, $includeFollowing = false])` returns the request uri. If $urlPart is provided (numeric), then a url-component will be extracted at the specified location. $uriPart can also be negative to select from the end. If $includeFollowing is true, the parts after $uriPart will also be included
- `get_raw_request_uri([$uriPart = false, $includeFollowing = false])` same as `get_request_uri` but works off the url the user came in on and not what was translated to the code_language
- `get_config($key1, [$key2...])` will retrieve the value from the `custom` key in `config.json` by the $keys you pass in. Dots(.) are a delimiter as well
- `get_template()` returns the path to the template being rendered
- `reject_template()` meant to be called from a template when the developer decides that the current template isn't correct for the current route
- `meta($meta_name, $meta_value)` will queue a meta tag for rendering. Natively supports Twitter and Facebook
- `remove_meta($meta_info)` will unqueue a meta tag based on either the name or the exact parameters used to create it
- `bodyClass(string)` will add a string to the class of body`
- `htmlClass(string)` will add a string to the class of html`
- `title_part($part, [$prefix = false])` will construct the title tag for the page. `$prefix` true will prepend the `$part` instead of appending
- `title_separator($sep)` changes the title tag part separator text. Defaults to ' | ' automatically
- `script_register($name, $urlOrSrc, [$deps = array()])` teaches the site how to load a particular script when it or another script that needs it are enqueued
- `script_enqueue($name, [$header = true])` enqueues a script by name and any of it's dependencies. Defaults to the header
- `script_enqueue($name, $urlOrSrc, [$deps = array()], [$header = true])` functions as both a register and enqueue
- `stylesheet_register($name, $url, [$deps = array()])` teached the site how to load a particular stylesheet
- `stylesheet_enqueue($name, [$header = true])` enqueues a stylesheet by name and any of it's dependencies. Defaults to the header
- `stylesheet_enqueue($name, $url, [$deps = array()], [$header = true])` function as both a register and enqueue
- `header_resources()` will render the currently enqueued stylesheets, scripts, and meta tags for the header
- `footer_resources()` will render the currently enqueued stylesheets, scripts, and meta tags for the footer
- `read_data($fileName, [$cache = true, $options = null])` reads data files. Please read the [read_data Readme](./data/README.txt) for more info
- `dpm()` will dump out any data you pass to it in an easy to read format. To activate press CTRL+F12
- `is_multilingual()` return true/false if language features are enabled
- `current_lang()` return the current detected language of the site
- `current_lang_name()` return the current detect language's name of the site
- `is_lang($langPrefix)` return true/false if the current detected language matches parameter
- `get_languages()` returns an array of 'prefix' => 'name' pairs
- `getPageData($key, [$fileName, $default])` will fetch the requested `$key` value from a data file mapped to the current url. If the file doesn't exist and the site is in dev mode, the file will be spawned. If the `$key` doesn't exist and the site is in dev mode, the `$key` will be spawned with a `$default` value (default is the {`$key`})