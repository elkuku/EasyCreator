var excludeDirTree;

window.addEvent('domready',function(){
	excludeDirTree = new Mif.Tree({
		container: $('tree_container'),
		forest: true,
		initialize: function(){
			this.initCheckbox('simple');
			//new Mif.Tree(this);
		},
		types: {
			folder:{
				openIcon: 'mif-tree-open-icon',
				closeIcon: 'mif-tree-close-icon'
			},
			loader:{
				openIcon: 'mif-tree-loader-open-icon',
				closeIcon: 'mif-tree-loader-close-icon',
				dropDenied: ['inside','after']
			},
			disabled:{
				openIcon: 'mif-tree-open-icon',
				closeIcon: 'mif-tree-close-icon',
				dragDisabled: true,
				cls: 'disabled'
			},
			book:{
				openIcon: 'mif-tree-book-icon',
				closeIcon: 'mif-tree-book-icon',
				loadable: true
			},
			bin:{
				openIcon: 'mif-tree-bin-open-icon',
				closeIcon: 'mif-tree-bin-close-icon'
			}
		},
		dfltType:'folder',
		height: 18,
		onCheck: function(node){
			checked = this.getChecked();
			var p = '';
			var fillin = '';
			
			checked.each(function(e) {
				fillin += p+e.path;
				if( ! p) p = ',';
			});

			$('excludeDirs').set('value', fillin);
		},
		onUnCheck: function(node){
			checked = this.getChecked();
			var p = '';
			var fillin = '';
			
			checked.each(function(e) {
				fillin += p+e.path;
				if( ! p) p = ',';
			});

			$('excludeDirs').set('value', fillin);
		}
	});

	//tree.initSortable();
	var json=[	
		{
			"property": {
				"name": "root"
			},
			"children": [
				{
					"property": {
						"name": "node1"
					}
				},
				{
					"property": {
						"name": "node2",
						"hasCheckbox": false
					},
					"children":[
						{
							"property": {
								"name": "node2.1"
							},
							"state": {
								"checked": "checked"
							}
						},
						{
							"property": {
								"name": "node2.2"
							}
						}
					]
				},
				{
					"property": {
						"name": "node4"
					}
				},
				{
					"property": {
						"name": "node3",
						hasCheckbox: false
					}
				}
			]
		}
	];
	/*
	// load tree from json.
	tree.load({
		json: json
	});
	*/
});