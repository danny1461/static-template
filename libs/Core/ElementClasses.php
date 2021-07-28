<?php

namespace Core;

class ElementClasses {
	public $classes = array();

	public function add($classes) {
		if (is_string($classes)) {
			$classes = explode(' ', $classes);
			$classes = array_map('trim', $classes);
		}

		$this->classes = array_merge($this->classes, array_filter($classes));
	}

	public function remove($classes) {
		if (is_string($classes)) {
			$classes = explode(' ', $classes);
			$classes = array_map('trim', $classes);
			$classes = array_filter($classes);
		}

		$this->classes = array_diff($this->classes, $classes);
	}

	public function getClassString() {
		return implode(' ', $this->classes);
	}
}