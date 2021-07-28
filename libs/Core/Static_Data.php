<?php

namespace Core;

class Static_Data {
	private $file;
	private $ext;
	private $opts;
	private $readable = false;
	
	public function __construct($fileName, $options = null) {
		$fileName = str_replace('\\', '/', $fileName);
		$this->opts = $options;
		$this->updateFile($fileName);
		
		if (strpos($this->file, '/') !== false) {
			if (!is_file($this->file)) {
				return;
			}
		}
		else {
			if ($this->ext) {
				$this->file = WEB_ROOT . '/data/' . $this->file;
				if (!is_file($this->file)) {
					return;
				}
			}
			else {
				foreach (glob(WEB_ROOT . '/data/' . $this->file . '.*') as $file) {
					$this->updateFile($file);
					break;
				}
				
				if (!is_file($this->file)) {
					return;
				}
			}
		}
		
		$this->readable = true;
	}
	
	private function updateFile($path) {
		$this->file = $path;
		
		$this->ext = strrpos($this->file, '.');
		if ($this->ext !== false) {
			$this->ext = substr($this->file, $this->ext + 1);
			if (strpos($this->ext, '/') !== false) {
				$this->ext = false;
			}
		}
	}
	
	public function readFile() {
		if (!$this->readable) {
			return false;
		}
		
		$func = 'readFile_' . strtoupper($this->ext);
		if (!method_exists($this, $func)) {
			return false;
		}
		
		return $this->$func();
	}
	
	private function readFile_CSV() {
		if (!is_array($this->opts)) {
			$this->opts = array();
		}
		
		$this->opts = array_merge(array(
			'headers' => false,
			'objects' => false
		), $this->opts);
		
		$fp = fopen($this->file, 'r');
		$content = array();
		$headers = false;
		
		while ($arr = fgetcsv($fp)) {
			if ($this->opts['headers']) {
				if (!$headers) {
					$headers = $arr;
				}
				else {
					$arr2 = array();
					foreach ($arr as $ndx => $val) {
						if (isset($headers[$ndx])) {
							$arr2[$headers[$ndx]] = $val;
						}
						else {
							$arr2["col_{$ndx}"] = $val;
						}
					}
					
					$content[] = $this->opts['objects'] ? (object)$arr2 : $arr2;
				}
			}
			else {
				$content[] = $this->opts['objects'] ? (object)$arr : $arr;
			}
		}
		
		return $content;
	}
	
	private function readFile_JSON() {
		if (is_null($this->opts)) {
			$this->opts = true;
		}
		
		$content = file_get_contents($this->file);
		return json_decode($content, $this->opts);
	}
	
	private function readFile_INI() {
		$content = parse_ini_file($this->file, true);
		
		$skippedOne = true;
		$progress = true;
		$depFound = false;
		while ($skippedOne && $progress) {
			$skippedOne = false;
			$progress = false;
			$depFound = false;
			foreach ($content as $key => $vals) {
				if (strpos($key, ':') !== false) {
					$depFound = true;
					$parts = explode(':', $key);
					$parts = array_map('trim', $parts);
					
					if (!isset($content[$parts[1]])) {
						$skippedOne = true;
						continue;
					}
					
					$progress = true;
					$content[$parts[0]] = array_merge($content[$parts[1]], $vals);
					unset($content[$key]);
				}
			}
		}
		
		if ($depFound && !$progress) {
			return false;
		}
		
		return $content;
	}
}