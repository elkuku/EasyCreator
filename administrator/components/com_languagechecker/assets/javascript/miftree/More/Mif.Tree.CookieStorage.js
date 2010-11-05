/*
---
 
name: Mif.Tree.CookieStorage
description: Mif.Tree.Node
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.CookieStorage
 
...
*/

Mif.Tree.CookieStorage = new Class({

	Implements: [Options],
	
	options:{
		store: function(node){
			return node.property.id;
		},
		retrieve: function(value){
			return Mif.id(value);
		},
		event: 'toggle',
		action: 'toggle'
	},

	initialize: function(tree, options){
		this.setOptions(options);
		this.tree = tree;
		this.cookie = new Cookie('mif.tree:' + this.options.event + tree.id||'');
		this.nodes = [];
		this.initSave();
	},
	
	write: function(){
		this.cookie.write(JSON.encode(this.nodes));
	},
	
	read: function(){
		return JSON.decode(this.cookie.read()) || [];
	},
	
	restore: function(data){
		if(!data){
			this.restored = this.restored || this.read();
		}
		var restored = data || this.restored;
		for(var i = 0, l = restored.length; i < l; i++){
			var stored = restored[i];
			var node = this.options.retrieve(stored);
			if(node){
				node[this.options.action](true);
				restored.erase(stored);
				l--;
			}
		}
		return restored;
	},
	
	initSave: function(){
		this.tree.addEvent(this.options.event, function(node, state){
			var value = this.options.store(node);
			if(state){
				this.nodes.include(value);
			}else{
				this.nodes.erase(value);
			}
			this.write();
		}.bind(this));
	}

});
