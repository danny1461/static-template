<?php

namespace Core;

class FormFieldSpec implements IFormFieldSpec {
	private $required = false;
	private $transformer = null;
	private $isArray = false;
	private $validators = [];

	public function setRequired($message = ''): IFormFieldSpec {
		$this->required = function($fieldName) use ($message) {
			return $message ?: "{$fieldName} is required";
		};

		return $this;
	}

	public function setConditionalRequired(callable $conditional, $message = ''): IFormFieldSpec {
		$this->required = function($fieldName, $form) use ($conditional, $message) {
			$result = $conditional($fieldName, $form);

			if ($result === true) {
				return $message ?: "{$fieldName} is required";
			}

			return $result;
		};

		return $this;
	}

	public function setStringType(): IFormFieldSpec {
		$this->transformer = 'strval';
		return $this;
	}

	public function setIntType(): IFormFieldSpec {
		$this->transformer = 'intval';
		return $this;
	}

	public function setBoolType(): IFormFieldSpec {
		$this->transformer = 'boolval';
		return $this;
	}

	public function setIsArray(): IFormFieldSpec {
		$this->isArray = true;
		return $this;
	}

	public function setValueTranformer($transformer): IFormFieldSpec {
		$this->transformer = $transformer;
		return $this;
	}

	function addRegexValidator($regex, $message = ''): IFormFieldSpec {
		$this->validators[] = function($value, $fieldName) use ($message, $regex) {
			if (!preg_match($regex, $value)) {
				return $message ?: "{$fieldName} should match regex";
			}

			return true;
		};

		return $this;
	}

	function addMinMaxValueValidator($minValue = null, $maxValue = null, $message = ''): IFormFieldSpec {
		$this->validators[] = function($value, $fieldName) use ($message, $minValue, $maxValue) {
			if (!$message) {
				$message = "{$fieldName} should be ";
				if (!is_null($minValue)) {
					$message .= 'greater than ' . $minValue . ' and ';
				}
	
				if (!is_null($maxValue)) {
					$message .= 'less than ' . $maxValue;
				}
			}

			if (!is_null($minValue) && $value < $minValue) {
				return $message;
			}

			if (!is_null($maxValue) && $value > $maxValue) {
				return $message;
			}

			return true;
		};

		return $this;
	}

	function addLengthValidator($minLength = null, $maxLength = null, $message = ''): IFormFieldSpec {
		$this->validators[] = function($value, $fieldName) use ($message, $minLength, $maxLength) {
			if (!$message) {
				$message = "{$fieldName} should have a length ";
				if (!is_null($minLength)) {
					$message .= 'greater than ' . $minLength . ' and ';
				}
	
				if (!is_null($maxLength)) {
					$message .= 'less than ' . $maxLength;
				}
			}

			if (!is_null($minLength) && strlen($value) < $minLength) {
				return $message;
			}

			if (!is_null($maxLength) && strlen($value) > $maxLength) {
				return $message;
			}

			return true;
		};

		return $this;
	}

	function addChoiceValidator($choices, $message = ''): IFormFieldSpec {
		$this->validators[] = function($value, $fieldName) use ($message, $choices) {
			if (!in_array($value, $choices)) {
				return $message ?: "{$fieldName} should be one of " . implode(', ', $choices);
			}

			return true;
		};

		return $this;
	}

	function addCustomValidator($callback): IFormFieldSpec {
		$this->validators[] = $callback;

		return $this;
	}

	function addEmailValidator($message = ''): IFormFieldSpec {
		$this->validators[] = function($value, $fieldName) use ($message) {
			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				return $message ?: "{$fieldName} should be a valid email address";
			}

			return true;
		};

		return $this;
	}

	/**
	 * Returns whether this field is required
	 * 
	 * @param FormValidator $form 
	 * @return bool 
	 */
	public function getIsRequired(FormValidator $form, $fieldName) {
		$value = $this->required;

		if (is_callable($value)) {
			$value = $value($fieldName, $form);
		}

		return $value;
	}

	/**
	 * Transforms value type
	 * 
	 * @param mixed $value 
	 * @return mixed 
	 */
	public function transformValue($value) {
		if ($this->transformer) {
			$fn = $this->transformer;
			$value = $fn($value);
		}

		return $value;
	}

	/**
	 * Returns array of error messages
	 * 
	 * @param mixed $value 
	 * @param FormValidator $form 
	 * @param bool $allErrors 
	 * @return array 
	 */
	public function isValid($value, FormValidator $form, $fieldName, $allErrors) {
		$errors = [];

		if ($this->isArray && !is_array($value)) {
			$errors[] = 'array expected';

			if (!$allErrors) {
				return $errors;
			}
		}

		if (!is_array($value)) {
			$value = [$value];
		}

		foreach ($value as $val) {
			foreach ($this->validators as $fn) {
				$result = $fn($val, $fieldName, $form);
	
				if (is_string($result)) {
					$errors[] = $result;

					if (!$allErrors) {
						return $errors;
					}
				}
			}
		}

		return $errors;
	}
}