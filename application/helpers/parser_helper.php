<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('extract_ids')) {
	function extract_ids($string, $prefix='_PREFIX_', $postfix='_POSTFIX_') {
		$array = [];
		$start = 0;
		$end = 0;

		while(strpos($string,$prefix,$end))
		{
			$start = strpos($string,$prefix,$start)+strlen($prefix);
			$end = strpos($string,$postfix,$start);

			$array[] = substr($string,$start,$end-$start);
			$end = $end + strlen($postfix);
		}

		return $array;
	}
}
