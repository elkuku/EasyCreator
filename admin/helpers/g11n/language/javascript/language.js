/**
 * @version SVN: $Id: language.js 269 2010-12-11 23:45:15Z elkuku $
 */

/**
 * JavaScript g11n object. This has been "stolen" from the Joomla! 1.6 code
 * base - to improve it =;)
 * 
 * Custom behavior for JavaScript I18N in Joomla! 1.6
 * 
 * Allows you to call Joomla.JText._() to get a translated JavaScript string
 * pushed in with JText::script() in Joomla.
 */
(function() {
	/**
	 * 
	 */
	g11n = {
		strings : {},
		stringsPlural : {},
		legacy : '',
		debug : '',

		pluralFunction : '',

		/**
		 * 
		 */
		loadLanguageStrings : function(object) {
			for ( var key in object) {
				this.strings[key] = object[key];
			}
		},

		/**
		 * 
		 */
		loadPluralStrings : function(object) {
			for ( var key in object) {
				this.stringsPlural[key] = object[key];
			}
		},

		/**
		 * 
		 */
		setPluralFunction : function(object) {
			this.pluralFunction = object;
		},

		/**
		 * 
		 */
		translate : function(string) {
			if (this.debug) {
				return this.debugTranslate(string);
			}

			test = phpjs.md5(string);

			if (typeof this.strings[test] !== 'undefined') {
				return phpjs.base64_decode(this.strings[test]);
			}

			test = phpjs.md5(string.toUpperCase());

			if (this.legacy == 'mixed' || !this.legacy) {
				if (typeof this.strings[test] !== 'undefined') {
					return phpjs.base64_decode(this.strings[test]);
				}
			}

			return string;
		},

		/**
		 * 
		 */
		translatePlural : function(singular, plural, count) {
			key = phpjs.md5(singular);

			index = phpjs.call_user_func(this.pluralFunction, count);

			if (typeof this.stringsPlural[key] !== 'undefined') {
				if (typeof this.stringsPlural[key][index] !== 'undefined') {
					return phpjs.base64_decode(this.stringsPlural[key][index]);
				}
			}

			// -- Fallback - english: singular == 1
			return (count == 1) ? singular : plural;
		},

		/**
		 * 
		 */
		debugTranslate : function(string) {
			//test = selfhtml.MD5(string);
			// console.log(test);
			// console.log(phpjs.md5(string));

			if (typeof this.strings[test] !== 'undefined') {
				var msg = phpjs.sprintf('Translated:\nO: %s\nT: %s', string,
						phpjs.base64_decode(this.strings[test]));
				var add = this.log(msg);

				return phpjs.sprintf(add, phpjs
						.base64_decode(this.strings[test]));
			}

			if (this.legacy == 'mixed' || !this.legacy) {
				test = selfhtml.MD5(string.toUpperCase());
				if (typeof this.strings[test] !== 'undefined') {
					var msg = phpjs.sprintf('Legacy:\nO: %s\nT: %s', string,
							phpjs.base64_decode(this.strings[test]));
					var add = this.log(msg, 'info');

					return phpjs.sprintf(add, phpjs
							.base64_decode(this.strings[test]));
				}
			}

			var add = this.log(phpjs.sprintf('Untranslated:\nO: %s', string),
					'warn');
			this.log('', 'trace');

			return phpjs.sprintf(add, string);
		},

		/**
		 * 
		 */
		log : function(string, type) {

			var add = '%s';

			switch (type) {
			case 'info':// legacy
				add = 'L-%s-L';
				if (typeof (console) != 'undefined')
					console.info(string);
				break;// untranslated
			case 'warn':
				add = '¿-%s-¿';
				if (typeof (console) != 'undefined') {
					console.warn(string);
					console.log(phpjs.md5(string));
				}
				break;
			case 'trace':
				if (typeof (console) != 'undefined')
					console.trace('Trace');
				break;
			case undefined:// translated/other
			default:
				add = '+-%s-+';
				if (typeof (console) != 'undefined')
					console.log(string);
				break;

			}

			return add;
		}
	}// g11n

})();
