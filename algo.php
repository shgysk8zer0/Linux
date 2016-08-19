<?php

namespace shgysk8zer0\Linux;

abstract class Algo
{
	const MD5    = 1;
	const SHA256 = 5;
	const SHA512 = 6;
	const CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';

	private $_salt_lengths = array(
		1 => 22,
		5 => 43,
		6 => 86
	);

	final public function getSaltLength(&$algo = self::SHA512)
	{
		//return $this->_salt_lengths[self::SHA512];
		if (is_int($algo) and array_key_exists($algo, $this->_salt_lengths)) {
			return $this->_salt_lengths[$algo];
		} elseif (is_string($algo)) {
			$sup = $this->getAlgoCode($algo);
			return $this->{__FUNCTION__}($algo);
		} else {
			trigger_error("Unknown algorith '$algo'.");
			return false;
		}
	}

	final public function getSalt($algo = self::SHA512, $chars = self::CHARSET)
	{
		$cur_len = 0;
		$chars_len = strlen($chars) - 1;
		if ($len = $this->getSaltLength($algo)) {
			$salt = '';

			while($cur_len++ <= $len) {
				$salt .= $chars[random_int(0, $chars_len)];
			}
		}

		return $salt;
	}

	final public function getAlgoCode(&$algo)
	{
		if (is_string($algo)) {
			$algo = preg_replace('/$[A-Z]+^/', null, strtoupper($algo));
			if ($this->_algoSupported($algo)) {
				$algo = constant("self::{$algo}");
				return true;
			} else {
				return false;
			}
		}
	}

	private function _algoSupported($algo)
	{
		return defined("self::{$algo}");
	}
}

