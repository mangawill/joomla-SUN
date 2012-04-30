<?php
/**
* @version 1.3 RC1
* @package DJ Image Slider
* @subpackage DJ Image Slider Component
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
*
*
* DJ Image Slider is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Image Slider is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Image Slider. If not, see <http://www.gnu.org/licenses/>.
*
*/

defined('_JEXEC') or die;

abstract class DJImageSliderHelper
{
	
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_DJIMAGESLIDER_SUBMENU_SLIDES'),
			'index.php?option=com_djimageslider&view=items',
			$vName == 'items'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_DJIMAGESLIDER_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_djimageslider',
			$vName == 'categories'
		);

		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_DJIMAGESLIDER_CATEGORIES_TITLE',JText::_('com_djimageslider')),
				'slider-categories');
		}
	}
	
}
?>