{default input_handler=$attribute.content.input
         attribute_base='ContentObjectAttribute'
         editorRow=10}

{if gt($attribute.contentclass_attribute.data_int1,10)}
    {set editorRow=$attribute.contentclass_attribute.data_int1}
{/if}

{if $input_handler.is_editor_enabled}
<!-- Start editor -->
    {run-once}
    <!-- Load TinyMCE code -->
    <script type="text/javascript" src={"javascript/tiny_mce.js"|ezdesign}></script>
    <!-- Init TinyMCE script -->
    <script type="text/javascript">
    <!--
    
    var eZtinyMceRootURL = {concat('/eztinymce/insertimage/', $attribute.contentobject_id, '/', $attribute.version  )|ezurl}, eztinyIdString;
    
    {literal}
    
    //ezTinyMCE scripts:
    tinyMCE.init({
    	mode : "none",
    	theme : "advanced",
    	plugins : "table,insertdatetime,paste,ezimage,fullscreen,devkit,",
    	theme_advanced_buttons1 : "formatselect,|,bold,italic,|,insertdate,inserttime,|,bullist,numlist,|,undo,redo,|,link,unlink,|,image,|,table,delete_table,|,cell_props,delete_col,col_before,col_after,|,delete_row,row_before,row_after,|,split_cells,merge_cells,|,fullscreen",
    	theme_advanced_buttons2 : "",
    	theme_advanced_buttons3 : "",
    	theme_advanced_path_location : "bottom",
    	theme_advanced_toolbar_location : "top",
    	theme_advanced_resize_horizontal : false,
    	theme_advanced_resizing : true,
    	valid_elements: "-strong/-b/-bold[class],-em/-i/-emphasize[class],literal[class],ol[class],ul[class],li,a/link[href|target=_blank|title|class|id],p/paragraph[class],anchor[name],img[src|class|alt|align|inline|id|customattributes],table[class|border|width|id|title|customattributes|ezborder|bordercolor],tr,th[class|width|rowspan|colspan],td[class|width|rowspan|colspan],h1,h2,h3,h4,h5,h6",
    	valid_child_elements: "h1/h2/h3/h4/h5/h6/a/link[%itrans_na],table[tr],tr[td|th],strong/b/p/div/em/i/td/th[%itrans|#text]",
    	cleanup : true,
    	cleanup_serializer : 'xml',	
    	entity_encoding : 'raw',
    	remove_linebreaks : false,
    	apply_source_formatting : false,
    	fix_list_elements : true,
    	fix_table_elements : true,
    	file_browser_callback : "ezMceFileBrowser",
    	urlconverter_callback : "ezMceURLConverter",
    	save_callback : "ezMceSave"
    });
    	
    function ezMceInit(html)
    {
    	return html;
    }
    
    function ezMceSave(element_id, html, body)
    {
    	return html;
    }
    
    function ezMceURLConverter(url, node, on_save)
    {
    	//Use the standard tinyMce url converter for now.
    	url = tinyMCE.convertURL(url, node, on_save);
    	return url;
    }
    
    function ezMceToggleEditor(id)
    {
        var el = document.getElementById(id);
    	if (el){
    		if (tinyMCE.getInstanceById(id) == null){
    			el.value = ezMceInit(el.value);
    			tinyMCE.execCommand('mceAddControl', false, id);
    		} else { 
    			tinyMCE.execCommand('mceRemoveControl', false, id);
    			el.value = ezMceSave(id, el.value);
    		}
    	}
    }
    
    
    
    function ezMceFileBrowser (field_name, url, type, win)
    {
    
        alert("Field_Name: " + field_name + "\nURL: " + url + "\nType: " + type + "\nWin: " + win); // debug/testing
        return false;
    
        // newer writing style of the TinyMCE developers for tinyMCE.openWindow
    
        tinyMCE.openWindow({
            file : eZtinyMceRootURL,
            title : "File Browser",
            width : 420,
            height : 440,
            close_previous : "no"
        }, {
            window : win,
            input : field_name,
            resizable : "yes",
            inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
            editor_id : tinyMCE.getWindowArg("editor_id")
        });
        return false;
    }
    
    {/literal}
    
    //-->
    </script>
    {/run-once}
    
    <div class="oe-window">
        <textarea class="box" id="{$attribute_base}_data_text_{$attribute.id}" name="{$attribute_base}_data_text_{$attribute.id}" cols="88" rows="{$editorRow}">{$input_handler.input_xml}</textarea>
    </div>
    
    <div class="block">
        <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_disable_editor]" value="{'Disable editor'|i18n('design/standard/content/datatype')}" />
        <script type="text/javascript">
        <!--
        
        eztinyIdString = '{$attribute_base}_data_text_{$attribute.id}';
        document.write(' &nbsp; <a href="JavaScript:ezMceToggleEditor(\'' + eztinyIdString + '\');">Toggle editor<\/a>');
        
        // comment out this if you don't want the editor to toggle on by default
        //ezMceToggleEditor( eztinyIdString );
        
        -->
        </script>
    </div>
<!-- End editor -->
{else}
    {let aliased_handler=$input_handler.aliased_handler}
    {include uri=concat("design:content/datatype/edit/",$aliased_handler.edit_template_name,".tpl") input_handler=$aliased_handler}
    <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_enable_editor]" value="{'Enable editor'|i18n('design/standard/content/datatype')}" /><br />
    {/let}
{/if}
{/default}
