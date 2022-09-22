<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var \Citrus\Developer\Components\Chess\ChessComponent $component */

use Bitrix\Main\Localization\Loc;
?>

<div class="svg-icons">
	<?#close?>
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 357 357" style="enable-background:new 0 0 357 357;" xml:space="preserve">
		<g id="icon-close">
			<polygon points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3 214.2,178.5"/>
		</g>
	</svg>

	<?#copy?>
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     width="30px" height="30px" viewBox="0 0 511.627 511.627" style="enable-background:new 0 0 511.627 511.627;"
	     xml:space="preserve">
		<g id="icon-copy">
			<path d="M503.633,117.628c-5.332-5.327-11.8-7.993-19.41-7.993H365.446c-11.417,0-23.603,3.806-36.542,11.42V27.412
				c0-7.616-2.662-14.092-7.994-19.417C315.578,2.666,309.11,0,301.492,0H182.725c-7.614,0-15.99,1.903-25.125,5.708
				c-9.136,3.806-16.368,8.376-21.7,13.706L19.414,135.901c-5.33,5.329-9.9,12.563-13.706,21.698C1.903,166.738,0,175.108,0,182.725
				v191.858c0,7.618,2.663,14.093,7.992,19.417c5.33,5.332,11.803,7.994,19.414,7.994h155.318v82.229c0,7.61,2.662,14.085,7.992,19.41
				c5.327,5.332,11.8,7.994,19.414,7.994h274.091c7.61,0,14.085-2.662,19.41-7.994c5.332-5.325,7.994-11.8,7.994-19.41V137.046
				C511.627,129.432,508.965,122.958,503.633,117.628z M328.904,170.449v85.364h-85.366L328.904,170.449z M146.178,60.813v85.364
				H60.814L146.178,60.813z M202.139,245.535c-5.33,5.33-9.9,12.564-13.706,21.701c-3.805,9.141-5.708,17.508-5.708,25.126v73.083
				H36.547V182.725h118.766c7.616,0,14.087-2.664,19.417-7.994c5.327-5.33,7.994-11.801,7.994-19.412V36.547h109.637v118.771
				L202.139,245.535z M475.078,475.085H219.263V292.355h118.775c7.614,0,14.082-2.662,19.41-7.994
				c5.328-5.325,7.994-11.797,7.994-19.41V146.178h109.629v328.907H475.078z"/>
		</g>
	</svg>

	<?#plus?>
	<svg width="30px" height="30px" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 24 24">
		<g id="icon-plus">
		<path d="m23,10h-8.5c-0.3,0-0.5-0.2-0.5-0.5v-8.5c0-0.6-0.4-1-1-1h-2c-0.6,0-1,0.4-1,1v8.5c0,0.3-0.2,0.5-0.5,0.5h-8.5c-0.6,0-1,0.4-1,1v2c0,0.6 0.4,1 1,1h8.5c0.3,0 0.5,0.2 0.5,0.5v8.5c0,0.6 0.4,1 1,1h2c0.6,0 1-0.4 1-1v-8.5c0-0.3 0.2-0.5 0.5-0.5h8.5c0.6,0 1-0.4 1-1v-2c0-0.6-0.4-1-1-1z"/>
		</g>
	</svg>

	<?#trash?>
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     width="30px" height="30px" viewBox="0 0 774.266 774.266" style="enable-background:new 0 0 774.266 774.266;"
	     xml:space="preserve">
		<g id="icon-trash">
			<path d="M640.35,91.169H536.971V23.991C536.971,10.469,526.064,0,512.543,0c-1.312,0-2.187,0.438-2.614,0.875
				C509.491,0.438,508.616,0,508.179,0H265.212h-1.74h-1.75c-13.521,0-23.99,10.469-23.99,23.991v67.179H133.916
				c-29.667,0-52.783,23.116-52.783,52.783v38.387v47.981h45.803v491.6c0,29.668,22.679,52.346,52.346,52.346h415.703
				c29.667,0,52.782-22.678,52.782-52.346v-491.6h45.366v-47.981v-38.387C693.133,114.286,670.008,91.169,640.35,91.169z
				 M285.713,47.981h202.84v43.188h-202.84V47.981z M599.349,721.922c0,3.061-1.312,4.363-4.364,4.363H179.282
				c-3.052,0-4.364-1.303-4.364-4.363V230.32h424.431V721.922z M644.715,182.339H129.551v-38.387c0-3.053,1.312-4.802,4.364-4.802
				H640.35c3.053,0,4.365,1.749,4.365,4.802V182.339z"/>
			<rect x="475.031" y="286.593" width="48.418" height="396.942"/>
			<rect x="363.361" y="286.593" width="48.418" height="396.942"/>
			<rect x="251.69" y="286.593" width="48.418" height="396.942"/>
		</g>
	</svg>
</div>

<div id="chess" ></div>

<?$lang = [
	'CHESS_SECTION_TITLE',
	'CITRUS_DEVELOPER_CHESS_FLOOR',
	'CHESS_CLONE_SECTION_LINK_TITLE',
	'CHESS_CLONE_SECTION_DELETE_TITLE',
	'CHESS_CLONE_FLOOR_LINK',
	'CHESS_CLONE_FLOOR_DELETE',
	'CHESS_DELETE_FLAT_TITLE',
	'CHESS_ADD_SECTION_LINK',
	'CITRUS_DEVELOPER_CHESS_FLOOR',
	'CHESS_DATA_FORM_CANCEL_BUTTN',
	'CHESS_ADD_FLAT_FORM_TITLE',
	'CHESS_FORM_BOOL_LABEL',
	'CHESS_FORM_CANCEL_BUTTON',
	'CHESS_FORM_SUBMIT_BUTTON'
];?>

<script>
	;(function(){
		new Vue({
			el: '#chess',
			data: <?=\Bitrix\Main\Web\Json::encode(
				[
					'items' => $arResult['SECTIONS'],
					'isEdit' => true,
					'properties' => $arResult['EDIT_PROPERTIES'],
					'lang' => array_reduce($lang, function($previewValue, $langItem){
						$previewValue[$langItem] = Loc::getMessage($langItem);
						return $previewValue;
					}, [])
				]
			);?>,
			template: '<vue-chess :isEdit="isEdit" :items="items" :properties="properties" :lang="lang" />',
		});
	}());
</script>