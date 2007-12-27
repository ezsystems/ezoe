//ezTinyMCE scripts:
tinyMCE.init({
	mode : "none",
	theme : "advanced",
	plugins : "table,insertdatetime,paste",
	theme_advanced_buttons1 : "formatselect,separator,bold,italic,separator,insertdate,inserttime,separator,bullist,numlist,separator,undo,redo,separator,link,unlink,separator,table,delete_table,separator,cell_props,delete_col,col_before,col_after,separator,delete_row,row_before,row_after,separator,split_cells,merge_cells",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_path_location : "bottom",
	theme_advanced_toolbar_location : "top",
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : true,
	valid_elements: "-strong/-b/-bold[class],-em/-i/-emphasize[class],literal[class],ol[class],ul[class],li,a/link[href|target|title|class|id],p/paragraph[class],anchor[name],embed[href|class|view|align|target|size|id],custom[*],table[class|border|width],tr,th[class|width|rowspan|colspan],td[class|width|rowspan|colspan],h[level<1?2?3?4?5?6|class|anchor_name],header[level<1?2?3?4?5?6|class|anchor_name],h1,h2,h3,h4,h5,h6",
	valid_child_elements: "h1/h2/h3/h4/h5/h6/a/link[%itrans_na],table[tr],tr[td|th],strong/b/p/div/em/i/td/th[%itrans|#text]",
	cleanup_debug : true,
	cleanup_indent : true,
	entity_encoding : "raw",
	remove_linebreaks : false,
	apply_source_formatting : false,
	fix_list_elements : true,
	fix_table_elements : true,
	file_browser_callback : "ezMceFileBrowser",
	urlconverter_callback : "ezMceURLConverter",
	save_callback : "ezMceSave"
});
	
function ezMceInit(html){
	//replace some tags that tinyMCE don't understand (header, h and link)
	html = html.replace(/\<link(.*)<\/link/gi, "<a$1</a" );
	html = html.replace(/\<header\s+level="(\d)"(.*)<\/header/gi, "<h$1$2</h$1" );
	html = html.replace(/\<h\s+level="(\d)"(.*)<\/h/gi, "<h$1$2</h$1" );
	return html;
}


function ezMceSave(element_id, html, body) {
	// Change some html that ez don't understand on save / submit
	html = html.replace(/\<h(\d)(.*)<\/h(\d)/gi, '<header level="$1"$2</header');
	return html;
}

function ezMceFileBrowser(field_name, url, type, win) {
	// This function is fired on filebrowse
	//win.document.forms[0].elements[field_name].value = 'my browser value';
}

function ezMceURLConverter(url, node, on_save) {
	//Use the standard tinyMce url converter for now.
	url = tinyMCE.convertURL(url, node, on_save);
	return url;
}

function ezMceToggleEditor(id, id2) {
	if (id2 = document.getElementById(id)){
		if (tinyMCE.getInstanceById(id) == null){
			id2.value = ezMceInit(id2.value);
			tinyMCE.execCommand('mceAddControl', false, id);
		} else { 
			tinyMCE.execCommand('mceRemoveControl', false, id);
			id2.value = ezMceSave(id, id2.value);
		}
	}
}
	
/*
table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",

*/