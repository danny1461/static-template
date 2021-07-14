INI, CSV, and JSON files can be placed in this directory for easy parsing.

Example:
<?php
	// Usage:
	// read_data(PATH, [OPTIONS])

	// Data file exists elsewhere on the machine
	$data = read_data('/usr/desktop/db.csv');

	// Data file exists in /data directory
	$data = read_data('db');	// When omitting the extension, it will still find it and correctly read it

Options:
	INI: None

	CSV:
		Defaults to:
			array(
				'headers' => false, // If true, will use the first row as column names
				'objects' => false  // If true, each row will be returned as an instance of stdClass
			)

	JSON:
		Defaults to:
			true // If true, returns data as associated array. Falsy will return as instances of stdClass