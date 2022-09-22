<?php

namespace Citrus\Developer\Components\Chess;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Loader;
use Citrus\Developer\Admin\Iblock\ChessEditor;
use Citrus\Developer\Chess;
use Citrus\Developer\Components\Base;
use Citrus\Developer\Components\DefaultParamsTrait;
use Citrus\Developer\Components\RequiredModulesTrait;
use Citrus\Developer\Iblock;
use Bitrix\Main\Localization\Loc;
use Citrus\Developer\RouterFactory;
use Maximaster\Tools\Helpers\IblockStructure;
use Arrilot\BitrixHermitage\Action;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!Loader::includeModule('citrus.developer'))
{
	return false;
}

Loc::loadMessages(__FILE__);

/** @noinspection AutoloadingIssuesInspection */
class ChessComponent extends Base
{
	use DefaultParamsTrait, RequiredModulesTrait;

	protected $requiredModules = ['iblock'];

	protected $requestFields = ['edit' => 'chess-edit', 'delete' => 'chess-delete'];
	protected $requestData;

	/**
	 * @var array
	 */
	protected $filter;

	/** @var string[] */
	protected $properties = [];
	/** @var int */
	protected $offersIblockId;
	protected $houseId;
	protected $router;
	protected $planId;
	protected $siteId;
	/** @var Chess */
	protected $chess;

	public function __construct(\CBitrixComponent $component = null)
	{
		parent::__construct($component);

		$this->router = RouterFactory::residentalComplex();
	}

	public function onPrepareComponentParams($arParams)
	{
		$arParams = parent::onPrepareComponentParams($arParams);

		$this->houseId = (int)$arParams['HOUSE_ID'];
		if ($this->houseId <= 0)
		{
			throw new ArgumentException(Loc::getMessage("CITRUS_DEVELOPER_CHESS_WRONG_HOUSE_ID_PARAM"));
		}

		$this->chess = new Chess($this->houseId);
		$this->siteId = $this->chess->getSiteId();
		$this->offersIblockId = Iblock::getId(Iblock::APARTMENTS, $this->siteId);

		$filter = $GLOBALS[$arParams['FILTER_NAME']];
		if (!is_array($filter))
		{
			$filter = [];
		}

		unset($filter['IBLOCK_ID']);
		$filter['=IBLOCK_ID'] = $this->offersIblockId;

		if (($planId = (int)$arParams['PLAN_ID']) > 0)
		{
			$this->planId = $planId;
		}

		$this->filter = $filter;

		if (is_array($arParams['PROPERTIES']))
		{
			$this->properties = $arParams['PROPERTIES'];
		}

		return $arParams;
	}

	/**
	 * @return bool
	 */
	public function isEditMode()
	{
		return $this->arParams['ADMIN_EDIT'];
	}

	/**
	 * Преобразует поля фильтра вида PROPERTY_123 в PROPERTY_code
	 *
	 * @return array
	 */
	protected function convertFilter()
	{
		$fields = array_change_key_case($this->chess->flatQuery()->getEntity()->getFields(), CASE_UPPER);

		$properties = IblockStructure::properties($this->offersIblockId);
		$propertyCodes = [];
		foreach ($properties as $code => $property)
		{
			$propertyCodes[$property['ID']] = $property['CODE'];
		}

		$result = [];
		foreach ($this->filter as $field => $value)
		{
			if (preg_match('#^(.*?)PROPERTY_(\d+)$#', $field, $matches) && isset($propertyCodes[$matches[2]]))
			{
				$fieldCode = 'PROPERTY_' . ToUpper($propertyCodes[$matches[2]]) . '_VALUE';
				$fieldSuffix = $fields[$fieldCode] instanceof ReferenceField ? '.ID' : '';

				$result[$matches[1] . $fieldCode . $fieldSuffix] = $value;
			}
			else
			{
				$result[$field] = $value;
			}
		}

		return $result;
	}

	protected function flatQuery()
	{
		$query = $this->chess->flatQuery();
		$entity = $query->getEntity();

		$query
			->setSelect([
				'ID',
				'CODE',
				'ACTIVE',
				'NAME',
				'IBLOCK_SECTION_ID',
				'SORT',

				'section' => 'IBLOCK_SECTION.NAME',
				'rooms' =>'PROPERTY_rooms_VALUE.VALUE',
				'plan' => 'PROPERTY_layout_VALUE.ID',
				'number' => 'PROPERTY_apartment_VALUE',
				'floor' => 'PROPERTY_floor_VALUE',
				'floors' => 'PROPERTY_section_VALUE.PROPERTY_floors_VALUE'
			])
			->setOrder(['section' => 'asc', 'floor' => 'desc', 'SORT' => 'asc', 'rooms' => 'asc']);

		foreach ($this->properties as $col)
		{
			$suffix = '';
			if (!$entity->hasField('PROPERTY_' . $col))
			{
				continue;
			}

			$field = $entity->getField('PROPERTY_' . $col . '_VALUE');

			if (
				$field instanceof ReferenceField
				&& $field->getRefEntityName() == '\Bitrix\Iblock\PropertyEnumeration'
			)
			{
				// свойство типа Список
				$suffix = '.VALUE';
			}
			elseif (
				$field instanceof ReferenceField
				&& $field->getRefEntityName() == '\Maximaster\Tools\Orm\Iblock\Element'
			)
			{
				// привязка к элементам ИБ
				$suffix = '.NAME';
			}

			$query->addSelect("PROPERTY_{$col}_VALUE" . $suffix, $col);
		}

		return $query;
	}

	protected function fetchSections()
	{
		$query = $this->chess->sectionQuery()
			->addFilter('ACTIVE', 'Y')
			->setSelect([
				'ID',
				'IBLOCK_ID',
				'NAME',
				'floors' => 'PROPERTY_floors_VALUE',
			])
			->setOrder(['SORT', 'NAME']);

		// @todo Кешировать получение секций
		$iterator = $query->exec();
		$result = [];
		while ($section = $iterator->fetch())
		{
			$result[$section['ID']] = array_diff([
				'id' => (int)$section['ID'],
				'name' => $section['NAME'],
				'floorsCount' => round($section['floors']),
				'editId' => Action::editIBlockElement($this, $section),
				'flats' => [],
			], [null]);
		}
		return $result;
	}

	protected function renderException($e)
	{
		if ($this->arParams['ADMIN_EDIT_CHECK'] == 'Y'
			|| $this->arParams['ADMIN_EDIT_SAVE'] == 'Y'
		)
		{
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}

		parent::renderException($e);
	}

	protected function edit()
	{
		$chessEditor = new ChessEditor($this->houseId);
		if ($this->arParams['ADMIN_EDIT_CHECK'] == 'Y')
		{
			$chessEditor->validateRequest();
		}
		else
		{
			$chessEditor->edit();
		}
	}

	/**
	 * @return null
	 */
	protected function execute()
	{
		if ($this->arParams['ADMIN_EDIT_CHECK'] == 'Y' ||
			$this->arParams['ADMIN_EDIT_SAVE'] == 'Y'
		)
		{
			$this->edit();
			return;
		}

		$this->arResult['SELECTED_PLANS'] = $this->planId ? [$this->planId] : [];

		$this->arResult['SECTIONS'] = $this->fetchData($this->convertFilter());
		if ($this->isEditMode())
		{
			$this->arResult['EDIT_PROPERTIES'] = [
				'layout' => [
					'name' => Loc::getMessage("CITRUS_DEVELOPER_CHESS_FLAT_LAYOUT"),
					'code' => 'layout',
					'type' => 'list',
					'items' => $this->getLayoutItems(),
				],
				'active' => [
					'name' => Loc::getMessage("CITRUS_DEVELOPER_CHESS_FLAT_ACTIVE"),
					'type' => 'bool',
					'code' => 'active',
				],
			];
		}

		$this->includeComponentTemplate();

		return !empty($this->arResult['SECTIONS']);
	}

	protected function getImagePath($imageId)
	{
		if (!$imageId)
		{
			return null;
		}

		return \CFile::GetPath($imageId);
	}

	private function getLayoutItems()
	{
		$query = $this->chess->layoutQuery()
			->setOrder(['SORT' => 'asc'])
			->setSelect([
				'ID',
				'NAME',
				'PREVIEW_PICTURE',
				'DETAIL_PICTURE',
				'rooms' => 'PROPERTY_rooms_VALUE.VALUE',
			])
		;

		$result = [];
		foreach ($query->exec()->fetchAll() as $layout)
		{
			if ($imageId = $layout['PREVIEW_PICTURE'] ?: $layout['DETAIL_PICTURE'])
			{
				unset($layout['PREVIEW_PICTURE'], $layout['DETAIL_PICTURE']);
				$layout['image'] = $this->getImagePath($imageId);
			}

			array_push($result, array_change_key_case($layout, CASE_LOWER));
		}

		return $result;
	}

	protected function fetchData(array $additionalFilter = [])
	{
		$sections = $this->fetchSections();

		$query = $this->flatQuery();

		array_walk($additionalFilter, function ($value, $key) use ($query) {
			$query->addFilter($key, $value);
		});

		// @todo Кешировать получение квартир
		$iterator = $query->exec();

		$helper = new Iblock\OrmPropertyFormatter([], $this->offersIblockId);

		$makeProperties = function () use ($helper)
		{
			$result = [];
			foreach ($this->properties as $prop)
			{
				if ($helper->getValue($prop) !== null)
				{
					$result[$helper->getName($prop)] = $helper->getFormatValue($prop);
				}
			}
			return $result;
		};

		while ($flat = $iterator->fetch())
		{
			$helper->setItem($flat);
			$flatId = (int)$flat['ID'];
			$sectionId = (int)$flat['IBLOCK_SECTION_ID'];

			$complex = Iblock\ForeignIblock::getParentByChildSection($sectionId, Iblock::COMPLEXES);
			$house = Iblock\ForeignIblock::getParentByChildSection($sectionId, Iblock::HOUSES);
			$section = Iblock\ForeignIblock::getParentByChildSection($sectionId, Iblock::SECTIONS);

			$detailPageUrl = $this->router->getUrl('flat_detail', [
				'ELEMENT_ID' => $complex['ID'],
				'ELEMENT_CODE' => $complex['CODE'],
				'HOUSE_ID' => $house['ID'],
				'HOUSE_CODE' => $house['CODE'],
				'FLAT_ID' => $flatId,
				'FLAT_CODE' => $flat['CODE'],
			]);

			$floor = (int)$flat['floor'];
			if (!$floor)
			{
				trigger_error('Incorrect flat floor', E_USER_WARNING);
				continue;
			}

			if (!array_key_exists($section['ID'], $sections))
			{
				trigger_error('Incorrect flat section', E_USER_WARNING);
				continue;
			}

			$sections[$section['ID']]['flats'][] = array_diff([
				'id' => $flatId,
				'name' => $flat['NAME'],
				'floor' => $floor,
				'rooms' => $flat['rooms'],
				'active' => $flat['ACTIVE'] === 'Y',
				'plan' => $flat['plan'] ? (int)$flat['plan'] : null,
				'number' => $flat['apartment'],
				'link' => $detailPageUrl,
				'properties' => $makeProperties(),
				'editId' => Action::editIBlockElement($this, $flatId),
				'sort' => (int) $flat['SORT']
			], [null]);
		}

		return array_values($sections);
	}
}
