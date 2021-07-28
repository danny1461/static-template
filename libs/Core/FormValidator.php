<?php

namespace Core;

use Exception;

class FormValidator implements IFormValidator {
	/** @var FormFieldSpec[] */
	private $fields;

	/** @var array */
	private $data = null;

	public function __construct($fields) {
		$this->fields = $fields;
	}

	public function validate(array $data, bool $allErrors = false): IFormValidationResult {
		if (!is_null($this->data)) {
			throw new Exception('validate already running');
		}

		$this->data = $data;
		$result = new FormValidationResult();

		// Transform values
		foreach ($this->fields as $fieldName => $field) {
			try {
				$value = $this->getFieldValue($fieldName);
				$this->data[$fieldName] = empty($value)
					? null
					: $field->transformValue($this->getFieldValue($fieldName));
			}
			catch (Exception $e) {
				$result->addFieldError($fieldName, $e->getMessage());
			}
		}

		// Validate
		foreach ($this->fields as $fieldName => $field) {
			try {
				$value = $this->getFieldValue($fieldName);
				
				// Is empty?
				if (empty($value)) {
					// Check is required
					$requiredMessage = $field->getIsRequired($this, $fieldName);
					if ($requiredMessage) {
						$result->addFieldError($fieldName, $requiredMessage);

						if (!$allErrors) {
							continue;
						}
					}
					else {
						continue;
					}
				}

				// Run validators
				$errors = $field->isValid($value, $this, $fieldName, $allErrors);
				foreach ($errors as $message) {
					$result->addFieldError($fieldName, $message);
				}
			}
			catch (Exception $e) {
				$result->addFieldError($fieldName, $e->getMessage());
			}
		}

		$result->setData($this->data);
		$this->data = null;

		return $result;
	}

	/**
	 * Fetches a value for a field by name
	 * 
	 * @param string $name 
	 * @return mixed 
	 */
	public function getFieldValue($name) {
		return array_key_exists($name, $this->data)
			? $this->data[$name]
			: null;
	}
}