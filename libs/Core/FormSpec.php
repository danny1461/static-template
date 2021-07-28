<?php

namespace Core;

// TODO: Add FormBuilder that accepts a FormSpec
class FormSpec {
	private $fields = [];

	/**
	 * Adds a field to the form
	 * 
	 * @param string $name 
	 * @return IFormFieldSpec 
	 */
	public function addField($name) {
		$this->fields[$name] = new FormFieldSpec();
		return $this->fields[$name];
	}

	/**
	 * 
	 * @return IFormValidator
	 */
	public function getValidator() {
		return new FormValidator($this->fields);
	}

	public function getFields() {
		return $this->fields;
	}
}