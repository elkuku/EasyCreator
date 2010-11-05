/*
---
 
name: Mif.Tree.Drag
description: implements drag and drop
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: [Mif.Tree, Mif.Tree.Transform, more:/Drag.Move]
provides: Mif.Tree.Drag
 
...
*/

Mif.Tree.Drag = new Class({
	
	Implements: [new Events, new Options],
	
	Extends: Drag,
	
	options:{
		group: 'tree',
		droppables: [],
		snap: 4,
		animate: true,
		open: 600,//time to open node
		scrollDelay: 100,
		scrollSpeed: 100,
		modifier: 'control',//copy
		startPlace: ['icon', 'name'],
		allowContainerDrop: true
	},

	initialize: function(tree, options){
		tree.drag = this;
		this.setOptions(options);
		$extend(this, {
			tree: tree,
			snap: this.options.snap,
			groups: [],
			droppables: [],
			action: this.options.action
		});
		
		this.addToGroups(this.options.group);
		
		this.setDroppables(this.options.droppables);
		
		$extend(tree.defaults, {
			dropDenied: [],
			dragDisabled: false
		});
		tree.addEvent('drawRoot',function(){
			tree.root.dropDenied.combine(['before', 'after']);
		});
		
		this.pointer = new Element('div').addClass('mif-tree-pointer').injectInside(tree.wrapper);
		
		this.current = Mif.Tree.Drag.current;
		this.target = Mif.Tree.Drag.target;
		this.where = Mif.Tree.Drag.where;

		this.element = [this.current, this.target, this.where];
		this.document = tree.wrapper.getDocument();
		
		this.selection = (Browser.Engine.trident) ? 'selectstart' : 'mousedown';
		
		this.bound = {
			start: this.start.bind(this),
			check: this.check.bind(this),
			drag: this.drag.bind(this),
			stop: this.stop.bind(this),
			cancel: this.cancel.bind(this),
			eventStop: $lambda(false),
			leave: this.leave.bind(this),
			enter: this.enter.bind(this),
			keydown: this.keydown.bind(this)
		};
		this.attach();
		
		this.addEvent('start', function(){
			Mif.Tree.Drag.dropZone=this;
			this.tree.unselect();
			document.addEvent('keydown', this.bound.keydown);
			this.setDroppables();
			this.droppables.each(function(item){
				item.getElement().addEvents({mouseleave: this.bound.leave, mouseenter: this.bound.enter});
			}, this);
			Mif.Tree.Drag.current.getDOM('name').addClass('mif-tree-drag-current');
			this.addGhost();
		}, true);
		this.addEvent('complete', function(){
			document.removeEvent('keydown', this.bound.keydown);
			this.droppables.each(function(item){
				item.getElement().removeEvent('mouseleave', this.bound.leave).removeEvent('mouseenter', this.bound.enter);
			}, this);
			Mif.Tree.Drag.current.getDOM('name').removeClass('mif-tree-drag-current');
			var dropZone = Mif.Tree.Drag.dropZone;
			if(!dropZone || dropZone.where=='notAllowed'){
				Mif.Tree.Drag.startZone.onstop();
				Mif.Tree.Drag.startZone.emptydrop();
				return;
			}
			if(dropZone.onstop) dropZone.onstop();
			dropZone.beforeDrop();
		});
	},
	
	getElement: function(){
		return this.tree.wrapper;
	},
	
	addToGroups: function(groups){
		groups = $splat(groups);
		this.groups.combine(groups);
		groups.each(function(group){
			Mif.Tree.Drag.groups[group]=(Mif.Tree.Drag.groups[group]||[]).include(this);
		}, this);
	},
	
	setDroppables: function(droppables){
		this.droppables.combine($splat(droppables));
		this.groups.each(function(group){
			this.droppables.combine(Mif.Tree.Drag.groups[group]);
		}, this);
	},

	attach: function(){
		this.tree.wrapper.addEvent('mousedown', this.bound.start);
		return this;
	},

	detach: function(){
		this.tree.wrapper.removeEvent('mousedown', this.bound.start);
		return this;
	},
	
	dragTargetSelect: function(){
		function addDragTarget(){
			this.current.getDOM('name').addClass('mif-tree-drag-current');
		}
		function removeDragTarget(){
			this.current.getDOM('name').removeClass('mif-tree-drag-current');
		}
		this.addEvent('start',addDragTarget.bind(this));
		this.addEvent('beforeComplete',removeDragTarget.bind(this));
	},
	
	leave: function(event){
		var dropZone = Mif.Tree.Drag.dropZone;
		if(dropZone){
			dropZone.where = 'notAllowed';
			Mif.Tree.Drag.ghost.firstChild.className = 'mif-tree-ghost-icon mif-tree-ghost-'+dropZone.where;
			if(dropZone.onleave) dropZone.onleave();
			Mif.Tree.Drag.dropZone = false;
		}
		
		var relatedZone = this.getZone(event.relatedTarget);
		if(relatedZone) this.enter(null, relatedZone);
	},
	
	onleave: function(){
		this.tree.unselect();
		this.clean();
		$clear(this.scrolling);
		this.scrolling = null;
		this.target = false;
	},
	
	enter: function(event, zone){
		if(event) zone = this.getZone(event.target);
		var dropZone = Mif.Tree.Drag.dropZone;
		if(dropZone && dropZone.onleave) dropZone.onleave();
		Mif.Tree.Drag.dropZone = zone;
		zone.current = Mif.Tree.Drag.current;
		if(zone.onenter) zone.onenter();
	},
	
	onenter: function(){
		this.onleave();
	},
	
	getZone: function(target){//private leave/enter
		if(!target) return false;
		var parent = $(target);
		do{
			for(var l = this.droppables.length;l--;){
				var zone = this.droppables[l];
				if( parent == zone.getElement() ) {
					return zone;
				}
			}
			parent = parent.getParent();
		}while(parent);
		return false;
	},
	
	keydown: function(event){
		if(event.key == 'esc') {
			var zone = Mif.Tree.Drag.dropZone;
			if(zone) zone.where = 'notAllowed';
			this.stop(event);
		}
	},
	
	autoScroll: function(){
		var y = this.y;
		if(y == -1) return;
		var wrapper = this.tree.wrapper;
		var top = y-wrapper.scrollTop;
		var bottom = wrapper.offsetHeight-top;
		var sign = 0;
		var delta;
		if(top < this.tree.height){
			delta = top;
			sign = 1;
		}else if(bottom < this.tree.height){
			delta = bottom;
			sign = -1;
		}
		if(sign && !this.scrolling){
			this.scrolling = function(node){
				if(y != this.y){
					y = this.y;
					delta = (sign == 1 ? (y - wrapper.scrollTop) : (wrapper.offsetHeight - y + wrapper.scrollTop)) || 1;
				}
				wrapper.scrollTop = wrapper.scrollTop - sign*this.options.scrollSpeed/delta;
			}.periodical(this.options.scrollDelay, this, [sign]);
		}
		if(!sign){
			$clear(this.scrolling);
			this.scrolling = null;
		}
	},
	
	start: function(event){//mousedown
		if(event.rightClick) return;
		if (this.options.preventDefault) event.preventDefault();
		this.fireEvent('beforeStart', this.element);

		var target = this.tree.mouse.target;
		if(!target) return;
		this.current = $splat(this.options.startPlace).contains(target) ? this.tree.mouse.node : false;
		if(!this.current || this.current.dragDisabled) {
			return;
		}
		Mif.Tree.Drag.current = this.current;
		Mif.Tree.Drag.startZone = this;
		
		this.mouse = {start: event.page};
		this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
		this.document.addEvent(this.selection, this.bound.eventStop);
	},
	
	drag: function(event){
		Mif.Tree.Drag.ghost.position({x:event.page.x+20,y:event.page.y+20});
		var dropZone = Mif.Tree.Drag.dropZone;
		if(!dropZone||!dropZone.ondrag) return;
		Mif.Tree.Drag.dropZone.ondrag(event);
	},

	ondrag: function(event){
		this.autoScroll();
		if(!this.checkTarget()) return;
		this.clean();
		var where = this.where;
		var target = this.target;
		var ghostType = where;
		if(where == 'after' && target && (target.getNext()) || where == 'before' && target.getPrevious()){
			ghostType = 'between';
		}
		Mif.Tree.Drag.ghost.firstChild.className = 'mif-tree-ghost-icon mif-tree-ghost-' + ghostType;
		if(where == 'notAllowed'){
			this.tree.unselect();
			return;
		}
		if(target && target.tree) this.tree.select(target);
		if(where == 'inside'){
			if(target.tree && !target.isOpen() && !this.openTimer && (target.loadable || target.hasChildren()) ){
				this.wrapper = target.getDOM('wrapper').setStyle('cursor', 'progress');
				this.openTimer = function(){
					target.toggle();
					this.clean();
				}.delay(this.options.open,this);
			}
		}else{
			var wrapper = this.tree.wrapper;
			var top = this.index*this.tree.height;
			if(where == 'after') top += this.tree.height;
			this.pointer.setStyles({
				left: wrapper.scrollLeft,
				top: top,
				width: wrapper.clientWidth
			});
		}
	},

	clean: function(){
		this.pointer.style.width = 0;
		if(this.openTimer){
			$clear(this.openTimer);
			this.openTimer = false;
			this.wrapper.style.cursor = 'inherit';
			this.wrapper = false;
		}
	},
	
	addGhost: function(){
		var wrapper = this.current.getDOM('wrapper');
		var ghost = new Element('span').addClass('mif-tree-ghost');
		ghost.adopt(Mif.Tree.Draw.node(this.current).getFirst())
		.injectInside(document.body).addClass('mif-tree-ghost-notAllowed').setStyle('position', 'absolute');
		new Element('span').set('html',Mif.Tree.Draw.zeroSpace).injectTop(ghost);
		ghost.getLast().getFirst().className = '';
		Mif.Tree.Drag.ghost = ghost;
	},
	
	checkTarget: function(){
		this.y = this.tree.mouse.coords.y;
		var target = this.tree.mouse.node;
		if(!target){
			if(this.options.allowContainerDrop && (this.tree.forest || !this.tree.root)){
				this.target = this.tree.$index.getLast();
				this.index = this.tree.$index.length-1;
				if(this.index == -1){
					this.where = 'inside';
					this.target = this.tree.root || this.tree;
				}else{
					this.where = 'after';
				}
			}else{
				this.target = false;
				this.where = 'notAllowed';
			}
			this.fireEvent('drag');
			return true;
		};
		if((this.current instanceof Mif.Tree.Node) && this.current.contains(target)){
			this.target = target;
			this.where = 'notAllowed';
			this.fireEvent('drag');
			return true;
		};
		this.index = Math.floor(this.y/this.tree.height);
		var delta = this.y - this.index*this.tree.height;
		var deny = target.dropDenied;
		if(this.tree.sortable){
			deny.include('before').include('after');
		};
		var where;
		if(!deny.contains('inside') && delta > (this.tree.height/4) && delta < (3/4*this.tree.height)){
			where = 'inside';
		}else{
			if(delta < this.tree.height/2){
				if(deny.contains('before')){
					if(deny.contains('inside')){
						where = deny.contains('after') ? 'notAllowed' : 'after';
					}else{
						where = 'inside';
					}
				}else{
					where = 'before';
				}
			}else{
				if(deny.contains('after')){
					if(deny.contains('inside')){
						where = deny.contains('before') ? 'notAllowed' : 'before';
					}else{
						where = 'inside';
					}
				}else{
					where = 'after';
				}
			}
		};
		if(this.where == where && this.target == target) return false;
		this.where = where; 
		this.target = target;
		this.fireEvent('drag');
		return true;
	},
	
	emptydrop: function(){
		var current = this.current, target = this.target, where = this.where;
		var scroll = this.tree.scroll;
		var complete = function(){
			scroll.removeEvent('complete', complete);
			if(this.options.animate){
				var wrapper = current.getDOM('wrapper');
				var position = wrapper.getPosition();
				Mif.Tree.Drag.ghost.set('morph',{
					duration: 'short',
					onComplete: function(){
						Mif.Tree.Drag.ghost.dispose();
						this.fireEvent('emptydrop', this.element);
					}.bind(this)
				});
				Mif.Tree.Drag.ghost.morph({left: position.x, top: position.y});
				return;
			};
			Mif.Tree.Drag.ghost.dispose();
			this.fireEvent('emptydrop', this.element);
			return;
		}.bind(this);
		scroll.addEvent('complete', complete);
		this.tree.select(this.current);
		this.tree.scrollTo(this.current);
	},
	
	beforeDrop: function(){
		if(this.options.beforeDrop){
			this.options.beforeDrop.apply(this, [this.current, this.target, this.where]);
		}else{
			this.drop();
		}
	},
	
	drop: function(){
		var current = this.current, target = this.target, where = this.where;
		Mif.Tree.Drag.ghost.dispose();
		var action = this.action || (this.tree.key[this.options.modifier] ? 'copy' : 'move');
		if(this.where == 'inside' && target.tree && !target.isOpen()){
			if(target.tree) target.toggle();
			if(target.$loading){
				var onLoad = function(){
					this.tree[action](current, target, where);
					this.tree.select(current).scrollTo(current);
					this.fireEvent('drop', [current, target, where]);
					target.removeEvent('load',onLoad);
				};
				target.addEvent('load',onLoad);
				return;
			};
		};
		if(!(current instanceof Mif.Tree.Node )){
			current = current.toNode(this.tree);
		}
		this.tree[action](current, target, where);
		this.tree.select(current).scrollTo(current);
		this.fireEvent('drop', [current, target, where]);
	},
	
	onstop: function(){
		this.clean();
		$clear(this.scrolling);
	}
});

Mif.Tree.Drag.groups={};
