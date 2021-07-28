<?php

namespace Core;

interface IFormValidator {
	/**
	 * Runs form validators against given data
	 * 
	 * @param array $data 
	 * @param bool $allErrors
	 * @return IFormValidationResult 
	 */
	function validate(array $data, bool $allErrors = false): IFormValidationResult;
}