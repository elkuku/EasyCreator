/*
---
 
name: Mif.Tree.Node
description: Mif.Tree.Node
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.Node
 
...
*/

Mif.Tree.Node = new Class({

	Implements: [Events],
	
	initialize: function(structure, options) {
		$extend(this, structure);
		this.children = [];
		this.type = options.type || this.tree.dfltType;
		this.property = options.property || {};
		this.data = options.data;
		this.state = $extend($unlink(this.tree.dfltState), options.state);
		this.$calculate();
		this.UID = Mif.Tree.Node.UID++;
		Mif.Tree.Nodes[this.UID] = this;
		var id = this.id;
		if(id != null) Mif.ids[id] = this;
		this.tree.fireEvent('nodeCreate', [this]);
		this._property = ['id', 'name', 'cls', 'openIcon', 'closeIcon', 'openIconUrl', 'closeIconUrl', 'hidden'];
	},
	
	$calculate: function(){
		$extend(this, $unlink(this.tree.defaults));
		this.type = $splat(this.type);
		this.type.each(function(type){
			var props = this.tree.types[type];
			if(props) $extend(this, props);
		}, this);
		$extend(this, this.property);
		return this;
	},
	
	getDOM: function(what){
		var node = $(this.tree.DOMidPrefix+this.UID);
		if(what == 'node') return node;
		var wrapper = node.getFirst();
		if(what == 'wrapper') return wrapper;
		if(what == 'children') return wrapper.getNext();
		return wrapper.getElement('.mif-tree-'+what);
	},
	
	getGadjetType: function(){
		return (this.loadable && !this.isLoaded()) ? 'plus' : (this.hasVisibleChildren() ? (this.isOpen() ? 'minus' : 'plus') : 'none');
	},
	
	toggle: function(state) {
		if(this.state.open == state || this.$loading || this.$toggling) return this;
		var parent = this.getParent();
		function toggle(type){
			this.state.open = !this.state.open;
			if(type == 'drawed'){
				this.drawToggle();
			}else{
				parent._toggle = (parent._toggle||[])[this.state.open ? 'include' : 'erase'](this);
			}
			this.fireEvent('toggle', [this.state.open]);
			this.tree.fireEvent('toggle', [this, this.state.open]);
			return this;
		}
		if(parent && !parent.$draw){
			return toggle.apply(this, []);
		}
		if(this.loadable && !this.state.loaded) {
            if(!this.load_event){
                this.load_event = true;
                this.addEvent('load',function(){
                    this.toggle();
                }.bind(this));
            }
            return this.load();
        }
		if(!this.hasChildren()) return this;
		return toggle.apply(this, ['drawed']);
	},
	
	drawToggle: function(){
		this.tree.$getIndex();
		Mif.Tree.Draw.update(this);
	},
	
	recursive: function(fn, args){
		args=$splat(args);
		if(fn.apply(this, args) !== false){
			this.children.each(function(node){
				if(node.recursive(fn, args) === false){
					return false;
				}
			});
		}
		return this;
	},
	
	isOpen: function(){
		return this.state.open;
	},
	
	isLoaded: function(){
		return this.state.loaded;
	},
	
	isLast: function(){
		if(this.parentNode == null || this.parentNode.children.getLast() == this) return true;
		return false;
	},
	
	isFirst: function(){
		if(this.parentNode == null || this.parentNode.children[0] == this) return true;
		return false;
	},
	
	isRoot: function(){
		return this.parentNode == null ? true : false;
	},
	
	getChildren: function(){
		return this.children;
	},
	
	hasChildren: function(){
		return this.children.length ? true : false;
	},
	
	index: function(){
		if( this.isRoot() ) return 0;
		return this.parentNode.children.indexOf(this);
	},
	
	getNext: function(){
		if(this.isLast()) return null;
		return this.parentNode.children[this.index()+1];
	},
	
	getPrevious: function(){
		if( this.isFirst() ) return null;
		return this.parentNode.children[this.index()-1];
	},
	
	getFirst: function(){
		if(!this.hasChildren()) return null;
		return this.children[0];
	},
	
	getLast: function(){
		if(!this.hasChildren()) return null;
		return this.children.getLast();		
	},
	
	getParent: function(){
		return this.parentNode;
	},
	
	_getNextVisible: function(){
		var current=this;
		if(current.isRoot()){
			if(!current.isOpen() || !current.hasChildren(true)) return false;
			return current.getFirst(true);
		}else{
			if(current.isOpen() && current.getFirst(true)){
				return current.getFirst(true);
			}else{
				var parent = current;
				do{
					current = parent.getNext(true);
					if(current) return current;
					parent = parent.parentNode;
				}while(parent);
				return false;
			}
		}
	},
	
	getPreviousVisible: function(){
		var index = this.tree.$index.indexOf(this);
		return index == 0 ? null : this.tree.$index[index-1];
	},
	
	getNextVisible: function(){
		var index = this.tree.$index.indexOf(this);
		return index < this.tree.$index.length-1 ? this.tree.$index[index+1] : null;
	},
	
	getVisiblePosition: function(){
		return this.tree.$index.indexOf(this);
	},
	
	hasVisibleChildren: function(){
		if(!this.hasChildren()) return false;
		if(this.isOpen()){
			var next = this.getNextVisible();
			if(!next) return false;
			if(next.parentNode != this) return false;
			return true;
		}else{
			var child = this.getFirst();
			while(child){
				if(!child.hidden) return true;
				child = child.getNext();
			}
			return false;
		}
	},
	
	isLastVisible: function(){
		var next = this.getNext();
		while(next){
			if(!next.hidden) return false;
			next = next.getNext();
		};
		return true;
	},
		
	contains: function(node){
		while(node){
			if(node == this) return true;
			node = node.parentNode;
		};
		return false;
	},

	addType: function(type){
		return this.processType(type, 'add');
	},

	removeType: function(type){
		return this.processType(type, 'remove');
	},
	
	setType: function(type){
		return this.processType(type, 'set');
	},
	
	processType: function(type, action){
		switch(action){
			case 'add': this.type.include(type); break;
			case 'remove': this.type.erase(type); break;
			case 'set': this.type = type; break;
		}
		var current = {};
		this._property.each(function(p){
			current[p] = this[p];
		}, this);
		this.$calculate();
		this._property.each(function(p){
			this.updateProperty(p, current[p], this[p]);
		}, this);
		return this;
	},
	
	set: function(obj){
		this.tree.fireEvent('beforeSet', [this, obj]);
		var property = obj.property||obj||{};
		for(var p in property){
			var nv = property[p];
			var cv = this[p];
			this.updateProperty(p, cv, nv);
			this[p] = this.property[p] = nv;
		}
		this.tree.fireEvent('set', [this, obj]);
		return this;
	},
	
	updateProperty: function(p, cv, nv){
		if(nv == cv) return this;
		if(p == 'id'){
			delete Mif.ids[cv];
			if(nv) Mif.ids[nv]=this;
			return this;
		}
		if(!Mif.Tree.Draw.isUpdatable(this)) return this;
		switch(p){
			case 'name':
				this.getDOM('name').set('html', nv);
				return this;
			case 'cls':
				this.getDOM('wrapper').removeClass(cv).addClass(nv);
				return this;
			case 'openIcon':
			case 'closeIcon':
				this.getDOM('icon').removeClass(cv).addClass(nv);
				return this;
			case 'openIconUrl':
			case 'closeIconUrl':
				var icon = this.getDOM('icon');
				icon.setStyle('background-image', 'none');
				if(nv) icon.setStyle('background-image', 'url('+nv+')');
				return this;
			case 'hidden':
				this.getDOM('node').setStyle('display', nv ? 'none' : 'block');
				var _previous = this.getPreviousVisible();
				var _next = this.getNextVisible();
				var parent = this.getParent();
				this[p] = this.property[p]=nv;
				this.tree.$getIndex();
				var previous = this.getPreviousVisible();
				var next = this.getNextVisible();
				[_previous, _next, previous, next, parent, this].each(function(node){
					Mif.Tree.Draw.update(node);
				});
				return this;
		}
		return this;
	},
	
	updateOpenState: function(){
		if(this.state.open){
			this.state.open = false;
			this.toggle();
		}
	}
	
});

Mif.Tree.Node.UID = 0;
Mif.Tree.Nodes = {};