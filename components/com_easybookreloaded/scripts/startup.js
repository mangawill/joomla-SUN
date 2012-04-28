window.addEvent("domready", function(){
	var container = document.id("entry_container");
	var myRequest = new Request.HTML({
	    url: "index.php",
	    filter: ".easy_entrylink",
	    onRequest: function(){
	    	container.addClass("ajax-loading");
	    },
	    onSuccess: function(responseElements){
	    	console.log(responseElements);
	    	container.removeClass("ajax-loading");
	    	container.empty().adopt(responseElements);
	    },
	    onFailure: function(){
	        alert('Sorry, your request failed :(');
	    	container.removeClass("ajax-loading");
	    }
	}).get("option=com_easybookreloaded&controller=entry&task=add&tmpl=component&print=1");
});