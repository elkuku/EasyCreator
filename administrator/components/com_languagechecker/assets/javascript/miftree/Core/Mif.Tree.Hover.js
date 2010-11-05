/*
---
 
name: Mif.Tree.Hover
description: hover(mouseover/mouseout) events/effects
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.Hover
 
...
*/

Mif.Tree.implement({
	
	initHover: function(){
		this.defaults.hoverClass = '';
		this.wrapper.addEvent('mousemove', this.hover.bind(this));
		this.wrapper.addEvent('mouseout', this.hover.bind(this));
		this.defaultHoverState = {
			gadjet: false,
			checkbox: false,
			icon: false,
			name: false,
			node: false
		};
		this.hoverState = $unlink(this.defaultHoverState);
	},
	
	hover: function(){
		var cnode = this.mouse.node;
		var ctarget = this.mouse.target;
		$each(this.hoverState, function(node, target, state){
			if(node == cnode && (target == 'node'||target==ctarget)) return;
			if(node) {
				Mif.Tree.Hover.out(node, target);
				state[target] = false;
				this.fireEvent('hover', [node, target, 'out']);
			}
			if(cnode && (target == 'node'||target == ctarget)) {
				Mif.Tree.Hover.over(cnode, target);
				state[target] = cnode;
				this.fireEvent('hover', [cnode, target, 'over']);
			}else{
				state[target] = false;
			}
		}, this);
	},
	
	updateHover: function(){
		this.hoverState = $unlink(this.defaultHoverState);
		this.hover();
	}
	
});

Mif.Tree.Hover = {
	
	over: function(node, target){
		var wrapper = node.getDOM('wrapper');
		wrapper.addClass((node.hoverClass||'mif-tree-hover')+'-'+target);
		if(node.state.selected) wrapper.addClass((node.hoverClass||'mif-tree-hover')+'-selected-'+target);
	},
	
	out: function(node, target){
		var wrapper = node.getDOM('wrapper');
		wrapper.removeClass((node.hoverClass||'mif-tree-hover')+'-'+target).removeClass((node.hoverClass||'mif-tree-hover')+'-selected-'+target);
	}
	
};
