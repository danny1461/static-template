<?php

namespace Core;

interface IFormFieldSpec {
	/**
	 * Sets the field as required
	 * 
	 * @param string $message
	 * @return static
	 */
	function setRequired($message = ''): IFormFieldSpec;
	/**
	 * Uses the result of the given callback to determine if this field is required
	 * 
	 * @param string $message
	 * @param callable<FormValidator> $conditional 
	 * @return IFormFieldSpec 
	 */
	function setConditionalRequired(callable $conditional, $message = ''): IFormFieldSpec;
	/**
	 * Casts given values for this field to string
	 * 
	 * @return IFormFieldSpec 
	 */
	function setStringType(): IFormFieldSpec;
	/**
	 * Casts given values for this field to int
	 * 
	 * @return IFormFieldSpec 
	 */
	function setIntType(): IFormFieldSpec;
	/**
	 * Casts given values for this field to bool
	 * 
	 * @return IFormFieldSpec 
	 */
	function setBoolType(): IFormFieldSpec;
	/**
	 * Sets this field as being an array of values
	 * 
	 * @return IFormFieldSpec 
	 */
	function setIsArray(): IFormFieldSpec;
	/**
	 * Provides a tranformation function for the field value
	 * 
	 * @param callable $transformer 
	 * @return IFormFieldSpec 
	 */
	function setValueTranformer($transformer): IFormFieldSpec;
	/**
	 * Ensures the value matches the given regex
	 * 
	 * @param string $regex 
	 * @param string $message 
	 * @return IFormFieldSpec 
	 */
	function addRegexValidator($regex, $message = ''): IFormFieldSpec;
	/**
	 * Ensures the value is within a certain range
	 * 
	 * @param mixed|null $minValue 
	 * @param mixed|null $maxValue 
	 * @param string $message 
	 * @return IFormFieldSpec 
	 */
	function addMinMaxValueValidator($minValue = null, $maxValue = null, $message = ''): IFormFieldSpec;
	/**
	 * Ensures the value is within a certain length
	 * 
	 * @param mixed|null $minLength 
	 * @param mixed|null $maxLength 
	 * @param string $message 
	 * @return IFormFieldSpec 
	 */
	function addLengthValidator($minLength = null, $maxLength = null, $message = ''): IFormFieldSpec;
	/**
	 * Ensures the value is one of a given list
	 * 
	 * @param array $choices 
	 * @param string $message 
	 * @return IFormFieldSpec 
	 */
	function addChoiceValidator($choices, $message = ''): IFormFieldSpec;
	/**
	 * Runs custom validator against value
	 * 
	 * @param callable $callback should return true for success or string for error message
	 * @return IFormFieldSpec 
	 */
	function addCustomValidator($callback): IFormFieldSpec;
	/**
	 * Ensures the value is a valid email
	 * 
	 * @param string $message 
	 * @return IFormFieldSpec 
	 */
	function addEmailValidator($message = ''): IFormFieldSpec;
}