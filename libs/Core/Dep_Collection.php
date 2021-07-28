<?php

namespace Core;

use Exception;

class Dep_Collection {
	public $resources = array();
	
	public function addResource($name, $payload, $deps = array()) {
		$this->resources[$name] = array(
			'name' => $name,
			'deps' => $deps,
			'payload' => $payload
		);
	}
	
	private function orderDeps(&$list, $res, $circular = array()) {
		if (in_array($res['name'], $circular)) {
			throw new Exception("Circular dependency! One of {$res['name']}'s dependencies require {$res['name']}.");
		}
		
		$circular[] = $res['name'];
		
		foreach ($res['deps'] as $dep) {
			if (!isset($this->resources[$dep])) {
				continue;
			}
			
			if (!in_array($dep, $list)) {
				$this->orderDeps($list, $this->resources[$dep], $circular);
			}
		}
		
		$list[] = $res['name'];
	}
	
	public function getOrderedList($needed = false) {
		if ($needed === false) {
			$needed = array_keys($this->resources);
		}
		
		$list = array();
		foreach ($needed as $dep) {
			if (!isset($this->resources[$dep])) {
				continue;
			}
			
			if (!in_array($dep, $list)) {
				$this->orderDeps($list, $this->resources[$dep]);
			}
		}
		
		$result = array();
		foreach ($list as $name) {
			$result[] = $this->resources[$name]['payload'];
		}
		
		return $result;
	}
}