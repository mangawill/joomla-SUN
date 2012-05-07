<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerActive extends UsersController
{

	/**
	 * Method to register a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('Active', 'UsersModel');

		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_users.active.data', $requestData);

			// Redirect back to the registration screen.
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=active', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->active($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_users.active.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage($model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=active', false));
			return false;
		}
		
		$model->used();

		// Flush the data from the session.
		$app->setUserState('com_users.active.data', null);

		// Redirect to the profile screen.
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=active&layout=complete', false));


		return true;
	}
}
