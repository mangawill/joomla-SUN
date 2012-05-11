<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<div class="registration<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=active.activate'); ?>" method="post" class="form-validate">
<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($fieldset->name);?>
	<?php if (count($fields)):?>
		<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
			<legend><?php echo JText::_($fieldset->label);?></legend>
		<?php endif;?>
			<dl>
		<?php foreach($fields as $field):// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<?php echo $field->input;?>
			<?php else:?>
				<dt>
				<?php echo $field->label; ?>
				</dt>
				<dd><?php echo $field->input;?> <?php echo $field->description;?></dd>
			<?php endif;?>
		<?php endforeach;?>
			</dl>
		</fieldset>
	<?php endif;?>
<?php endforeach;?>
<div class="checkboxs">
	<div class="checkbox"><input id="checkbox_adult" type="checkbox" aria-required="true" required="true"/>我已阅读并且同意太阳城亚洲的规则与条款</div>
	<div class="checkbox"><input id="checkbox_terms" type="checkbox" aria-required="true" required="true"/>我声明我已经年满18岁</div>
</div>
		<div>
			<button type="submit" class="validate button">确认提交</button>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="active.activate" />
			<?php echo JHtml::_('form.token');?>
		</div>
	</form>
</div>
