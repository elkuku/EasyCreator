/*
---
 
name: Mif.Tree.Draw
description: convert javascript tree object to html
license: MIT-Style License (http://mifjs.net/license.txt)
copyright: Anton Samoylov (http://mifjs.net)
authors: Anton Samoylov (http://mifjs.net)
requires: Mif.Tree
provides: Mif.Tree.Draw
 
...
*/

Mif.Tree.Draw = {

	getHTML: function(node,html){
		var prefix = node.tree.DOMidPrefix;
		var checkbox;
		if($defined(node.state.checked)){
			if(!node.hasCheckbox) node.state.checked='nochecked';
			checkbox = '<span class="mif-tree-checkbox mif-tree-node-'+node.state.checked+'" uid="'+node.UID+'">'+Mif.Tree.Draw.zeroSpace+'</span>';
		}else{
			checkbox = '';
		}
		html = html||[];
		html.push(
		'<div class="mif-tree-node ',(node.isLast() ? 'mif-tree-node-last' : ''),'"'+(node.hidden ? ' style="display:none"' : '')+' id="',prefix,node.UID,'">',
			'<span class="mif-tree-node-wrapper ',node.cls,(node.state.selected ? ' mif-tree-node-selected' : ''),'" uid="',node.UID,'">',
				'<span class="mif-tree-gadjet mif-tree-gadjet-',node.getGadjetType(),'" uid="',node.UID,'">',Mif.Tree.Draw.zeroSpace,'</span>',
				checkbox,
				'<span class="mif-tree-icon ',(node.closeIconUrl?'" style="background-image: url('+node.closeIconUrl+')" ': node.closeIcon+'"'),' uid="',node.UID,'">',Mif.Tree.Draw.zeroSpace,'</span>',
				'<span class="mif-tree-name" uid="',node.UID,'">',node.name,'</span>',
			'</span>',
			'<div class="mif-tree-children" style="display:none"></div>',
		'</div>'
		);
		return html;
	},
	
	children: function(parent, container){
		parent.open = true;
		parent.$draw = true;
		var html = [];
		var children = parent.children;
		for(var i = 0, l = children.length; i < l; i++){
			this.getHTML(children[i], html);
		}
		container = container || parent.getDOM('children');
		container.set('html', html.join(''));
		parent.tree.fireEvent('drawChildren',[parent]);
	},
	
	root: function(tree){
		var domRoot = this.node(tree.root);
		domRoot.inject(tree.wrapper);
		tree.$draw = true;
		tree.fireEvent('drawRoot');
	},
	
	forestRoot: function(tree){
		var container = new Element('div').addClass('mif-tree-children-root').injectInside(tree.wrapper);
		Mif.Tree.Draw.children(tree.root, container);
	},
	
	node: function(node){
		return new Element('div').set('html', this.getHTML(node).join('')).getFirst();
	},
	
	isUpdatable: function(node){
		if(
			(!node||!node.tree) ||
			(node.getParent() && !node.getParent().$draw) || 
			(node.isRoot() && (!node.tree.$draw||node.tree.forest)) 
		) return false;
		return true;
	},
	
	update: function(node){
		if(!this.isUpdatable(node)) return null;
		if(!node.hasChildren()) node.state.open = false;
		node.getDOM('gadjet').className = 'mif-tree-gadjet mif-tree-gadjet-'+node.getGadjetType();
		if (node.closeIconUrl) {
			node.getDOM('icon').setStyle('background-image', 'url('+(node.isOpen() ? node.openIconUrl : node.closeIconUrl)+')');
		} else {
			node.getDOM('icon').className = 'mif-tree-icon '+node[node.isOpen() ? 'openIcon' : 'closeIcon'];
		}
		node.getDOM('node')[(node.isLastVisible() ?'add' : 'remove')+'Class']('mif-tree-node-last');
		if(node.$loading) return null;
		var children = node.getDOM('children');
		if(node.isOpen()){
			if(!node.$draw) Mif.Tree.Draw.children(node);
			children.style.display = 'block';
		}else{
			children.style.display = 'none';
		}
		node.tree.fireEvent('updateNode', node);
		return node;
	},
	
	inject: function(node, element){
		if(!this.isUpdatable(node)) return;
		element = element || node.getDOM('node') || this.node(node);
		var previous = node.getPrevious();
		if(previous){
			element.injectAfter(previous.getDOM('node'));
			return;
		}
		var container;
		if(node.tree.forest && node.parentNode.isRoot()){
			container = node.tree.wrapper.getElement('.mif-tree-children-root');
		}else if(node.tree.root == node){
			container = node.tree.wrapper;
		}else{
			container = node.parentNode.getDOM('children');
		}
		element.inject(container, 'top');
	}
	
};

Mif.Tree.Draw.zeroSpace = Browser.Engine.trident ? '&shy;' : (Browser.Engine.webkit ? '&#8203' : '');

