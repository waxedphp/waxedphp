'use strict';

// ex) A submenu plugin that appends the contents of the input element to the editor
export default {
    // plugin name (Required)
    name: 'custom_plugin_submenu',

    // add function - It is called only once when the plugin is first run.
    // This function generates HTML to append and register the event.
    // arguments - (core : core object, targetElement : clicked button element)
    add: function (core, targetElement) {

        // Registering a namespace for caching as a plugin name in the context object
        const context = core.context;
        context.custom = {
            textElement: null
        };

        // Generate submenu HTML
        let listDiv = eval(this.setSubmenu.call(core));

        // Input tag caching
        context.custom.textElement = listDiv.getElementsByTagName('INPUT')[0];

        // You must bind "core" object when registering an event.
        /** add event listeners */
        listDiv.getElementsByTagName('BUTTON')[0].addEventListener('click', this.onClick.bind(core));
        context.custom.textElement.addEventListener('mousedown', function () {

        });

        /** append html */
        targetElement.parentNode.appendChild(listDiv);
    },

    setSubmenu: function () {
        const listDiv = this.util.createElement('DIV');

        listDiv.className = 'sun-editor-submenu layer_editor layer_align';
        listDiv.style.display = 'none';
        listDiv.innerHTML = '' +
            '<div class="inner_layer">' +
            '   <ul class="list_editor">' +
            '       <li><input type="text" placeholder="insert text" style="width: 100%; border: 1px solid #CCC;" /></li>' +
            '       <li><button type="button" class="btn_editor" title="Append text">Append text</button></li>' +
            '   </ul>' +
            '</div>';

        return listDiv;
    },

    onClick: function () {
        // Get Input value
        const value = this.util.createTextNode(this.context.custom.textElement.value);

        // insert
        this.insertNode(value);

        // focus
        this.focus();
    }
};