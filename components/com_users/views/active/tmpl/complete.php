<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;
$userCode = JRequest::getVar('usercode');
$user = unserialize(base64_decode($userCode));
?>
<div class="registration-complete<?php echo $this->pageclass_sfx;?>">
您好，您已经注册成功，您的帐号是：<span class="point"><?php echo $user['username']; ?></span> 密码是：<span class="point"><?php echo $user['password'];?></span>，请马上登陆<a href="http://www.33msc.com/game.aspx">http://www.33msc.com/game.aspx</a> 或者回到我们的首页点击 游戏登陆 修改密码,如果您要存款请联系我们的在线客服或者致电我们的开户专线 15190762555。<br/><br/>
太阳城网上娱乐为您服务! <br/><br/>
在线客服一：<a style="font-weight: bold;" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=9596730&amp;site=qq&amp;menu=yes"><img border="0" title="点击这里给我发消息" alt="点击这里给我发消息" src="http://wpa.qq.com/pa?p=2:9596730:41"></a><br/><br/>
在线客服二：<a style="font-weight: bold;" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=9580629&amp;site=qq&amp;menu=yes"><img border="0" title="点击这里给我发消息" alt="点击这里给我发消息" src="http://wpa.qq.com/pa?p=2:9580629:41"></a><br/><br/>
 Msn：cs@818sun.com<br/><br/>菲律宾热线：00639275893118 全球通：13416209808
</div>
