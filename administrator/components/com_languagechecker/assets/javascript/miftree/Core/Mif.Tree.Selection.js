/*
---
 
name: Mif.Tree.Selection
description: tree nodes selection
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.Selection
 
...
*/

Mif.Tree.implement({
	
	initSelection: function(){
		this.defaults.selectClass = '';
		this.wrapper.addEvent('mousedown', this.attachSelect.bindWithEvent(this));
	},
	
	attachSelect: function(event){
		if(!['icon', 'name', 'node'].contains(this.mouse.target)) return;
		var node = this.mouse.node;
		if(!node) return;
		this.select(node);
	},
	
	select: function(node) {
		if(!node) return this;
		var current = this.selected;
		if (current == node) return this;
		if (current) {
			current.select(false);
			this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
		}
		this.selected = node;
		node.select(true);
		this.fireEvent('select', [node]).fireEvent('selectChange', [node, true]);
		return this;
	},
	
	unselect: function(){
		var current = this.selected;
		if(!current) return this;
		this.selected = false;
		current.select(false);
		this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
		return this;
	},
	
	getSelected: function(){
		return this.selected;
	},
	
	isSelected: function(node){
		return node.isSelected();
	}
	
});

Mif.Tree.Node.implement({
		
	select: function(state) {
		this.state.selected = state;
		if(!Mif.Tree.Draw.isUpdatable(this)) return;
		var wrapper=this.getDOM('wrapper');
		wrapper[(state ? 'add' : 'remove')+'Class'](this.selectClass||'mif-tree-node-selected');
	},
	
	isSelected: function(){
		return this.state.selected;
	}
	
});
