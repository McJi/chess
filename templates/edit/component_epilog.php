<?
CJSCore::Init(['vue', 'swiper', 'magnificPopup']);

\Bitrix\Main\Page\Asset::getInstance()->addJs($templateFolder.'/vueComponent/dist/build.js');

//\Bitrix\Main\Page\Asset::getInstance()->addJs('http://localhost:8080/dist/build.js', true);
