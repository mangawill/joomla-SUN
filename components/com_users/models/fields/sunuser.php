<?php
/**
 * @version     0.0.1
 * @package     com_simple
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldSunuser extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Sunuser';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// default value
		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		if(empty($value)) {
			$value = $this->getDefault();
		}

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. $value . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
	}
	
	private function getDefault()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->from('#__users AS users')
			->join('INNER', '#__user_usergroup_map AS usergroup_map ON usergroup_map.user_id = users.id')
			->where('users.used=0 AND usergroup_map.group_id=2')
			->select('users.*');
		$db->setQuery($query, 0, 1);
		$user = $db->loadObject();
		if($user) {
			return $user->username;
		} else {
			return '';
		}
	}
}