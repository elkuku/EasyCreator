/**
 * Plugin designed for test prupose. It add a button (that manage an alert) and a select (that allow to insert tags) in the toolbar.
 * This plugin also disable the "f" key in the editarea, and load a CSS and a JS file
 */
var EditArea_joomla= {
	/**
	 * Get called once this file is loaded (editArea still not initialized)
	 *
	 * @return nothing
	 */
	init: function(){
	//		alert("joomla init: "+ this._someInternalFunction(2, 3));
//		editArea.load_css(this.baseURL+"css/joomla.css");
//		editArea.load_script(this.baseURL+"joomla2.js");
	}

	/**
	 * Returns the HTML code for a specific control string or false if this plugin doesn't have that control.
	 * A control can be a button, select list or any other HTML item to present in the EditArea user interface.
	 * Language variables such as {$lang_somekey} will also be replaced with contents from
	 * the language packs.
	 *
	 * @param {string} ctrl_name: the name of the control to add
	 * @return HTML code for a specific control or false.
	 * @type string	or boolean
	 */
	,get_control_html: function(ctrl_name){
		switch(ctrl_name){
			case "joomla_but":
				// Control id, button img, command
				//return parent.editAreaLoader.get_button_html('joomla_but', 'joomla.gif', 'joomla_cmd', false, this.baseURL);
			case "joomla_select":
				html= "<select id='joomla_select' onchange='javascript:editArea.execCommand(\"joomla_select_change\")' fileSpecific='no'>"
					+"			<option value='-1'>{$joomla_select}</option>"
					+"			<option value='JPATH_BASE'>JPATH_BASE</option>"
					+"			<option value='JPATH_ROOT'>JPATH_ROOT</option>"
					+"			<option value='JPATH_SITE'>JPATH_SITE</option>"
					+"			<option value='JPATH_ADMINISTRATOR'>JPATH_ADMINISTRATOR</option>"
					+"			<option value='JURI::root()'>JURI::root()</option>"
					+"</select>";
				return html;
			case "joomla_trans_select":
				html= "<select id='joomla_trans_select' onchange='javascript:editArea.execCommand(\"joomla_trans_select_change\")' fileSpecific='no'>"
					+"			<option value='-1'>{$joomla_trans_select}</option>"
					+"			<option value='_'>JText::_('')</option>"
					+"			<option value='printf'>JText::printf('')</option>"
					+"			<option value='sprintf'>JText::sprintf('')</option>"
					+"</select>";
				return html;
		}
		return false;
	}
	/**
	 * Get called once EditArea is fully loaded and initialised
	 *
	 * @return nothing

	,onload: function(){
		//alert("test load");
	}
	*/
	/**
	 * Is called each time the user touch a keyboard key.
	 *
	 * @param (event) e: the keydown event
	 * @return true - pass to next handler in chain, false - stop chain execution
	 * @type boolean

	,onkeydown: function(e){
		var str= String.fromCharCode(e.keyCode);
		// desactivate the "f" character
		if(str.toLowerCase()=="f"){
//			return true;
		}
		return false;
	}
	*/
	/**
	 * Executes a specific command, this function handles plugin commands.
	 *
	 * @param {string} cmd: the name of the command being executed
	 * @param {unknown} param: the parameter of the command
	 * @return true - pass to next handler in chain, false - stop chain execution
	 * @type boolean
	 */
	,execCommand: function(cmd, param){
		// Handle commands
		switch(cmd){
			case "joomla_select_change":
				var val= document.getElementById("joomla_select").value;
				if(val!=-1)
					parent.editAreaLoader.insertTags(editArea.id, val, "");
				document.getElementById("joomla_select").options[0].selected=true;
				return false;
			case "joomla_trans_select_change":
				var val= document.getElementById("joomla_trans_select").value;
				if(val!=-1)
				parent.editAreaLoader.insertTags(editArea.id, "JText::"+val+"('", "')");
				document.getElementById("joomla_trans_select").options[0].selected=true;
				return false;
			case "joomla_cmd":
			//	alert("user clicked on joomla_cmd");
			//	return false;
		}
		// Pass to next handler in chain
		return true;
	}

	/**
	 * This is just an internal plugin method, prefix all internal methods with a _ character.
	 * The prefix is needed so they doesn't collide with future EditArea callback functions.
	 *
	 * @param {string} a Some arg1.
	 * @param {string} b Some arg2.
	 * @return Some return.
	 * @type unknown

	,_someInternalFunction : function(a, b) {
		return a+b;
	}
	*/
};

// Adds the plugin class to the list of available EditArea plugins
editArea.add_plugin("joomla", EditArea_joomla);
