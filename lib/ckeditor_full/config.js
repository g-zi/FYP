/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

//	config.skin = 'atlas';
//	config.skin = 'bootstrapck';
//	config.skin = 'kama';
//	config.skin = 'moono';
//	config.skin = 'moono-dark';
//	config.skin = 'moonocolor';
//	config.skin = 'office2013';

	config.fontSize_sizes = '8pt/8px; 10pt/10px; 12pt/12px; 14pt/14px; 18pt/18px; 22pt/22px; 28pt/28px; 36pt/36px;';

	config.font_names = 
	    'Times/Times New Roman, Times, freeserif;' +
		'Arial/Arial, Helvetica, freesans;' +
	    'Arial Black/Arial Black, DejaVu Sans Mono Bold;' +
	    'Arial Narrow Bold/Impact, DejaVu Sans Condensed Bold;' +
	    'din1451 DB/din1451,Trebuchet MS, Arial;' +
	    'Courier/Courier, New Courier, Courier New, freemono;' +
	    'Comic/Comic Sans MS, cursive;' +
	    'Trebuchet/Trebuchet MS;' +
	    'Calibri/Calibri, Verdana, Geneva, sans-serif;' + 
	    'Lucida/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
	    'Georgia/Georgia, serif;' 

	config.format_tags = 'p;h1;h2;h3;h4;h5;h6';

	config.toolbar = [
			{ items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ items: [ 'NumberedList', 'BulletedList', 'Outdent', 'Indent' ] },
			{ items: [ 'HorizontalRule', 'Table', 'PageBreak' ] },
			{ items: [ 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor' ] },
			{ items: [ 'Link', 'Unlink', 'Image' ] },
//			{ items: [ 'Sourcedialog' ] },
			{ items: [ 'Save' ] } ];

	config.colorButton_colorsPerRow = '8';

// excel colors with slight modifications
	config.colorButton_colors =
		'000,960,440,040,044,009,339,444,' +
		'A00,F60,880,080,088,00F,66C,777,' +
		'F00,F90,9C0,396,3CC,36F,707,AAA,' +
		'F0F,FC0,FF0,0F0,0FF,0CF,B5B,DDD,' +
		'FAC,FDA,FF9,CFC,CFF,9CF,F9F,FFF';

};
