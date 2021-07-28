<?php

namespace Core;

use Countable;

class FormValidationResult implements IFormValidationResult, Countable {
	private $data = [];
	private $errors = [];
	private $errorNum = 0;

	/**
	 * Sets the data for this result
	 * 
	 * @param array $data 
	 * @return void 
	 */
	public function setData(array $data) {
		$this->data = $data;
	}

	public function getData(): array {
		return $this->data;
	}

	public function getFieldData($name) {
		return array_key_exists($name, $this->data)
			? $this->data[$name]
			: null;
	}

	public function hasErrors(): bool {
		return $this->errorNum > 0;
	}

	public function errorCount(): int {
		return $this->errorNum;
	}

	public function count() {
		return $this->errorNum;
	}

	public function getFieldErrors($name) {
		return array_key_exists($name, $this->errors)
			? $this->errors[$name]
			: null;
	}

	public function addFieldError(string $name, string $msg) {
		if (!isset($this->errors[$name])) {
			$this->errors[$name] = [];
		}

		$this->errors[$name][] = $msg;
		$this->errorNum++;
	}

	public function getAllErrors(): array {
		return $this->errors;
	}
}