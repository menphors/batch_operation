<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Carbon\Carbon;

class Helper
{
	
	
	public static function xmlToArray($contents, $getAttributes = true, $tagPriority = true, $encoding = 'utf-8')
	{
		$contents = trim($contents);
		if (empty($contents)) {
			return [];
		}
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $encoding);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		if (xml_parse_into_struct($parser, $contents, $xmlValues) === 0) {
			xml_parser_free($parser);
			return [];
		}
		xml_parser_free($parser);
		if (empty($xmlValues)) {
			return [];
		}
		unset($contents, $parser);
		$xmlArray = [];
		$current = &$xmlArray;
		$repeatedTagIndex = [];
		foreach ($xmlValues as $num => $xmlTag) {
			$result = null;
			$attributesData = null;
			if (isset($xmlTag['value'])) {
				if ($tagPriority) {
					$result = $xmlTag['value'];
				} else {
					$result['value'] = $xmlTag['value'];
				}
			}
			if (isset($xmlTag['attributes']) and $getAttributes) {
				foreach ($xmlTag['attributes'] as $attr => $val) {
					if ($tagPriority) {
						$attributesData[$attr] = $val;
					} else {
						$result['@attributes'][$attr] = $val;
					}
				}
			}
			if ($xmlTag['type'] == 'open') {
				$parent[$xmlTag['level'] - 1] = &$current;
				if (!is_array($current) or (!in_array($xmlTag['tag'], array_keys($current)))) {
					$current[$xmlTag['tag']] = $result;
					unset($result);
					if ($attributesData) {
						$current['@'.$xmlTag['tag']] = $attributesData;
					}
					$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
					$current = &$current[$xmlTag['tag']];
				} else {
					if (isset($current[$xmlTag['tag']]['0'])) {
						$current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $result;
						unset($result);
						if ($attributesData) {
							if (isset($repeatedTagIndex['@'.$xmlTag['tag'].'_'.$xmlTag['level']])) {
								$current[$xmlTag['tag']][$repeatedTagIndex['@'.$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
							}
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					} else {
						$current[$xmlTag['tag']] = [$current[$xmlTag['tag']], $result];
						unset($result);
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 2;
						if (isset($current['@'.$xmlTag['tag']])) {
							$current[$xmlTag['tag']]['@0'] = $current['@'.$xmlTag['tag']];
							unset($current['@'.$xmlTag['tag']]);
						}
						if ($attributesData) {
							$current[$xmlTag['tag']]['@1'] = $attributesData;
						}
					}
					$lastItemIndex = $repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] - 1;
					$current = &$current[$xmlTag['tag']][$lastItemIndex];
				}
			} elseif ($xmlTag['type'] == 'complete') {
				if (!isset($current[$xmlTag['tag']]) and empty($current['@'.$xmlTag['tag']])) {
					$current[$xmlTag['tag']] = $result;
					unset($result);
					$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
					if ($tagPriority and $attributesData) {
						$current['@'.$xmlTag['tag']] = $attributesData;
					}
				} else {
					if (isset($current[$xmlTag['tag']]['0']) and is_array($current[$xmlTag['tag']])) {
						$current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $result;
						unset($result);
						if ($tagPriority and $getAttributes and $attributesData) {
							$current[$xmlTag['tag']]['@'.$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					} else {
						$current[$xmlTag['tag']] = [
								$current[$xmlTag['tag']],
								$result,
						];
						unset($result);
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] = 1;
						if ($tagPriority and $getAttributes) {
							if (isset($current['@'.$xmlTag['tag']])) {
								$current[$xmlTag['tag']]['@0'] = $current['@'.$xmlTag['tag']];
								unset($current['@'.$xmlTag['tag']]);
							}
							if ($attributesData) {
								$current[$xmlTag['tag']]['@'.$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']]] = $attributesData;
							}
						}
						$repeatedTagIndex[$xmlTag['tag'].'_'.$xmlTag['level']] += 1;
					}
				}
			} elseif ($xmlTag['type'] == 'close') {
				$current = &$parent[$xmlTag['level'] - 1];
			}
			unset($xmlValues[$num]);
		}
		return $xmlArray;
	}
	
	/**
	 * 
	 * @param string $startDate
	 * @param string $endDate
	 * @return number
	 */
	public static function diffDays($startDate, $endDate = null){
		$startDate = Carbon::parse($startDate);
		if($endDate == null) $endDate = Carbon::now();
		else $endDate = Carbon::parse($endDate);	
		return $endDate->diffInDays($startDate);
	}
	
	public static function KBToGB($kb){
		return round(($kb/1024)/1024);
	}
	
	
	public static function NGBSSAmountToUSD($amount){
		return $amount/100000000;
	}
	
	public static function getLangColumn($columnName, $lang){
		if(!in_array($lang, ['en', 'kh', 'zh'])) $lang = 'en';
		return $columnName.'_'.$lang;
	}
	
	public static function getDisplayAmount($measurementUnit, $amount){
		$result = [
				'unit' => self::getMeasurementUnit($measurementUnit),
				'amount' => $amount
		];
		if($measurementUnit == '1003'){
			$minutes = $amount/60;
			if($minutes < 60){
				$result = [
						'unit' => $minutes > 1 ? __('other.minutes'):__('other.minute'),
						'amount' => floor($minutes)
				];
			}else{
				$hours = $minutes / 60;
				$result = [
						'unit' => $hours > 1 ? __('other.hours'):__('other.hour'),
						'amount' => floor($hours)
				];
			}
		}else if($measurementUnit == '1004'){
			if($amount > 60){
				$hours = $amount / 60;
				$result = [
						'unit' => $hours > 1 ? __('other.hours'):__('other.hour'),
						'amount' => floor($hours)
				];
			}
		}else if($measurementUnit == '1108'){
			if($amount >= 1024){
				$result = [
						'unit' => __('other.gb'),
						'amount' => round(($amount/1024),1)
				];
			}
		}
		
		return $result;
		
	}
	
	public static function getMeasurementUnit($id){
		$units = [
				"1003" => __('other.second'),
				"1004" => __('other.minutes'),
				"1006" => __('other.time'),
				"1101" => __('other.item'),
				"1106" => __('other.byte'),
				"1107" => __('other.kb'),
				"1108" => __('other.mb'),
				"1109" => __('other.gb'),
				"1120" => __('other.point'),
				"1121" => __('other.page'),
				"1122" => __('other.entry')
		];
		
		if(array_key_exists($id, $units)){
			return $units[$id];
		}else{
			return null;
		}
	}
}