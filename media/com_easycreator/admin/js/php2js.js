/**
 * @modified by elkuku to fit EasyCreator
 * @package EasyCreator
 * @subpackage Javascript
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function str_replace(search, replace, subject, count)
{
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Gabriel Paderni
    // +   improved by: Philip Peterson
    // +   improved by: Simon Willison (http://simonwillison.net)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   bugfixed by: Anton Ongson
    // +      input by: Onno Marsman
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    tweaked by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   input by: Oleg Eremeev
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Oleg Eremeev
    // %          note 1: The count parameter must be passed as a string in order
    // %          note 1:  to find a global variable in which the result will be given
    // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // *     returns 1: 'Kevin.van.Zonneveld'
    // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
    // *     returns 2: 'hemmo, mars'

    var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = r instanceof Array, sa = s instanceof Array;
    s = [].concat(s);
    if(count)
    {
        this.window[count] = 0;
    }

    for(i = 0, sl = s.length; i < sl; i++)
    {
        if(s[i] === '')
        {
            continue;
        }

        for(j = 0, fl = f.length; j < fl; j++)
        {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if(count && s[i] !== temp)
            {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }

    return sa ? s : s[0];
}


var php2js = {
    trim:function(str, charlist)
    {
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: mdsjack (http://www.mdsjack.bo.it)
        // +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
        // +      input by: Erkekjetter
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +      input by: DxGx
        // +   improved by: Steven Levithan (http://blog.stevenlevithan.com)
        // +    tweaked by: Jack
        // +   bugfixed by: Onno Marsman
        // *     example 1: trim('    Kevin van Zonneveld    ');
        // *     returns 1: 'Kevin van Zonneveld'
        // *     example 2: trim('Hello World', 'Hdle');
        // *     returns 2: 'o Wor'
        // *     example 3: trim(16, 1);
        // *     returns 3: 6

        var whitespace, l = 0, i = 0;
        str += '';

        if(!charlist)
        {
            // default list
            whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
        } else
        {
            // preg_quote custom list
            charlist += '';
            whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
        }

        l = str.length;
        for(i = 0; i < l; i++)
        {
            if(whitespace.indexOf(str.charAt(i)) === -1)
            {
                str = str.substring(i);
                break;
            }
        }

        l = str.length;
        for(i = l - 1; i >= 0; i--)
        {
            if(whitespace.indexOf(str.charAt(i)) === -1)
            {
                str = str.substring(0, i + 1);
                break;
            }
        }

        return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
    }, //function

    sprintf:function()
    {
        // Return a formatted string
        //
        // version: 1008.1718
        // discuss at: http://phpjs.org/functions/sprintf
        // + original by: Ash Searle (http://hexmen.com/blog/)
        // + namespaced by: Michael White (http://getsprink.com)
        // + tweaked by: Jack
        // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // + input by: Paulo Freitas
        // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // + input by: Brett Zamir (http://brett-zamir.me)
        // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // * example 1: sprintf("%01.2f", 123.1);
        // * returns 1: 123.10
        // * example 2: sprintf("[%10s]", 'monkey');
        // * returns 2: '[ monkey]'
        // * example 3: sprintf("[%'#10s]", 'monkey');
        // * returns 3: '[####monkey]'
        var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
        var a = arguments, i = 0, format = a[i++];

        // pad()
        var pad = function(str, len, chr, leftJustify)
        {
            if(!chr)
            {
                chr = ' ';
            }

            var padding = (str.length >= len) ? '' : Array(
                1 + len - str.length >>> 0).join(chr);
            return leftJustify ? str + padding : padding + str;
        };

        // justify()
        var justify = function(value, prefix, leftJustify, minWidth, zeroPad, customPadChar)
        {
            var diff = minWidth - value.length;
            if(diff > 0)
            {
                if(leftJustify || !zeroPad)
                {
                    value = pad(value, minWidth, customPadChar, leftJustify);
                } else
                {
                    value = value.slice(0, prefix.length)
                        + pad('', diff, '0', true) + value.slice(prefix.length);
                }
            }

            return value;
        };

        // formatBaseX()
        var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad)
        {
            // Note: casts negative numbers to positive ones
            var number = value >>> 0;
            prefix = prefix && number && {
                '2':'0b',
                '8':'0',
                '16':'0x'
            }[base] || '';
            value = prefix + pad(number.toString(base), precision || 0, '0', false);
            return justify(value, prefix, leftJustify, minWidth, zeroPad);
        };

        // formatString()
        var formatString = function(value, leftJustify, minWidth, precision, zeroPad, customPadChar)
        {
            if(precision != null)
            {
                value = value.slice(0, precision);
            }

            return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
        };

        // doFormat()
        var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type)
        {
            var number;
            var prefix;
            var method;
            var textTransform;
            var value;

            if(substring == '%%')
            {
                return '%';
            }

            // parse flags
            var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
            var flagsl = flags.length;
            for(var j = 0; flags && j < flagsl; j++)
            {
                switch(flags.charAt(j))
                {
                    case ' ':
                        positivePrefix = ' ';
                        break;
                    case '+':
                        positivePrefix = '+';
                        break;
                    case '-':
                        leftJustify = true;
                        break;
                    case "'":
                        customPadChar = flags.charAt(j + 1);
                        break;
                    case '0':
                        zeroPad = true;
                        break;
                    case '#':
                        prefixBaseX = true;
                        break;
                }
            }

            // parameters may be null, undefined, empty-string or real valued
            // we want to ignore null, undefined and empty-string values
            if(!minWidth)
            {
                minWidth = 0;
            } else if(minWidth == '*')
            {
                minWidth = +a[i++];
            } else if(minWidth.charAt(0) == '*')
            {
                minWidth = +a[minWidth.slice(1, -1)];
            } else
            {
                minWidth = +minWidth;
            }

            // Note: undocumented perl feature:
            if(minWidth < 0)
            {
                minWidth = -minWidth;
                leftJustify = true;
            }

            if(!isFinite(minWidth))
            {
                throw new Error('sprintf: (minimum-)width must be finite');
            }

            if(!precision)
            {
                precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0
                    : undefined;
            } else if(precision == '*')
            {
                precision = +a[i++];
            } else if(precision.charAt(0) == '*')
            {
                precision = +a[precision.slice(1, -1)];
            } else
            {
                precision = +precision;
            }

            // grab value using valueIndex if required?
            value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

            switch(type)
            {
                case 's':
                    return formatString(String(value), leftJustify, minWidth,
                        precision, zeroPad, customPadChar);
                case 'c':
                    return formatString(String.fromCharCode(+value), leftJustify,
                        minWidth, precision, zeroPad);
                case 'b':
                    return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth,
                        precision, zeroPad);
                case 'o':
                    return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth,
                        precision, zeroPad);
                case 'x':
                    return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth,
                        precision, zeroPad);
                case 'X':
                    return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth,
                        precision, zeroPad).toUpperCase();
                case 'u':
                    return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth,
                        precision, zeroPad);
                case 'i':
                case 'd':
                    number = parseInt(+value, 10);
                    prefix = number < 0 ? '-' : positivePrefix;
                    value = prefix
                        + pad(String(Math.abs(number)), precision, '0', false);
                    return justify(value, prefix, leftJustify, minWidth, zeroPad);
                case 'e':
                case 'E':
                case 'f':
                case 'F':
                case 'g':
                case 'G':
                    number = +value;
                    prefix = number < 0 ? '-' : positivePrefix;
                    method = [ 'toExponential', 'toFixed', 'toPrecision' ]['efg'
                        .indexOf(type.toLowerCase())];
                    textTransform = [ 'toString', 'toUpperCase' ]['eEfFgG'
                        .indexOf(type) % 2];
                    value = prefix + Math.abs(number)[method](precision);
                    return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]
                        ();
                default:
                    return substring;
            }
        };

        return format.replace(regex, doFormat);
    }
};
