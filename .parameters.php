<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arCurrentValues */

if (!\Bitrix\Main\Loader::includeModule("citrus.developer"))
{
	return;
}
$arComponentParameters = [
	"GROUPS" => [
	],
	"PARAMETERS" => [
		"VARIABLE_ALIASES" => [],
		"HOUSE_ID" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CITRUS_DEVELOPER_CHESS_HOUSE_ID"),
			"TYPE" => "INT",
			"DEFAULT" => "",
		],
		"PLAN_ID" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CITRUS_DEVELOPER_CHESS_PLAN_ID"),
			"TYPE" => "INT",
			"DEFAULT" => "",
		],
		"FILTER_NAME" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CITRUS_DEVELOPER_CHESS_FILTER_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		],
		'ADMIN_EDIT' => [
			"NAME" => Loc::getMessage("CITRUS_DEVELOPER_CHESS_ADMIN_EDIT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		],
		"PROPERTIES" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CITRUS_DEVELOPER_CHESS_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => [],
			"DEFAULT" => [
				'common_area',
				'kitchen_area',
				'rooms',
				'cost',
			],
		],
	],
];

