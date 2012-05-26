<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$canDo = UsersHelper::getActions();

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid(document.id('user-form'))) {
			Joomla.submitform(task, document.getElementById('user-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="user-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend>Function</legend>
			<ul class="adminformlist">
			<li>
				只支持CSV文件上传
			</li>
			<li>
				<input name="csv" type="file" />
				<button type="submit">Import</button>
			</li>
			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<input type="hidden" name="task" value="user.doimport" />
		<input type="hidden" name="option" value="com_users"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
