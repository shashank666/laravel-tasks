/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	//config.language = 'en';
	config.uiColor = '#FFFFFF';
	config.removeButtons = 'Underline,Subscript,Superscript';
	config.allowedContent = true;
	config.extraAllowedContent = 'script;*(*);*{*}';
    //config.toolbarLocation = 'bottom';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	config.removePlugins = 'elementspath';
};
