<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Registration model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersModelActive extends JModelForm
{
	/**
	 * @var		object	The user registration data.
	 * @since	1.6
	 */
	protected $data;

	/**
	 * Method to get the registration form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_users');

			// Override the base user data with any data in the session.
			$temp = (array)$app->getUserState('com_users.active.data', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

			// Get the groups the user should be added to after registration.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system	= $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.active', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the registration form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.active', 'active', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return $this->getData();
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		$userParams	= JComponentHelper::getParams('com_users');

		//Add the choice for site language at registration time
		if ($userParams->get('site_language') == 1 && $userParams->get('frontend_userparams') == 1)
		{
			$form->loadFile('sitelang', false);
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app	= JFactory::getApplication();
		$params	= $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function active($temp)
	{
		$config = JFactory::getConfig();
		$db		= $this->getDbo();

		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array)$this->getData();

		// Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];
		$data['password2']	= $data['password'];
		
		// authentication
		jimport('joomla.user.authentication');

		$authenticate = JAuthentication::getInstance();
		$response = $authenticate->authenticate($data, array());

		if ($response->status != JAuthentication::STATUS_SUCCESS){
			$this->setError(JText::_('password error.'));
			return false;
		}
		$query = $db->getQuery(true);
		$query->from('#__users AS users')
			->select('users.id')
			->where(sprintf('username=%s AND used=0', $db->quote($data['username'])));
		$db->setQuery($query);
		$userId = $db->loadResult();
		$this->userId = $userId;
		
		if(!$userId) {
			$this->setError(JText::_('User been used.'));
			return false;
		}
		
		$user->load($userId);

		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}
		
		// send mail
		$this->sendAdminMail($user);
		
		return true;
	}
	
	public function sendAdminMail($user)
	{
		$app	= JFactory::getApplication();
		$acl	= JFactory::getACL();
		$db		= $this->getDbo();
		$groupId = 8;
		$subject = '太阳城新用户注册';
		$body = '用户名: '.$user->name."\r\n";
		$body .= '电子邮箱: '.$user->email."\r\n";
		$body .= '移动电话: '.$user->phone."\r\n";
		$body .= 'QQ: '.$user->qq."\r\n";
		$body .= '取款密码: '.$user->getpassword."\r\n";
		
		$uIds = $acl->getUsersByGroup($groupId, false);
		$query	= $db->getQuery(true);
		$query->select('email');
		$query->from('#__users');
		$query->where('id IN (' . implode(',', $uIds) . ')');
		$query->where("block = 0");
		$db->setQuery($query);
		$rows = $db->loadColumn();
		
		$mailer = JFactory::getMailer();
		$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		//$mailer->IsHTML(true);
		$mailer->addRecipient($rows);
		if (!$mailer->Send()) {
		  JError::raiseWarning(500, JText::_('ERROR_SENDING_EMAIL'));
		}
	}
	
	public function used()
	{
		$user = new JUser();
		$user->load($this->userId);
		$user->used = 1;
		$user->save();
	}
	
	public function getUserName()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->from('#__users AS users')
			->join('INNER', '#__user_usergroup_map AS usergroup_map ON usergroup_map.user_id = users.id')
			->where('users.used=0 AND usergroup_map.group_id=2')
			->select('users.*')->order('RAND()');
		$db->setQuery($query, 0, 1);
		$user = $db->loadObject();
		if($user) {
			return $user->username;
		} else {
			return '';
		}
	}
}
