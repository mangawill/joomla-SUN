<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ROOT.DS.'modules'.DS.'mod_djimageslider'.DS.'helper.php');

JHTML::_('behavior.mootools');
$app = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addScript('modules/mod_djimageslider_marquee/assets/mootools.marquee.js');
$document->addStyleSheet(JURI::base().'modules/mod_djimageslider_marquee/assets/mootools.marquee.css');

// taking the slides from the source
if($params->get('slider_source')==1) {
	jimport('joomla.application.component.helper');
	if(!JComponentHelper::isEnabled('com_djimageslider', true)){
		$app->enqueueMessage(JText::_('MOD_DJIMAGESLIDER_NO_COMPONENT'),'notice');
		return;
	}
	$slides = modDJImageSliderHelper::getImagesFromDJImageSlider($params);
	if($slides==null) {
		$app->enqueueMessage(JText::_('MOD_DJIMAGESLIDER_NO_CATEGORY_OR_ITEMS'),'notice');
		return;
	}
} else {
	$slides = modDJImageSliderHelper::getImagesFromFolder($params);
	if($slides==null) {
		$app->enqueueMessage(JText::_('MOD_DJIMAGESLIDER_NO_CATALOG_OR_FILES'),'notice');
		return;
	}
}

$mid = $module->id;
$mid = 97;
$js = sprintf('
window.addEvent("domready", function(){
    var marquee1 = new MooMarquee(document.id("slider-container%d"), {
    	leftButton: document.id("prev%d"),
    	rightButton: document.id("next%d")
    });
    marquee1.run();
});', $mid, $mid, $mid);
$document->addScriptDeclaration($js);

require(JModuleHelper::getLayoutPath('mod_djimageslider_marquee'));