/*
---
 
name: Mif.Tree.KeyNav
description: Mif.Tree.KeyNav
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.KeyNav
 
...
*/

Mif.Tree.KeyNav=new Class({
	
	initialize: function(tree){
		this.tree = tree;
		this.bound = {
			action: this.action.bind(this),
			attach: this.attach.bind(this),
			detach: this.detach.bind(this)
		};
		tree.addEvents({
			'focus': this.bound.attach,
			'blur': this.bound.detach
		});
	},
	
	attach: function(){
		var event = Browser.Engine.trident || Browser.Engine.webkit ? 'keydown' : 'keypress';
		document.addEvent(event, this.bound.action);
	},
	
	detach: function(){
		var event = Browser.Engine.trident || Browser.Engine.webkit ? 'keydown' : 'keypress';
		document.removeEvent(event, this.bound.action);
	},
	
	action: function(event){
		if(!['down','left','right','up', 'pgup', 'pgdown', 'end', 'home'].contains(event.key)) return;
		var tree = this.tree;
		if(!tree.selected){
			tree.select(tree.forest ? tree.root.getFirst() : tree.root);
		}else{
			var current = tree.selected;
			switch (event.key){
				case 'down': this.goForward(current); event.stop(); break;  
				case 'up': this.goBack(current); event.stop(); break;   
				case 'left': this.goLeft(current); event.stop(); break;
				case 'right': this.goRight(current); event.stop(); break;
				case 'home': this.goStart(current); event.stop(); break;
				case 'end': this.goEnd(current); event.stop(); break;
				case 'pgup': this.goPageUp(current); event.stop(); break;
				case 'pgdown': this.goPageDown(current); event.stop(); break;
			}
		}
		tree.scrollTo(tree.selected);
	},

	goForward: function(current){
		var forward = current.getNextVisible();
		if(forward) this.tree.select(forward);
	},
	
	goBack: function(current){
		var back = current.getPreviousVisible();
		if (back) this.tree.select(back);
	},
	
	goLeft: function(current){
		if(current.isRoot()){
			if(current.isOpen()){
				current.toggle();
			}else{
				return false;
			}
		}else{
			if( current.hasChildren(true) && current.isOpen() ){
				current.toggle();
			}else{
				if(current.tree.forest && current.getParent().isRoot()) return false;
				return this.tree.select(current.getParent());
			}
		}
		return true;
	},
	
	goRight: function(current){
		if(!current.hasChildren(true) && !current.loadable){
			return false;
		}else if(!current.isOpen()){
			return current.toggle();
		}else{
			return this.tree.select(current.getFirst(true));
		}
	},
	
	goStart: function(){
		this.tree.select(this.tree.$index[0]);
	},
	
	goEnd: function(){
		this.tree.select(this.tree.$index.getLast());
	},
	
	goPageDown: function(current){
		var tree = this.tree;
		var count = (tree.container.clientHeight/tree.height).toInt() - 1;
		var newIndex = Math.min(tree.$index.indexOf(current) + count, tree.$index.length - 1);
		tree.select(tree.$index[newIndex]);
	},
	
	goPageUp: function(current){
		var tree = this.tree;
		var count = (tree.container.clientHeight/tree.height).toInt() - 1;
		var newIndex = Math.max(tree.$index.indexOf(current) - count, 0);
		tree.select(tree.$index[newIndex]);
	}
	
});

Event.Keys.extend({
	'pgdown': 34,
	'pgup': 33,
	'home': 36,
	'end': 35
});
