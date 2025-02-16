/*
 * wysiwyg web editor
 *
 * suneditor.js
 * Copyright 2017 JiHong Lee.
 * MIT license.
 */
'use strict';

/**
 * @description utility function
 */
const util = {
    _d: document,
    _w: window,
    _onlyZeroWidthRegExp: new RegExp('^' + String.fromCharCode(8203) + '+$'),

    /**
     * @description Unicode Character 'ZERO WIDTH SPACE'
     */
    zeroWidthSpace: '\u200B',

    /**
     * @description A method that checks If the text is blank or to see if it contains only Unicode 'ZERO WIDTH SPACE' (\u200B)
     * @param {String} text - String value
     * @returns {Boolean}
     */
    onlyZeroWidthSpace: function (text) {
        return text === '' || this._onlyZeroWidthRegExp.test(text);
    },

    /**
     * @description Gets XMLHttpRequest object
     * @returns {Object}
     */
    getXMLHttpRequest: function () {
        /** IE */
        if (this._w.ActiveXObject) {
            try {
                return new ActiveXObject('Msxml2.XMLHTTP');
            } catch (e) {
                try {
                    return new ActiveXObject('Microsoft.XMLHTTP');
                } catch (e1) {
                    return null;
                }
            }
        }
        /** netscape */
        else if (this._w.XMLHttpRequest) {
            return new XMLHttpRequest();
        }
        /** fail */
        else {
            return null;
        }
    },

    /**
     * @description Copies object
     * @param {Object} obj - Object to be copy
     * @returns {Object}
     */
    copyObj: function (obj) {
        const copy = {};

        for (let attr in obj) {
            copy[attr] = obj[attr];
        }

        return copy;
    },

    /**
     * @description Create Element node
     * @param {String} elementName - Element name
     * @returns {Element}
     */
    createElement: function (elementName) {
        return this._d.createElement(elementName);
    },

    /**
     * @description Create text node
     * @param {String} text - text contents
     * @returns {Node}
     */
    createTextNode: function (text) {
        return this._d.createTextNode(text || '');
    },

    /**
     * @description Get the the tag path of the arguments value
     * If not found, return the first found value
     * @param {Array} nameArray - File name array
     * @param {String} extension - js, css
     * @returns {String}
     */
    getIncludePath: function (nameArray, extension) {
        let path = '';
        const pathList = [];
        const tagName = extension === 'js' ? 'script' : 'link';
        const src = extension === 'js' ? 'src' : 'href';
        
        let fileName = '(?:';
        for (let i = 0, len = nameArray.length; i < len; i++) {
            fileName += nameArray[i] + (i < len - 1 ? '|' : ')');
        }

        const regExp = new RegExp('(^|.*[\\\\\/])' + fileName + '(\\.[^\\\\/]+)?\.' + extension + '(?:\\?.*|;.*)?$', 'i');
        const extRegExp = new RegExp('.+\\.' + extension + '(?:\\?.*|;.*)?$', 'i');
            
        for (let c = this._d.getElementsByTagName(tagName), i = 0; i < c.length; i++) {
            if (extRegExp.test(c[i][src])) {
                pathList.push(c[i]);
            }
        }

        for (let i = 0; i < pathList.length; i++) {
            let editorTag = pathList[i][src].match(regExp);
            if (editorTag) {
                path = editorTag[0];
                break;
            }
        }

        if (path === '') path = pathList.length > 0 ? pathList[0][src] : '';

        -1 === path.indexOf(':/') && '//' !== path.slice(0, 2) && (path = 0 === path.indexOf('/') ? location.href.match(/^.*?:\/\/[^\/]*/)[0] + path : location.href.match(/^[^\?]*\/(?:)/)[0] + path);

        if (!path) throw '[SUNEDITOR.util.getIncludePath.fail] The SUNEDITOR installation path could not be automatically detected. (name: +' + name + ', extension: ' + extension + ')';

        return path;
    },

    /**
     * @description Converts contents into a format that can be placed in an editor
     * @param {String} contents - contents
     * @returns {String}
     */
    convertContentsForEditor: function (contents) {
        let tag, baseHtml, innerHTML = '';
        contents = contents.trim();

        tag = this._d.createRange().createContextualFragment(contents).childNodes;

        for (let i = 0, len = tag.length; i < len; i++) {
            baseHtml = tag[i].outerHTML || tag[i].textContent;

            if (tag[i].nodeType === 3) {
                const textArray = baseHtml.split(/\n/g);
                let text = '';
                for (let t = 0, tLen = textArray.length; t < tLen; t++) {
                    text = textArray[t].trim();
                    if (text.length > 0) innerHTML += '<P>' + text + '</p>';
                }
            } else {
                innerHTML += baseHtml;
            }
        }

        const ec = {'&': '&amp;', '\u00A0': '&nbsp;', '\'': '&quot;', '<': '&lt;', '>': '&gt;'};
        contents = contents.replace(/&|\u00A0|'|<|>/g, function (m) {
            return (typeof ec[m] === 'string') ? ec[m] : m;
        });

        if (innerHTML.length === 0) innerHTML = '<p>' + (contents.length > 0 ? contents : this.zeroWidthSpace) + '</p>';

        return innerHTML;
    },

    /**
     * @description Converts HTML string into a format that can be placed in an editor of code view mode
     * @param {String} html - HTML string
     * @returns {String}
     */
    convertHTMLForCodeView: function (html) {
        return html.replace(/\s*<(?:li|td)\s*(?:[a-z\-]+)?\s*(?:="?[^>]*"?)?\s*>/gi, this._insertIndent)
                    .replace(/<\/?(?:blockquote|hr|ol|ul|table|tbody|thead|th|tr)\s*(?:[a-z\-]+)?\s*(?:="?[^>]*"?)?\s*>(?=[^\n])/gi, this._insertLineBreak)
                    .replace(/<\/(?:p|div|h[1-6]|li|td|pre)>(?=[^\n])/gi, this._insertLineBreak);
    },

    /**
     * @description Add a line break to the open tag
     * @param {String} matchTag - matched tag string
     * @returns {String}
     */
    _insertIndent: function (matchTag) {
        return '  ' + matchTag.trim();
    },

    /**
     * @description Add a line breaks to the tags
     * @param {String} matchTag - matched tag string
     * @returns {String}
     */
    _insertLineBreak: function (matchTag) {
        return matchTag + '\n';
    },

    /**
     * @description It is judged whether it is the edit region top div element.
     * @param {Element} element - The element to check
     * @returns {Boolean}
     */
    isWysiwygDiv: function (element) {
        if (element && element.nodeType === 1 && this.hasClass(element, 'sun-editor-id-wysiwyg')) return true;
        return false;
    },

    /**
     * @description It is judged whether it is the format element (P, DIV, H1-6, LI, CODE)
     * @param {Element} element - The element to check
     * @returns {Boolean}
     */
    isFormatElement: function (element) {
        if (element && element.nodeType === 1 && /^(P|DIV|H[1-6]|LI|CODE)$/i.test(element.nodeName) && !this.isComponent(element)) return true;
        return false;
    },

    /**
     * @description It is judged whether it is the range format element. (BLOCKQUOTE, TABLE, THEAD, TBODY, TH, TR, TD, OL, UL, PRE)
     * * Range format element is wrap the format element  (P, DIV, H1-6, LI, CODE)
     * @param {Element} element - The element to check
     * @returns {Boolean}
     */
    isRangeFormatElement: function (element) {
        if (element && element.nodeType === 1 && /^(BLOCKQUOTE|TABLE|THEAD|TBODY|TH|TR|TD|OL|UL|PRE|FIGCAPTION)$/i.test(element.nodeName)) return true;
        return false;
    },

    /**
     * @description It is judged whether it is the component(img, iframe cover) element - "div.sun-editor-id-comp"
     * @param {Element} element - The element to check
     * @returns {Boolean}
     */
    isComponent: function (element) {
        return element && /sun-editor-id-comp/.test(element.className);
    },

    /**
     * @description Get format element of the argument value (P, DIV, H[1-6], LI)
     * @param {Element} element - Reference element if null or no value, it is relative to the current focus node.
     * @returns {Element}
     */
    getFormatElement: function (element) {
        if (!element) return null;

        while (element && !this.isFormatElement(element) && !this.isWysiwygDiv(element.parentNode)) {
            element = element.parentNode;
        }

        if (this.isWysiwygDiv(element) || this.isRangeFormatElement(element)) {
            const firstFormatElement = this.getListChildren(element, function (current) {
                return this.isFormatElement(current);
            }.bind(this))[0];

            return firstFormatElement;
        }

        return element;
    },

    /**
     * @description Get range format element of the argument value (blockquote, TABLE, TR, TD, OL, UL, PRE)
     * @param {Element} element - Reference element if null or no value, it is relative to the current focus node.
     * @returns {Element|null}
     */
    getRangeFormatElement: function (element) {
        if (!element) return null;

        while (element && !this.isRangeFormatElement(element) && !this.isWysiwygDiv(element)) {
            element = element.parentNode;
        }

        return this.isWysiwygDiv(element) ? null : element;
    },

    /**
     * @description Get the index of the argument value in the element array
     * @param {array} array - element array
     * @param {Element} element - Element to find index
     * @returns {Number}
     */
    getArrayIndex: function (array, element) {
        let idx = -1;
        for (let i = 0, len = array.length; i < len; i++) {
            if (array[i] === element) {
                idx = i;
                break;
            }
        }

        return idx;
    },

    /**
     * @description Get the next index of the argument value in the element array
     * @param {array} array - element array
     * @param {Element} item - Element to find index
     * @returns {Number}
     */
    nextIdx: function (array, item) {
        let idx = this.getArrayIndex(array, item);
        if (idx === -1) return -1;
        return idx + 1;
    },

    /**
     * @description Get the previous index of the argument value in the element array
     * @param {array} array - element array
     * @param {Element} item - Element to find index
     * @returns {Number}
     */
    prevIdx: function (array, item) {
        let idx = this.getArrayIndex(array, item);
        if (idx === -1) return -1;
        return idx - 1;
    },

    /**
     * @description Get index from parent
     * @param {Element} element - Element
     * @returns {Number}
     */
    getPositionIndex: function (element) {
        let idx = 0;
        while (element = element.previousSibling) {
            idx += 1;
        }
        return idx;
    },

    /**
     * @description Check the node is a table cell
     * @param {Element} node - Nodes to check
     * @returns {Boolean}
     */
    isCell: function (node) {
        return node && /^(TD|TH)$/i.test(node.nodeName);
    },

    /**
     * @description Check the node is a break node (BR)
     * @param {Element} node - Nodes to check
     * @returns {Boolean}
     */
    isBreak: function (node) {
        return node && /^BR$/i.test(node.nodeName);
    },

    /**
     * @description Get all child nodes of the argument value element (Without text node)
     * @param {Element} element - element to get child node
     * @param {(function|null)} validation - Conditional function
     * @returns {Array}
     */
    getListChildren: function (element, validation) {
        const children = [];
        if (!element || !element.children) return children;

        validation = validation || function () { return true; };

        (function recursionFunc(current) {
            if (element !== current && validation(current)) {
                children.push(current);
            }

            for (let i = 0, len = current.children.length; i < len; i++) {
                recursionFunc(current.children[i]);
            }
        })(element);

        return children;
    },

    /**
     * @description Get all child nodes of the argument value element (Include text nodes)
     * @param {Element} element - element to get child node
     * @param {(function|null)} validation - Conditional function
     * @returns {Array}
     */
    getListChildNodes: function (element, validation) {
        const children = [];
        if (!element || !element.childNodes) return children;

        validation = validation || function () { return true; };

        (function recursionFunc(current) {
            if (element !== current && validation(current)) {
                children.push(current);
            }

            for (let i = 0, len = current.childNodes.length; i < len; i++) {
                recursionFunc(current.childNodes[i]);
            }
        })(element);

        return children;
    },

    /**
     * @description Get the parent element of the argument value.
     * A tag that satisfies the query condition is imported.
     * Returns null if not found.
     * @param {Node} element - Reference element
     * @param {String|Function} query - Query String (tagName, .className, #ID, :name) or validation function.
     * Not use it like jquery.
     * Only one condition can be entered at a time.
     * @returns {Element|null}
     */
    getParentElement: function (element, query) {
        let check;

        if (typeof query === 'function') {
            check = query;
        } else {
            let attr;
            if (/\./.test(query)) {
                attr = 'className';
                query = query.split('.')[1];
            } else if (/#/.test(query)) {
                attr = 'id';
                query = '^' + query.split('#')[1] + '$';
            } else if (/:/.test(query)) {
                attr = 'name';
                query = '^' + query.split(':')[1] + '$';
            } else {
                attr = 'tagName';
                query = '^' + query + '$';
            }

            const regExp = new RegExp(query, 'i');
            check = function (el) {
                return regExp.test(el[attr]);
            };
        }

        while (element && !check(element)) {
            if (this.isWysiwygDiv(element)) {
                return null;
            }
            element = element.parentNode;
        }

        return element;
    },

    /**
     * @description Get the child element of the argument value.
     * A tag that satisfies the query condition is imported.
     * Returns null if not found.
     * @param {Node} element - Reference element
     * @param {String|Function} query - Query String (tagName, .className, #ID, :name) or validation function.
     * Not use it like jquery.
     * Only one condition can be entered at a time.
     * @returns {Element|null}
     */
    getChildElement: function (element, query) {
        let check;

        if (typeof query === 'function') {
            check = query;
        } else {
            let attr;
            if (/\./.test(query)) {
                attr = 'className';
                query = query.split('.')[1];
            } else if (/#/.test(query)) {
                attr = 'id';
                query = '^' + query.split('#')[1] + '$';
            } else if (/:/.test(query)) {
                attr = 'name';
                query = '^' + query.split(':')[1] + '$';
            } else {
                attr = 'tagName';
                query = '^' + query + '$';
            }

            const regExp = new RegExp(query, 'i');
            check = function (el) {
                return regExp.test(el[attr]);
            };
        }

        const childList = this.getListChildren(element, function (current) {
            return check(current);
        });

        return childList[0];
    },

    /**
     * @description Returns the position of the left and top of argument. {left:0, top:0}
     * @param {Element} element - Element node
     * @returns {Object}
     */
    getOffset: function (element) {
        let tableOffsetLeft = 0;
        let tableOffsetTop = 0;
        let tableElement = element.nodeType === 3 ? element.parentElement : element;

        while (!this.isWysiwygDiv(tableElement.parentNode)) {
            if (/^(A|TD|TH|FIGURE|FIGCAPTION|IMG|IFRAME)$/i.test(tableElement.nodeName) || /relative/i.test(tableElement.style.position)) {
                tableOffsetLeft += tableElement.offsetLeft;
                tableOffsetTop += tableElement.offsetTop;
            }
            tableElement = tableElement.parentNode;
        }

        return {
            left: tableOffsetLeft,
            top: tableOffsetTop - tableElement.parentNode.scrollTop
        };
    },

    /**
     * @description Set the text content value of the argument value element
     * @param {Element} element - Element to replace text content
     * @param {String} txt - Text to be applied
     */
    changeTxt: function (element, txt) {
        if (!element || !txt) return;
        element.textContent = txt;
    },

    /**
     * @description Determine whether any of the matched elements are assigned the given class
     * @param {Element} element - Elements to search class name
     * @param {String} className - Class name to search for
     * @returns {Boolean}
     */
    hasClass: function (element, className) {
        if (!element) return;

        return element.classList.contains(className.trim());
    },

    /**
     * @description Append the className value of the argument value element
     * @param {Element} element - Elements to add class name
     * @param {String} className - Class name to be add
     */
    addClass: function (element, className) {
        if (!element) return;

        const check = new RegExp('(\\s|^)' + className + '(\\s|$)');
        if (check.test(element.className)) return;

        element.className += ' ' + className;
    },

    /**
     * @description Delete the className value of the argument value element
     * @param {Element} element - Elements to remove class name
     * @param {String} className - Class name to be remove
     */
    removeClass: function (element, className) {
        if (!element) return;

        const check = new RegExp('(\\s|^)' + className + '(\\s|$)');
        element.className = element.className.replace(check, ' ').trim();
    },

    /**
     * @description Argument value If there is no class name, insert it and delete the class name if it exists
     * @param {Element} element - Elements to replace class name
     * @param {String} className - Class name to be change
     */
    toggleClass: function (element, className) {
        if (!element) return;

        const check = new RegExp('(\\s|^)' + className + '(\\s|$)');
        if (check.test(element.className)) {
            element.className = element.className.replace(check, ' ').trim();
        }
        else {
            element.className += ' ' + className;
        }
    },

    /**
     * @description Delete argumenu value element
     * @param {Element} item - Element to be remove
     */
    removeItem: function (item) {
        try {
            item.remove();
        } catch (e) {
            item.parentNode.removeChild(item);
        }
    },

    /**
     * @description Delete a empty child node of argument element
     * @param {Element} element - Element node
     */
    removeEmptyNode: function (element) {
        (function recursionFunc(current) {
            if (current.textContent.trim().length === 0 && !/^BR$/i.test(current.nodeName) && (!current.firstChild || !/^BR$/i.test(current.firstChild.nodeName))) {
                current.parentNode && current.parentNode.removeChild(current);
            } else {
                for (let i = 0, len = current.children.length; i < len; i++) {
                    if (!current.children[i]) continue;
                    recursionFunc(current.children[i]);
                }
            }
        })(element);
    },

    /**
     * @description Gets the clean HTML code for editor
     * @param {String} html - HTML string
     */
    cleanHTML: function (html) {
        const tagsAllowed = new RegExp('^(P|DIV|PRE|H1|H2|H3|H4|H5|H6|B|U|I|STRIKE|SUB|SUP|OL|UL|TABLE|BR|HR|A|IMG|IFRAME)$', 'i');
        const domTree = this._d.createRange().createContextualFragment(html).children;
        let cleanHTML = '';

        for (let i = 0, len = domTree.length; i < len; i++) {
            if (tagsAllowed.test(domTree[i].nodeName)) {
                cleanHTML += domTree[i].outerHTML.replace(/<!--(.*?)-->/g, '').replace(/<[a-zA-Z]+\:[a-zA-Z]+.*>(\n|.)*<\/[a-zA-Z]+\:[a-zA-Z]+>/g, '').replace(/\s(?:style|class|dir|xmlns|data-[a-z\-]+)\s*(?:[a-z\-]+)?\s*(?:="?[^>]*"?)?\s*/ig, '').replace(/<\/?(?:span|font)\s*(?:[a-z\-]+)?\s*(?:="?[^>]*"?)?\s*>/ig, '').replace(/<\/?[a-z]+:[a-z]+\s*(?:[a-z\-]+)?\s*(?:="?[^>]*"?)?\s*>/ig, '');
            }
        }

        return cleanHTML || html;
    }
};

export default util;