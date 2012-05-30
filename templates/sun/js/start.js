var Sun = window.Sun || {};

Sun = (function($, self) {
	
	function reloadUserClick() {
		var url = "index.php?option=com_users&task=active.getusername";
		var ele = $("jform_username");
		var myRequest = new Request({
		    url: url,
		    onSuccess: function(responseText, responseXML){
		    	ele.value = responseText;
		    }
		}).get();
	}
    
    self.userActive = function(){
    	var ele = $("jform_username_suffix");
    	if(!ele) return;
    	ele.innerHTML = '<img src="templates/sun/images/account_reset.png"/>';
    	ele.addEvent("click", reloadUserClick);
    }
    
    return self;
})(document.id, Sun);

window.addEvent("domready", function(){
	Sun.userActive();
});