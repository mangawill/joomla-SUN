var MooMarquee = new Class({
    Implements: Options,
    options: {
        speed: 30,
        direction: "left",
        childWrapper: "ul",
        childElements: "li",
        cssClass: "marquee-container"
    },
    initialize: function(element, options){
        this.element = element;
        this.setOptions(options);
        this.direction = this.options.direction;
        this.speed = this.options.speed;
        
        var that = this;
        var childWrapper = this.element.getElement(this.options.childWrapper);
        
        // css class
        this.element.addClass(this.options.cssClass);
        
        // clone innerHTML
        childWrapper.innerHTML = childWrapper.innerHTML + childWrapper.innerHTML;
        
        // total width
        var childElements = this.element.getElements(this.options.childElements);
        this.totalWidth = 0;
        childElements.each(function(el, index){
            that.totalWidth += el.offsetWidth;
        });
        this.element.getElement("ul").setStyle("width", this.totalWidth+"px");
        
        // click
        if(this.options.leftButton) {
        	this.options.leftButton.addEvent("click", function(){
    	        that.direction = "left";
            });
        }
        if(this.options.rightButton) {
        	this.options.rightButton.addEvent("click", function(){
    	        that.direction = "right";
            });
        }
        
        // mouseover
        this.element.addEvent("mouseover", function(){
            that.stop();
        });
        
        // mouseout
        this.element.addEvent("mouseout", function(){
            that.run();
        });
    },
    
    move: function(){
    	if(this.direction == "left") this.moveLeft();
    	else this.moveRight();
    },
    
    run: function(){
    	var that = this;
    	this.interval = setInterval(function(){that.move();}, that.speed);
    },
    
    stop: function(){
    	clearInterval(this.interval);
    },
    
    moveLeft: function(){
        if(this.element.scrollLeft > this.totalWidth/2) {
            this.element.scrollLeft = 0;
        }
        else {
            this.element.scrollLeft++;
        }
    },
    
    moveRight: function(){
        if(this.element.scrollLeft <= 0) {
            this.element.scrollLeft = this.totalWidth/2;
        } else {
            this.element.scrollLeft--;
        }
    }
});