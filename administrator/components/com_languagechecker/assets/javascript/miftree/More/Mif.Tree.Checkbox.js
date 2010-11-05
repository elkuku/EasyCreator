/*
---
 
name: Mif.Tree.Checkbox
description: Mif.Tree.Checkbox
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.Checkbox
 
...
*/

Mif.Tree.implement({

	initCheckbox: function(type){
		this.checkboxType = type || 'simple';
		this.dfltState.checked = 'unchecked';
		this.defaults.hasCheckbox = true;
		this.wrapper.addEvent('click', this.checkboxClick.bind(this));
		if(this.checkboxType == 'simple') return;
		this.addEvent('loadChildren', function(node){
			if(!node) return;
			if(node.state.checked == 'checked'){
				node.recursive(function(){
					this.state.checked = 'checked';
				});
			}else{
				node.getFirst().setParentCheckbox(1);
			}
		});

	},
	
	checkboxClick: function(event){
		if(this.mouse.target!='checkbox') {return;}
		this.mouse.node['switch']();
	},
	
	getChecked: function(includePartially){
		var checked=[];
		this.root.recursive(function(){
			var condition = includePartially ? this.state.checked!=='unchecked' : this.state.checked=='checked';
			if(this.hasCheckbox && condition) checked.push(this);
		});
		return checked;
	}

});

Mif.Tree.Node.implement({

	'switch' : function(state){
		if(this.state.checked == state || !this.hasCheckbox) return this;
		var type = this.tree.checkboxType;
		var checked=(this.state.checked == 'checked') ? 'unchecked' : 'checked';
		if(type == 'simple'){
			this.setCheckboxState(checked);
			this.tree.fireEvent(checked == 'checked' ? 'check' : 'unCheck', this);
			this.tree.fireEvent('switch', [this, (checked == 'checked' ? true : false)]);
			return this;
		};
		this.recursive(function(){
			this.setCheckboxState(checked);
		});
		this.setParentCheckbox();
		this.tree.fireEvent(checked == 'checked' ? 'check' : 'unCheck', this);
		this.tree.fireEvent('switch', [this, (checked == 'checked' ? true : false)]);
		return this;
	},
	
	setCheckboxState: function(state){
		if(!this.hasCheckbox) return;
		var oldState = this.state.checked;
		this.state.checked = state;
		if((!this.parentNode&&this.tree.$draw) || (this.parentNode && this.parentNode.$draw)){
			this.getDOM('checkbox').removeClass('mif-tree-node-'+oldState).addClass('mif-tree-node-'+state);
		}
	},
	
	setParentCheckbox: function(s){
		if(!this.hasCheckbox || !this.parentNode || (this.tree.forest && !this.parentNode.parentNode)) return;
		var parent = this.parentNode;
		var state = '';
		var children = parent.children;
		for(var i = children.length; i--; i>0){
			var child = children[i];
			if(!child.hasCheckbox) continue;
			var childState = child.state.checked;
			if(childState == 'partially'){
				state = 'partially';
				break;
			}else if(childState == 'checked'){
				if(state == 'unchecked'){
					state = 'partially';
					break;
				}
				state = 'checked';
			}else{
				if(state == 'checked'){
					state = 'partially';
					break;
				}else{
					state = 'unchecked';
				}
			}
		}
		if(parent.state.checked == state || (s && state == 'partially' && parent.state.checked == 'checked')){return;};
		parent.setCheckboxState(state);
		parent.setParentCheckbox(s);
	}

});
