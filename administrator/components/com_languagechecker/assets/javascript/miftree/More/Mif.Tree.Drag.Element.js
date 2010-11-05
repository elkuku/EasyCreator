/*
---
 
name: Mif.Tree.Drag.Element
description: dom element droppable
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree.Drag
provides: Mif.Tree.Drag.Element
 
...
*/

Mif.Tree.Drag.Element=new Class({

	Implements: [Options, Events],

	initialize: function(element, options){
		
		this.element=$(element);
		
		this.setOptions(options);
		
	},
	
	getElement: function(){
		return this.element;
	},
	
	onleave: function(){
		this.where='notAllowed';
		Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+this.where;
	},
	
	onenter: function(){
		this.where='inside';
		Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+this.where;
	},
	
	beforeDrop: function(){
		if(this.options.beforeDrop){
			this.options.beforeDrop.apply(this, [this.current, this.trarget, this.where]);
		}else{
			this.drop();
		}
	},
	
	drop: function(){
		Mif.Tree.Drag.ghost.dispose();
		this.fireEvent('drop', Mif.Tree.Drag.current);
	}
	

});
