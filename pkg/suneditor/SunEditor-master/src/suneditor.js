/*
 * wysiwyg web editor
 *
 * suneditor.js
 * Copyright 2017 JiHong Lee.
 * MIT license.
 */
'use strict';

import util from './lib/util';
import core from './lib/core';
import _Constructor from './lib/constructor';
import _Context from './lib/context';
import _defaultLang from './lang/en';


export default {
    /**
     * @description Returns the create function with preset options.
     * If the options overlap, the options of the 'create' function take precedence.
     * @param {Json} options - Initialization options
     * @returns {function}
     */
    init: function (init_options) {
        const self = this;

        return {
            create: function (idOrElement, options) {
                return self.create(idOrElement, options, init_options);
            }
        };
    },

    /**
     * @description Create the suneditor
     * @param {String|Element} idOrElement - textarea Id or textarea element
     * @param {Json} options - user options
     * @returns {{save: save, getContext: getContext, getContent: getContent, setContent: setContent, appendContent: appendContent, disabled: disabled, enabled: enabled, show: show, hide: hide, destroy: destroy}}
     */
    create: function (idOrElement, options, _init_options) {
        if (typeof options !== 'object') options = {};
        if (_init_options) {
            // options = Object.assign(util.copyObj(_init_options), options);
            options =  [util.copyObj(_init_options), options].reduce(function (init, option) {
                            Object.keys(option).forEach(function (key) {
                                init[key] = option[key];
                            });
                            return init;
                        }, {});
        }
        
        const element = typeof idOrElement === 'string' ? document.getElementById(idOrElement) : idOrElement;

        if (!element) {
            if (typeof idOrElement === 'string') {
                throw Error('[SUNEDITOR.create.fail] The element for that id was not found (ID:"' + idOrElement + '")');
            }

            throw Error('[SUNEDITOR.create.fail] suneditor requires textarea\'s element or id value');
        }

        const cons = _Constructor.init(element, options, (options.lang ||  _defaultLang), options.plugins);

        if (cons.constructed._top.id && document.getElementById(cons.constructed._top.id)) {
            throw Error('[SUNEDITOR.create.fail] The ID of the suneditor you are trying to create already exists (ID:"' + cons.constructed._top.id + '")');
        }

        element.style.display = 'none';
        cons.constructed._top.style.display = 'block';

        /** Create to sibling node */
        if (typeof element.nextElementSibling === 'object') {
            element.parentNode.insertBefore(cons.constructed._top, element.nextElementSibling);
        } else {
            element.parentNode.appendChild(cons.constructed._top);
        }

        return core(_Context(element, cons.constructed, cons.options), cons.plugins, cons.options.lang);
    }
};
