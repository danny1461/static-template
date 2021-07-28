<?php

namespace Core;

abstract class ViewModel {
	public static function from(array $data) {
		$result = null;

		foreach ($data as $key => $val) {
			if (is_numeric($key)) {
				return new static(...$data);
			}
			else {
				if (is_null($result)) {
					$result = new static();
				}

				if (property_exists(static::class, $key)) {
					$result->$key = $val;
				}
			}
		}

		return $result;
	}
}