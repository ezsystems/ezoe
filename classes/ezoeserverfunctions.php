<?php
//
// Created on: <28-Mar-2008 00:00:00 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Online Editor extension for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2011 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*
 * Generates all i18n strings for the TinyMCE editor
 * and transforms them to the TinyMCE format for
 * translations.
 */
class ezoeServerFunctions extends ezjscServerFunctions
{
    /**
     * i18n
     * Provides all i18n strings for use by TinyMCE and other javascript dialogs.
     *
     * @param array $args
     * @param string $fileExtension
     * @return string returns json string with translation data
    */
    public static function i18n( $args, $fileExtension )
    {
        $lang = '-en';
        $locale = eZLocale::instance();
        if ( $args && $args[0] )
            $lang = $args[0];

        $i18nArray =  array( $lang => array(
            'common' => array(
                'edit_confirm' => ezpI18n::tr( 'design/standard/ezoe', "Do you want to use the WYSIWYG mode for this textarea?"),
                'apply' => ezpI18n::tr( 'design/standard/ezoe', "Apply"),
                'insert' => ezpI18n::tr( 'design/standard/ezoe', "Insert"),
                'update' => ezpI18n::tr( 'design/standard/ezoe', "Update"),
                'cancel' => ezpI18n::tr( 'design/standard/ezoe', "Cancel"),
                'close' => ezpI18n::tr( 'design/standard/ezoe', "Close"),
                'browse' => ezpI18n::tr( 'design/standard/ezoe', "Browse"),
                'class_name' => ezpI18n::tr( 'design/standard/ezoe', "Class"),
                'not_set' => ezpI18n::tr( 'design/standard/ezoe', "-- Not set --"),
                'clipboard_msg' => ezpI18n::tr( 'design/standard/ezoe', "Copy/Cut/Paste is not available in Mozilla and Firefox.\nDo you want more information about this issue?"),
                'clipboard_no_support' => ezpI18n::tr( 'design/standard/ezoe', "Currently not supported by your browser, use keyboard shortcuts instead."),
                'popup_blocked' => ezpI18n::tr( 'design/standard/ezoe', "Sorry, but we have noticed that your popup-blocker has disabled a window that provides application functionality. You will need to disable popup blocking on this site in order to fully utilize this tool."),
                'invalid_data' => ezpI18n::tr( 'design/standard/ezoe', "Error: Invalid values entered, these are marked in red."),
                'more_colors' => ezpI18n::tr( 'design/standard/ezoe', "More colors")
            ),
            'validator_dlg' => array(
                'required' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; is required and must have a value', null, array( '%label' => '<label>' )),
                'number' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid number', null, array( '%label' => '<label>' )),
                'int' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid integer number', null, array( '%label' => '<label>' )),
                'url' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid absolute url address', null, array( '%label' => '<label>' )),
                'email' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid email address', null, array( '%label' => '<label>' )),
                'size' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid css size/unit value', null, array( '%label' => '<label>' )),
                'html_id' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be a valid html element id', null, array( '%label' => '<label>' )),
                'min' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be higher then %min', null, array( '%label' => '<label>', '%min' => '<min>' )),
                'max' => ezpI18n::tr( 'design/standard/ezoe/validator', '&quot;%label&quot; must be lower then %max', null, array( '%label' => '<label>', '%max' => '<max>' )),
            ),
            'contextmenu' => array(
                'align' => ezpI18n::tr( 'design/standard/ezoe', "Alignment"),
                'left' => ezpI18n::tr( 'design/standard/ezoe', "Left"),
                'center' => ezpI18n::tr( 'design/standard/ezoe', "Center"),
                'right' => ezpI18n::tr( 'design/standard/ezoe', "Right"),
                'full' => ezpI18n::tr( 'design/standard/ezoe', "Full")
            ),
            'insertdatetime' => array(
                'date_fmt' => ezpI18n::tr( 'design/standard/ezoe', "%Y-%m-%d"),
                'time_fmt' => ezpI18n::tr( 'design/standard/ezoe', "%H:%M:%S"),
                'insertdate_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert date"),
                'inserttime_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert time"),
                'months_long' => implode(',', $locale->LongMonthNames ),
                'months_short' => implode(',', $locale->ShortMonthNames ),
                'day_long' => implode( ',', $locale->LongDayNames ) . ',' . $locale->longDayName(0),
                'day_short' => implode( ',', $locale->ShortDayNames ) . ',' . $locale->shortDayName(0)
            ),
            'print' => array(
                'print_desc' => ezpI18n::tr( 'design/standard/ezoe', "Print")
            ),
            'preview' => array(
                'preview_desc' => ezpI18n::tr( 'design/standard/ezoe', "Preview")
            ),
            'directionality' => array(
                'ltr_desc' => ezpI18n::tr( 'design/standard/ezoe', "Direction left to right"),
                'rtl_desc' => ezpI18n::tr( 'design/standard/ezoe', "Direction right to left")
            ),
            /*'layer' => array(
                'insertlayer_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert new layer"),
                'forward_desc' => ezpI18n::tr( 'design/standard/ezoe', "Move forward"),
                'backward_desc' => ezpI18n::tr( 'design/standard/ezoe', "Move backward"),
                'absolute_desc' => ezpI18n::tr( 'design/standard/ezoe', "Toggle absolute positioning"),
                'content' => ezpI18n::tr( 'design/standard/ezoe', "New layer...")
            ),*/
            'save' => array(
                'save_desc' => ezpI18n::tr( 'design/standard/ezoe', "Save"),
                'cancel_desc' => ezpI18n::tr( 'design/standard/ezoe', "Cancel all changes")
            ),
            'nonbreaking' => array(
                'nonbreaking_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert non-breaking space character")
            ),
            'iespell' => array(
                'iespell_desc' => ezpI18n::tr( 'design/standard/ezoe', "Run spell checking"),
                'download' => ezpI18n::tr( 'design/standard/ezoe', "ieSpell not detected. Do you want to install it now?")
            ),
            'advhr' => array(
                'advhr_desc' => ezpI18n::tr( 'design/standard/ezoe', "Horizontale rule")
            ),
            'emotions' => array(
                'emotions_desc' => ezpI18n::tr( 'design/standard/ezoe', "Emotions")
            ),
            'emotions_dlg' => array(
                'title' => ezpI18n::tr( 'design/standard/ezoe', "Insert emotion"),
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Emotions"),
                'cool' => ezpI18n::tr( 'design/standard/ezoe', "Cool"),
                'cry' => ezpI18n::tr( 'design/standard/ezoe', "Cry"),
                'embarassed' => ezpI18n::tr( 'design/standard/ezoe', "Embarassed"),
                'foot_in_mouth' => ezpI18n::tr( 'design/standard/ezoe', "Foot in mouth"),
                'frown' => ezpI18n::tr( 'design/standard/ezoe', "Frown"),
                'innocent' => ezpI18n::tr( 'design/standard/ezoe', "Innocent"),
                'kiss' => ezpI18n::tr( 'design/standard/ezoe', "Kiss"),
                'laughing' => ezpI18n::tr( 'design/standard/ezoe', "Laughing"),
                'money_mouth' => ezpI18n::tr( 'design/standard/ezoe', "Money mouth"),
                'sealed' => ezpI18n::tr( 'design/standard/ezoe', "Sealed"),
                'smile' => ezpI18n::tr( 'design/standard/ezoe', "Smile"),
                'surprised' => ezpI18n::tr( 'design/standard/ezoe', "Surprised"),
                'tongue_out' => ezpI18n::tr( 'design/standard/ezoe', "Tongue out"),
                'undecided' => ezpI18n::tr( 'design/standard/ezoe', "Undecided"),
                'wink' => ezpI18n::tr( 'design/standard/ezoe', "Wink"),
                'yell' => ezpI18n::tr( 'design/standard/ezoe', "Yell"),
            ),
            'searchreplace' => array(
                'search_desc' => ezpI18n::tr( 'design/standard/ezoe', "Find"),
                'replace_desc' => ezpI18n::tr( 'design/standard/ezoe', "Find/Replace")
            ),
            /*'advimage' => array(
                'image_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit image")
            ),
            'advlink' => array(
                'link_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit link")
            ),
            'xhtmlxtras' => array(
                'cite_desc' => ezpI18n::tr( 'design/standard/ezoe', "Citation"),
                'abbr_desc' => ezpI18n::tr( 'design/standard/ezoe', "Abbreviation"),
                'acronym_desc' => ezpI18n::tr( 'design/standard/ezoe', "Acronym"),
                'del_desc' => ezpI18n::tr( 'design/standard/ezoe', "Deletion"),
                'ins_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insertion"),
                'attribs_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/Edit Attributes")
            ),
            'style' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Edit CSS Style")
            ),*/
            'paste' => array(
                'paste_text_desc' => ezpI18n::tr( 'design/standard/ezoe', "Paste as Plain Text"),
                'paste_word_desc' => ezpI18n::tr( 'design/standard/ezoe', "Paste from Word"),
                'selectall_desc' => ezpI18n::tr( 'design/standard/ezoe', "Select All"),
                'plaintext_mode_sticky' => ezpI18n::tr( 'design/standard/ezoe', "Paste is now in plain text mode. Click again to toggle back to regular paste mode. After you paste something you will be returned to regular paste mode."),
                'plaintext_mode' => ezpI18n::tr( 'design/standard/ezoe', "Paste is now in plain text mode. Click again to toggle back to regular paste mode."),
            ),
            'paste_dlg' => array(
                'text_title' => ezpI18n::tr( 'design/standard/ezoe', "Use CTRL+V on your keyboard to paste the text into the window."),
                'text_linebreaks' => ezpI18n::tr( 'design/standard/ezoe', "Keep linebreaks"),
                'word_title' => ezpI18n::tr( 'design/standard/ezoe', "Use CTRL+V on your keyboard to paste the text into the window.")
            ),
            'table' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Inserts a new table"),
                'row_before_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert row before"),
                'row_after_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert row after"),
                'delete_row_desc' => ezpI18n::tr( 'design/standard/ezoe', "Delete row"),
                'col_before_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert column before"),
                'col_after_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert column after"),
                'delete_col_desc' => ezpI18n::tr( 'design/standard/ezoe', "Remove column"),
                'split_cells_desc' => ezpI18n::tr( 'design/standard/ezoe', "Split merged table cells"),
                'merge_cells_desc' => ezpI18n::tr( 'design/standard/ezoe', "Merge table cells"),
                'row_desc' => ezpI18n::tr( 'design/standard/ezoe', "Table row properties"),
                'cell_desc' => ezpI18n::tr( 'design/standard/ezoe', "Table cell properties"),
                'props_desc' => ezpI18n::tr( 'design/standard/ezoe', "Table properties"),
                'paste_row_before_desc' => ezpI18n::tr( 'design/standard/ezoe', "Paste table row before"),
                'paste_row_after_desc' => ezpI18n::tr( 'design/standard/ezoe', "Paste table row after"),
                'cut_row_desc' => ezpI18n::tr( 'design/standard/ezoe', "Cut table row"),
                'copy_row_desc' => ezpI18n::tr( 'design/standard/ezoe', "Copy table row"),
                'del' => ezpI18n::tr( 'design/standard/ezoe', "Delete table"),
                'row' => ezpI18n::tr( 'design/standard/ezoe', "Row"),
                'col' => ezpI18n::tr( 'design/standard/ezoe', "Column"),
                'rows' => ezpI18n::tr( 'design/standard/ezoe', "Rows"),
                'cols' => ezpI18n::tr( 'design/standard/ezoe', "Columns"),
                'cell' => ezpI18n::tr( 'design/standard/ezoe', "Cell")
            ),
            'autosave' => array(
                'unload_msg' => ezpI18n::tr( 'design/standard/ezoe', "The changes you made will be lost if you navigate away from this page.")
            ),
            'fullscreen' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Toggle fullscreen mode")
            ),
            'media' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert / edit embedded media"),
                'edit' => ezpI18n::tr( 'design/standard/ezoe', "Edit embedded media")
            ),
            'fullpage' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Document properties")
            ),
            'template' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert predefined template content")
            ),
            'visualchars' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Visual control characters on/off.")
            ),
            'spellchecker' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Toggle spellchecker"),
                'menu' => ezpI18n::tr( 'design/standard/ezoe', "Spellchecker settings"),
                'ignore_word' => ezpI18n::tr( 'design/standard/ezoe', "Ignore word"),
                'ignore_words' => ezpI18n::tr( 'design/standard/ezoe', "Ignore all"),
                'langs' => ezpI18n::tr( 'design/standard/ezoe', "Languages"),
                'wait' => ezpI18n::tr( 'design/standard/ezoe', "Please wait..."),
                'sug' => ezpI18n::tr( 'design/standard/ezoe', "Suggestions"),
                'no_sug' => ezpI18n::tr( 'design/standard/ezoe', "No suggestions"),
                'no_mpell' => ezpI18n::tr( 'design/standard/ezoe', "No misspellings found.")
            ),
            'pagebreak' => array(
                'desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert page break.")
            ),
            'advanced' => array(
                'style_select' => ezpI18n::tr( 'design/standard/ezoe', "Styles"),
                //'font_size' => ezpI18n::tr( 'design/standard/ezoe', "Font size"),
                //'fontdefault' => ezpI18n::tr( 'design/standard/ezoe', "Font family"),
                'block' => ezpI18n::tr( 'design/standard/ezoe', "Format"),
                'paragraph' => ezpI18n::tr( 'design/standard/ezoe', "Paragraph"),
                'div' => ezpI18n::tr( 'design/standard/ezoe', "Div"),
                //'address' => ezpI18n::tr( 'design/standard/ezoe', "Address"),
                'pre' => ezpI18n::tr( 'design/standard/ezoe', "Literal"),
                'h1' => ezpI18n::tr( 'design/standard/ezoe', "Heading 1"),
                'h2' => ezpI18n::tr( 'design/standard/ezoe', "Heading 2"),
                'h3' => ezpI18n::tr( 'design/standard/ezoe', "Heading 3"),
                'h4' => ezpI18n::tr( 'design/standard/ezoe', "Heading 4"),
                'h5' => ezpI18n::tr( 'design/standard/ezoe', "Heading 5"),
                'h6' => ezpI18n::tr( 'design/standard/ezoe', "Heading 6"),
                //'blockquote' => ezpI18n::tr( 'design/standard/ezoe', "Blockquote"),
                'code' => ezpI18n::tr( 'design/standard/ezoe', "Code"),
                'samp' => ezpI18n::tr( 'design/standard/ezoe', "Code sample"),
                'dt' => ezpI18n::tr( 'design/standard/ezoe', "Definition term"),
                'dd' => ezpI18n::tr( 'design/standard/ezoe', "Definition description"),
                'bold_desc' => ezpI18n::tr( 'design/standard/ezoe', "Bold (Ctrl+B)"),
                'italic_desc' => ezpI18n::tr( 'design/standard/ezoe', "Italic (Ctrl+I)"),
                'underline_desc' => ezpI18n::tr( 'design/standard/ezoe', "Underline (Ctrl+U)"),
                'striketrough_desc' => ezpI18n::tr( 'design/standard/ezoe', "Strikethrough"),
                'justifyleft_desc' => ezpI18n::tr( 'design/standard/ezoe', "Align left"),
                'justifycenter_desc' => ezpI18n::tr( 'design/standard/ezoe', "Align center"),
                'justifyright_desc' => ezpI18n::tr( 'design/standard/ezoe', "Align right"),
                'justifyfull_desc' => ezpI18n::tr( 'design/standard/ezoe', "Align full"),
                'bullist_desc' => ezpI18n::tr( 'design/standard/ezoe', "Unordered list"),
                'numlist_desc' => ezpI18n::tr( 'design/standard/ezoe', "Ordered list"),
                'outdent_desc' => ezpI18n::tr( 'design/standard/ezoe', "Outdent"),
                'indent_desc' => ezpI18n::tr( 'design/standard/ezoe', "Indent"),
                'undo_desc' => ezpI18n::tr( 'design/standard/ezoe', "Undo (Ctrl+Z)"),
                'redo_desc' => ezpI18n::tr( 'design/standard/ezoe', "Redo (Ctrl+Y)"),
                'link_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit link"),
                'unlink_desc' => ezpI18n::tr( 'design/standard/ezoe', "Unlink"),
                'image_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit image"),

                'object_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit object"),
                'file_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit file"),
                'custom_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert custom tag"),
                'literal_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert literal text"),
                'pagebreak_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert pagebreak"),
                'disable_desc' => ezpI18n::tr( 'design/standard/content/datatype', "Disable editor"),
                'store_desc' => ezpI18n::tr( 'design/standard/content/edit', "Store draft"),
                'publish_desc' => ezpI18n::tr( 'design/standard/content/edit', "Send for publishing"),
                'discard_desc' => ezpI18n::tr( 'design/standard/content/edit', "Discard"),

                'cleanup_desc' => ezpI18n::tr( 'design/standard/ezoe', "Cleanup messy code"),
                'code_desc' => ezpI18n::tr( 'design/standard/ezoe', "Edit HTML Source"),
                'sub_desc' => ezpI18n::tr( 'design/standard/ezoe', "Subscript"),
                'sup_desc' => ezpI18n::tr( 'design/standard/ezoe', "Superscript"),
                'hr_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert horizontal ruler"),
                'removeformat_desc' => ezpI18n::tr( 'design/standard/ezoe', "Remove formatting"),
                'custom1_desc' => ezpI18n::tr( 'design/standard/ezoe', "Your custom description here"),
                //'forecolor_desc' => ezpI18n::tr( 'design/standard/ezoe', "Select text color"),
                //'backcolor_desc' => ezpI18n::tr( 'design/standard/ezoe', "Select background color"),
                'charmap_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert special character"),
                'visualaid_desc' => ezpI18n::tr( 'design/standard/ezoe', "Toggle guidelines/invisible elements"),
                'anchor_desc' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit anchor"),
                'cut_desc' => ezpI18n::tr( 'design/standard/ezoe', "Cut"),
                'copy_desc' => ezpI18n::tr( 'design/standard/ezoe', "Copy"),
                'paste_desc' => ezpI18n::tr( 'design/standard/ezoe', "Paste"),
                'image_props_desc' => ezpI18n::tr( 'design/standard/ezoe', "Image properties"),
                'newdocument_desc' => ezpI18n::tr( 'design/standard/ezoe', "New document"),
                'help_desc' => ezpI18n::tr( 'design/standard/ezoe', "Help"),
                //'blockquote_desc' => ezpI18n::tr( 'design/standard/ezoe', "Blockquote"),
                'clipboard_msg' => ezpI18n::tr( 'design/standard/ezoe', "Copy/Cut/Paste is not available in Mozilla and Firefox.\nDo you want more information about this issue?"),
                'path' => ezpI18n::tr( 'design/standard/ezoe', "Path"),
                'newdocument' => ezpI18n::tr( 'design/standard/ezoe', "Are you sure you want clear all contents?"),
                'toolbar_focus' => ezpI18n::tr( 'design/standard/ezoe', "Jump to tool buttons - Alt+Q, Jump to editor - Alt-Z, Jump to element path - Alt-X"),
                //'more_colors' => ezpI18n::tr( 'design/standard/ezoe', "More colors"),
                'next' => ezpI18n::tr( 'design/standard/ezoe', "Next"),
                'previous' => ezpI18n::tr( 'design/standard/ezoe', "Previous"),
                'select' => ezpI18n::tr( 'design/standard/ezoe', "Select"),
                'type' => ezpI18n::tr( 'design/standard/ezoe', "Type")
            ),
            'advanced_dlg' => array(
                //'about_title' => ezpI18n::tr( 'design/standard/ezoe', "Ephox Enterprise TinyMCE"),
                'about_general' => ezpI18n::tr( 'design/standard/ezoe', "About"),
                'about_help' => ezpI18n::tr( 'design/standard/ezoe', "Help"),
                'about_license' => ezpI18n::tr( 'design/standard/ezoe', "License"),
                'about_plugins' => ezpI18n::tr( 'design/standard/ezoe', "Plugins"),
                'about_plugin' => ezpI18n::tr( 'design/standard/ezoe', "Plugin"),
                'about_author' => ezpI18n::tr( 'design/standard/ezoe', "Author"),
                'about_version' => ezpI18n::tr( 'design/standard/ezoe', "Version"),
                'about_loaded' => ezpI18n::tr( 'design/standard/ezoe', "Loaded plugins"),
                /*'anchor_title' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit anchor"),
                'anchor_name' => ezpI18n::tr( 'design/standard/ezoe', "Anchor name"),*/
                'code_title' => ezpI18n::tr( 'design/standard/ezoe', "HTML Source Editor"),
                'code_wordwrap' => ezpI18n::tr( 'design/standard/ezoe', "Word wrap"),
                'colorpicker_title' => ezpI18n::tr( 'design/standard/ezoe', "Select a color"),
                'colorpicker_picker_tab' => ezpI18n::tr( 'design/standard/ezoe', "Picker"),
                'colorpicker_picker_title' => ezpI18n::tr( 'design/standard/ezoe', "Color picker"),
                'colorpicker_palette_tab' => ezpI18n::tr( 'design/standard/ezoe', "Palette"),
                'colorpicker_palette_title' => ezpI18n::tr( 'design/standard/ezoe', "Palette colors"),
                'colorpicker_named_tab' => ezpI18n::tr( 'design/standard/ezoe', "Named"),
                'colorpicker_named_title' => ezpI18n::tr( 'design/standard/ezoe', "Named colors"),
                'colorpicker_color' => ezpI18n::tr( 'design/standard/ezoe', "Color"),
                'colorpicker_name' => ezpI18n::tr( 'design/standard/ezoe', "Name"),
                'charmap_title' => ezpI18n::tr( 'design/standard/ezoe', "Select special character")/*,
                'image_title' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit image"),
                'image_src' => ezpI18n::tr( 'design/standard/ezoe', "Image URL"),
                'image_alt' => ezpI18n::tr( 'design/standard/ezoe', "Image description"),
                'image_list' => ezpI18n::tr( 'design/standard/ezoe', "Image list"),
                'image_border' => ezpI18n::tr( 'design/standard/ezoe', "Border"),
                'image_dimensions' => ezpI18n::tr( 'design/standard/ezoe', "Dimensions"),
                'image_vspace' => ezpI18n::tr( 'design/standard/ezoe', "Vertical space"),
                'image_hspace' => ezpI18n::tr( 'design/standard/ezoe', "Horizontal space"),
                'image_align' => ezpI18n::tr( 'design/standard/ezoe', "Alignment"),
                'image_align_baseline' => ezpI18n::tr( 'design/standard/ezoe', "Baseline"),
                'image_align_top' => ezpI18n::tr( 'design/standard/ezoe', "Top"),
                'image_align_middle' => ezpI18n::tr( 'design/standard/ezoe', "Middle"),
                'image_align_bottom' => ezpI18n::tr( 'design/standard/ezoe', "Bottom"),
                'image_align_texttop' => ezpI18n::tr( 'design/standard/ezoe', "Text top"),
                'image_align_textbottom' => ezpI18n::tr( 'design/standard/ezoe', "Text bottom"),
                'image_align_left' => ezpI18n::tr( 'design/standard/ezoe', "Left"),
                'image_align_right' => ezpI18n::tr( 'design/standard/ezoe', "Right"),
                'link_title' => ezpI18n::tr( 'design/standard/ezoe', "Insert/edit link"),
                'link_url' => ezpI18n::tr( 'design/standard/ezoe', "Link URL"),
                'link_target' => ezpI18n::tr( 'design/standard/ezoe', "Target"),
                'link_target_same' => ezpI18n::tr( 'design/standard/ezoe', "Open link in the same window"),
                'link_target_blank' => ezpI18n::tr( 'design/standard/ezoe', "Open link in a new window")/*,
                'link_titlefield' => ezpI18n::tr( 'design/standard/ezoe', "Title"),
                'link_is_email' => ezpI18n::tr( 'design/standard/ezoe', "The URL you entered seems to be an email address, do you want to add the required mailto: prefix?"),
                'link_is_external' => ezpI18n::tr( 'design/standard/ezoe', "The URL you entered seems to external link, do you want to add the required http:// prefix?"),
                'link_list' => ezpI18n::tr( 'design/standard/ezoe', "Link list")*/
            ),
            'ez' => array(
                'root_node_name' => ezpI18n::tr( 'kernel/content', 'Top Level Nodes'),
                'empty_search_result' => ezpI18n::tr( 'design/standard/content/search', 'No results were found when searching for &quot;%1&quot;', null, array( '%1' => '<search_string>' )),
                'empty_bookmarks_result' => ezpI18n::tr( 'design/standard/content/view', 'You have no bookmarks')
            ),
            'searchreplace_dlg' => array(
                'searchnext_desc' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Find again"),
                'notfound' => ezpI18n::tr('design/standard/ezoe/searchreplace', "The search has been completed. The search string could not be found."),
                'search_title' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Find"),
                'replace_title' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Find/Replace"),
                'allreplaced' => ezpI18n::tr('design/standard/ezoe/searchreplace', "All occurrences of the search string were replaced."),
                'findwhat' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Find what"),
                'replacewith' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Replace with"),
                'direction' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Direction"),
                'up' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Up"),
                'down' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Down"),
                'mcase' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Match case"),
                'findnext' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Find next"),
                'replace' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Replace"),
                'replaceall' => ezpI18n::tr('design/standard/ezoe/searchreplace', "Replace all")
            ),
        ));
        $i18nString = json_encode( $i18nArray );

        return 'tinyMCE.addI18n( ' . $i18nString . ' );';
    }

    /**
     * Gets current users bookmarks by offset and limit
     *
     * @param array $args  0 => offset:0, 1 => limit:10
     * @return hash
    */
    public static function bookmarks( $args )
    {
        $offset = isset( $args[0] ) ? (int) $args[0] : 0;
        $limit  = isset( $args[1] ) ? (int) $args[1] : 10;
        $http   = eZHTTPTool::instance();
        $user   = eZUser::currentUser();
        $sort   = 'desc';

        if ( !$user instanceOf eZUser )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Bookmarks retrival', 'current user object is not of type eZUser' );
        }

        $userID = $user->attribute('contentobject_id');
        if ( $http->hasPostVariable( 'SortBy' ) && $http->postVariable( 'SortBy' ) !== 'asc' )
        {
            $sort = 'asc';
        }

        // fetch bookmarks
        $count = eZPersistentObject::count( eZContentBrowseBookmark::definition(), array( 'user_id' => $userID ) );
        if ( $count )
        {
            $objectList = eZPersistentObject::fetchObjectList( eZContentBrowseBookmark::definition(),
                                                            null,
                                                            array( 'user_id' => $userID ),
                                                            array( 'id' => $sort ),
                                                            array( 'offset' => $offset, 'length' => $limit ),
                                                            true );
        }
        else
        {
            $objectList = false;
        }

        // Simplify node list so it can be encoded
        if ( $objectList )
        {
            $list = ezjscAjaxContent::nodeEncode( $objectList, array( 'loadImages' => true, 'fetchNodeFunction' => 'fetchNode', 'fetchChildrenCount' => true ), 'raw' );
        }
        else
        {
            $list = array();
        }

        return array(
            'list' => $list,
            'count' => $count ? count( $objectList ) : 0,
            'total_count' => (int) $count,
            'offset' => $offset,
            'limit' => $limit,
        );
    }

    /**
     * Gets current users bookmarks by offset and limit
     *
     * @param array $args  0 => node id:1, 1 => offset:0, 2 => limit:10
     * @return hash
    */
    public static function browse( $args )
    {
        $nodeID = isset( $args[0] ) ? (int) $args[0] : 1;
        $offset = isset( $args[1] ) ? (int) $args[1] : 0;
        $limit  = isset( $args[2] ) ? (int) $args[2] : 10;
        $http   = eZHTTPTool::instance();

        if ( !$nodeID )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Browse node list', 'Parent node id is not valid' );
        }

        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node instanceOf eZContentObjectTreeNode )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Browse node list', "Parent node '$nodeID' is not valid" );
        }

        $params = array( 'Depth' => 1,
                'Limit'            => $limit,
                'Offset'           => $offset,
                'SortBy'           => $node->attribute( 'sort_array' ),
                'DepthOperator'    => 'eq',
                'AsObject'         => true
        );

        // Look for some (class filter and sort by) post params to use as fetch params
        if ( $http->hasPostVariable( 'ClassFilterArray' ) && $http->postVariable( 'ClassFilterArray' ) !== '' )
        {
            $params['ClassFilterType']  = 'include';
            $params['ClassFilterArray'] = $http->postVariable( 'ClassFilterArray' );
        }

        if ( $http->hasPostVariable( 'SortBy' ) && $http->postVariable( 'SortBy' ) !== '' )
        {
            $params['SortBy'] = $http->postVariable( 'SortBy' );
        }

        // fetch nodes and total node count
        $count = $node->subTreeCount( $params );
        if ( $count )
        {
            $nodeArray = $node->subTree( $params );
        }
        else
        {
            $nodeArray = false;
        }

        // generate json response from node list
        if ( $nodeArray )
        {
            $list = ezjscAjaxContent::nodeEncode( $nodeArray, array( 'fetchChildrenCount' => true, 'loadImages' => true ), 'raw' );
        }
        else
        {
            $list = array();
        }

        return array(
            'list' => $list,
            'count' => count( $nodeArray ),
            'total_count' => (int) $count,
            'node' => ezjscAjaxContent::nodeEncode( $node, array('fetchPath' => true ), 'raw' ),
            'offset' => $offset,
            'limit' => $limit,
        );
    }

    /**
     * getCacheTime
     * Expiry time for code generators registirated on this class.
     * Needs to be increased to current time when changes are done to returned translations.
     *
     * @static
     * @param string $functionName
    */
    public static function getCacheTime( $functionName )
    {
        static $mtime = null;
        if ( $mtime === null )
        {
            $mtime = filemtime( __FILE__ );
        }
        return $mtime;
    }


    /**
     * Returns global TinyMCE initialization JS code
     * @param array $args array( attribute_id, version )
     * @return string
     */
    public static function tinyMCEGlobalInit( $args )
    {
        //dummy template to use with eZURLOperator::eZDesign()
        $tpl = eZTemplate::factory();

        //Fetch object attribute from arguments
        $attribute = self::getAttributeFromArgs( $args );

        if( !$attribute || $attribute->attribute( 'data_type_string' ) !== 'ezxmltext' )
        {
            return '';
        }

        $attribute_id = $attribute->attribute( 'id' );
        $attribute_contentobject_id = $attribute->attribute( 'contentobject_id' );
        $attribute_version = $attribute->attribute( 'version' );

        // Input Handler Params
        $input_handler=$attribute->attribute('content')->attribute('input');
        $json_xml_tag_alias = $input_handler->attribute( 'json_xml_tag_alias' );


        //INI related settings
        $siteINI = eZINI::instance();
        $ezoeINI = eZINI::instance( 'ezoe.ini', '', null, false );
        $designINI = eZINI::instance( 'design.ini' );

        $ez_locale = $siteINI->variable( 'RegionalSettings', 'Locale' );
        $language = '-' . $ez_locale;

        $skin = $ezoeINI->variable( 'EditorSettings', 'Skin' );
        if( $ezoeINI->hasVariable( 'EditorSettings', 'SkinVariant' ) )
        {
            $skin_variant = $ezoeINI->variable( 'EditorSettings', 'SkinVariant' );
        }
        else
        {
            $skin_variant = '';
        }


        $plugin_list = '-' . implode( ',-', $ezoeINI->variable( 'EditorSettings', 'Plugins' ) );

        $directionality    = 'ltr';
        if ( $ezoeINI->hasVariable( 'EditorSettings', 'Directionality' ) )
        {
            $directionality = $ezoeINI->variable( 'EditorSettings', 'Directionality' );
        }

        $toolbar_alignment = 'left';
        if ( $ezoeINI->hasVariable( 'EditorSettings', 'ToolbarAlign' ) )
        {
            $toolbar_alignment = $ezoeINI->variable( 'EditorSettings', 'ToolbarAlign' );
        }

        if( $ezoeINI->variable( 'EditorSettings', 'TagPathOpenDialog' ) === 'enabled' )
        {
            $tag_path_open_dialog = 'true';
        }
        else
        {
            $tag_path_open_dialog = 'false';
        }


        //Editor CSS Files
        $editor_css_list = array( 'skins/' . $skin . '/ui.css' );
        if( $skin_variant )
        {
            $editor_css_list[] = 'skins/' . $skin . '/ui_' . $skin_variant . '.css';
        }
        $editor_css_list_string = implode( ',', ezjscPacker::buildStylesheetFiles( $editor_css_list, 3 ) );


        //Content CSS Files
        $content_css_list = array();
        $content_css_list_temp = $designINI->variable( 'StylesheetSettings', 'EditorCSSFileList' );
        foreach( $content_css_list_temp as $css )
        {
            $content_css_list[] = implode( $skin, explode( '<skin>', $css ) );
        }
        $content_css_list_string = implode( ',', ezjscPacker::buildStylesheetFiles( $content_css_list, 3 ) );

        //URLS
        $popup_css = eZURLOperator::eZDesign( $tpl, "stylesheets/skins/" . $skin . "/dialog.css", 'ezdesign' );
        $popup_css_add = eZURLOperator::eZDesign( $tpl, "stylesheets/core.css", 'ezdesign' );

        $ez_root_url = '/';
        eZURI::transformURI( $ez_root_url );

        $ezoe_url = '/ezoe/';
        eZURI::transformURI( $ezoe_url );

        $ez_js_url = '/extension/ezoe/design/standard/javascript/';
        eZURI::transformURI( $ez_js_url );

        $tiny_mce_url = eZURLOperator::eZDesign( $tpl, 'javascript/tiny_mce.js', 'ezdesign' );

        //Spell Check config
        $spell_check_rpc_url = '/ezoe/spellcheck_rpc';
        eZURI::transformURI( $spell_check_rpc_url );

        $spell_languages = '+English=en';
        if ( $attribute->attribute( 'language_code' ) === $ez_locale )
        {
            $cur_locale = eZLocale::instance();
            $langCodes = explode( '-', $cur_locale->attribute( 'http_locale_code' ) );
            $spell_languages = '+' . $cur_locale->attribute( 'intl_language_name' ) . '=' . $langCodes[0];
        }
        else
        {
            $atr_locale = eZLocale::fetchByHttpLocaleCode( $attribute->attribute( 'language_code' ) );
            $langCodes = explode( '-', $atr_locale->attribute( 'http_locale_code' ) );
            $spell_languages = '+' . $atr_locale->attribute( 'intl_language_name' ) . '=' . $langCodes[0];

            $cur_locale = eZLocale::instance();
            $langCodes = explode( '-', $cur_locale->attribute( 'http_locale_code' ) );
            $spell_languages .= ',' . $cur_locale->attribute( 'intl_language_name' ) . '=' . $langCodes[0];
        }

        $atd_rpc_url = '/ezoe/atd_rpc?url=';
        eZURI::transformURI( $atd_rpc_url );

        $atd_css_url = eZURLOperator::eZDesign( $tpl, 'javascript/plugins/AtD/css/content.css', 'ezdesign' );


        $js = <<<EOT
            var eZOeAttributeSettings, eZOeGlobalSettings = {
                mode : "none",
                theme : "ez",
                width : '100%',
                language : '{$language}',
                skin : '{$skin}',
                skin_variant : '{$skin_variant}',
                plugins : "{$plugin_list}",
                directionality : '{$directionality}',
                theme_advanced_buttons2 : "",
                theme_advanced_buttons3 : "",
                theme_advanced_blockformats : "p,pre,h1,h2,h3,h4,h5,h6",// removes address tag, not suppored by ezxml
                theme_advanced_path_location : false,// ignore, use theme_advanced_statusbar_location
                theme_advanced_statusbar_location : "bottom",// correct value set by layout code bellow pr attribute
                theme_advanced_toolbar_location : "top",// correct value set by layout code bellow pr attribute
                theme_advanced_toolbar_align : "{$toolbar_alignment}",
                theme_advanced_toolbar_floating : true,
                theme_advanced_resize_horizontal : false,
                theme_advanced_resizing : true,
                valid_elements : "-strong/-b/-bold[class|customattributes],-em/-i/-emphasize[class|customattributes],span[id|type|class|title|customattributes|align|style|view|inline|alt],sub[class|type|customattributes|align],sup[class|type|customattributes|align],u[class|type|customattributes|align],pre[class|title|customattributes],ol[class|customattributes],ul[class|customattributes],li[class|customattributes],a[href|name|target|view|title|class|id|customattributes],p[class|customattributes|align|style],img[id|type|class|title|customattributes|align|style|view|inline|alt|src],table[class|border|width|id|title|customattributes|ezborder|bordercolor|align|style],tr,th[class|width|rowspan|colspan|customattributes|align|style],td[class|width|rowspan|colspan|customattributes|align|style],div[id|type|class|title|customattributes|align|style|view|inline|alt],h1[class|customattributes|align|style],h2[class|customattributes|align|style],h3[class|customattributes|align|style],h4[class|customattributes|align|style],h5[class|customattributes|align|style],h6[class|customattributes|align|style],br",
                valid_child_elements : "a[%itrans_na],table[tr],tr[td|th],ol/ul[li],h1/h2/h3/h4/h5/h6/pre/strong/b/p/em/i/u/span/sub/sup/li[%itrans|#text]div/pre/td/th[%btrans|%itrans|#text]",
                // cleanup : false,
                // cleanup_serializer : 'xml',
                // entity_encoding : 'raw',
                entities : '160,nbsp', // We need to transform nonbreaking white space to encoded form, all other charthers as stored in raw unicode form.
                // remove_linebreaks : false,
                // apply_source_formatting : false,
                fix_list_elements : true,
                fix_table_elements : true,
                convert_urls : false,
                inline_styles : false,
                tab_focus : ':prev,:next',
                theme_ez_xml_alias_list : {$json_xml_tag_alias},
                theme_ez_editor_css : '{$editor_css_list_string}',
                theme_ez_content_css : '{$content_css_list_string}',
                theme_ez_statusbar_open_dialog : {$tag_path_open_dialog},
                popup_css : "{$popup_css}",
                //popup_css_add : "{$popup_css_add}",
                gecko_spellcheck : true,
                object_resizing : false,//disable firefox inline image/table resizing
                table_inline_editing : true, // table edit controlls in gecko
                save_enablewhendirty : true,
                ez_root_url : "{$ez_root_url}",
                ez_extension_url : "{$ezoe_url}",
                ez_js_url : "{$ez_js_url}",
                /* Used by language pack / plugin url fixer bellow, do not change */
                ez_tinymce_url : "{$tiny_mce_url}",
                ez_contentobject_id : {$attribute_contentobject_id},
                ez_contentobject_version : {$attribute_version},
                spellchecker_rpc_url : "{$spell_check_rpc_url}",
                spellchecker_languages : '{$spell_languages}',
                atd_rpc_url : "{$atd_rpc_url}",
                atd_rpc_id  : "your API key here",
                /* this list contains the categories of errors we want to show */
                atd_show_types              : "Bias Language,Cliches,Complex Expression,Diacritical Marks,Double Negatives,Hidden Verbs,Jargon Language,Passive voice,Phrases to Avoid,Redundant Expression",
                /* strings this plugin should ignore */
                atd_ignore_strings          : "AtD,rsmudge",
                /* enable "Ignore Always" menu item, uses cookies by default. Set atd_ignore_rpc_url to a URL AtD should send ignore requests to. */
                atd_ignore_enable           : "true",
                /* the URL to the button image to display */
                //atd_button_url              : "atdbuttontr.gif",
                atd_css_url : "{$atd_css_url}",
                paste_preprocess : function(pl, o) {
                    // Strip <a> HTML tags from clipboard content (Happens on Internet Explorer)
                    o.content = o.content.replace( /(\s[a-z]+=")<a\s[^>]+>([^<]+)<\/a>/gi, '$1$2' );
                }
            };


            // make sure TinyMCE doesn't try to load language pack
            // and set urls for plugins so their dialogs work correctly
            (function(){
                var uri = document.location.protocol + '//' + document.location.host + eZOeGlobalSettings.ez_tinymce_url, tps = eZOeGlobalSettings.plugins.split(','), pm = tinymce.PluginManager, tp;
                tinymce.ScriptLoader.markDone( uri.replace( 'tiny_mce', 'langs/' + eZOeGlobalSettings.language ) );
                for (var i = 0, l = tps.length; i < l; i++)
                {
                    tp = tps[i].slice(1);
                    pm.urls[ tp ] = uri.replace( 'tiny_mce.js', 'plugins/' + tp );
                }
            }())

            tinyMCE.init( eZOeGlobalSettings );

            function eZOeToggleEditor( id, settings )
            {
                var el = document.getElementById( id );
                if ( el )
                {
                    if ( tinyMCE.getInstanceById( id ) == null )
                        //tinyMCE.execCommand('mceAddControl', false, id);
                        new tinymce.Editor(id, settings).render();
                    else
                        tinyMCE.execCommand( 'mceRemoveControl', false, id );
                }
            }


EOT;
        //eZDebug::writeDebug($js, __METHOD__);

        return $js;

    }


    /**
     * Returns single attribute initialization JS code
     * @param array $args array( attribute_id, version, attribute_base )
     * @return string
     */
    public static function ezoeAttributeInit( $args )
    {
        $attribute_base = $args[2];

        //Fetch object attribute from arguments
        $attribute = self::getAttributeFromArgs( $args );

        if( !$attribute || $attribute->attribute( 'data_type_string' ) !== 'ezxmltext' )
        {
            return '';
        }

        $attribute_id = $attribute->attribute( 'id' );

        //Layout settings
        $input_handler=$attribute->attribute('content')->attribute('input');
        $layout_settings = $input_handler->attribute( 'editor_layout_settings' );
        $layout_settings_buttons = implode( ',', $layout_settings['buttons'] );
        $layout_settings_path_location = $layout_settings['path_location'];
        $layout_settings_toolbar_location = $layout_settings['toolbar_location'];


        $js = <<<EOT
			jQuery(function(){
                eZOeAttributeSettings = eZOeGlobalSettings;
                eZOeAttributeSettings['ez_attribute_id'] = {$attribute_id};
                eZOeAttributeSettings['theme_advanced_buttons1'] = "{$layout_settings_buttons}";
                eZOeAttributeSettings['theme_advanced_statusbar_location'] = "{$layout_settings_path_location}";
                eZOeAttributeSettings['theme_advanced_toolbar_location'] = "{$layout_settings_toolbar_location}";

                eZOeToggleEditor( '{$attribute_base}_data_text_{$attribute_id}', eZOeAttributeSettings );
            });
EOT;

        //eZDebug::writeDebug($js, __METHOD__);

        return $js;

    }

    /**
     * Extracts data from arguments and fetches content object attribute
     * @param array $args array( attribute_id, version[, attribute_base] )
     * @return eZContentObjectAttribute
     */
    private static function getAttributeFromArgs( $args )
    {
        $attributeID = (int)$args[0];
        $version = (int)$args[1];
        return eZContentObjectAttribute::fetch( $attributeID, $version );
    }

}

?>