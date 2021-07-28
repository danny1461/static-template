<?php

namespace Core;

interface IFormValidationResult {
	/**
	 * Returns the transformed data from the FormSpec
	 * 
	 * @return array 
	 */
	function getData(): array;
	/**
	 *  Returns the transformed data for the given field
	 * 
	 * @param mixed $name 
	 * @return mixed 
	 */
	function getFieldData($name);
	/**
	 * Returns whether there are any errors set
	 * 
	 * @return bool 
	 */
	function hasErrors(): bool;
	/**
	 * Returns all the errors set for a given field
	 * 
	 * @param string $name 
	 * @return mixed 
	 */
	function getFieldErrors($name);
	/**
	 * Returns the total number of errors
	 * 
	 * @return int 
	 */
	function errorCount(): int;
	/**
	 * Returns all the errors set
	 * 
	 * @return array 
	 */
	function getAllErrors(): array;
}