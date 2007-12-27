<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Embed Content</title>
    <script language="javascript" type="text/javascript" src={"javascript/tiny_mce_popup.js"|ezdesign}></script>
    <script language="javascript" type="text/javascript" src={"javascript/ez_core.js"|ezdesign}></script>
    <script type="text/javascript">
    <!--
var eZPublishRoot = {'/'|ezroot}, ezajaxSearchUrl = {"eztinymce/search"|ezurl};
    
{literal} 

var preloadImg = null, 	imageAtr = ['src', 'mce_src', 'alt', 'border', 'title', 'width', 'height', 'id', 'class', 'align', 'longdesc'];;
var orgImageWidth, orgImageHeight, currentSelectedEmbed = false, embedObjectList = [], ezajaxSearchObject;

// Initialize
tinyMCE.setWindowArg('mce_windowresize', false);


function convertURL(url, node, on_save) {
	return eval("tinyMCEPopup.windowOpener." + tinyMCE.settings['urlconverter_callback'] + "(url, node, on_save);");
}


function setAttrib(elm, attrib, value) {
	/*
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib], o, u = '';

	if (value === undefined || value == null) {
		value = "";
		if (valueElm) value = valueElm.value;
	}
	*/

	if (value != "")
		elm.setAttribute(attrib, value);
	else if ( attrib == 'class' )
        elm.className = '';
    else
		elm.removeAttribute(attrib);
}

function makeAttrib(attrib, value, preserve)
{
	if ( !preserve )
	{
		value = value.replace(/&/g, '&amp;');
		value = value.replace(/\"/g, '&quot;');
		value = value.replace(/</g, '&lt;');
		value = value.replace(/>/g, '&gt;');
	}
	return ' ' + attrib + '="' + value + '"';
}

function insertAction() {
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();
	var formObj = document.forms[0];

	if (elm != null && elm.nodeName == "IMG") {

		setAttrib(elm, 'src', convertURL(src, tinyMCE.imgElement));
		setAttrib(elm, 'mce_src', src);
		setAttrib(elm, 'alt');
		setAttrib(elm, 'title');
		setAttrib(elm, 'border');
		setAttrib(elm, 'vspace');
		setAttrib(elm, 'hspace');
		setAttrib(elm, 'width');
		setAttrib(elm, 'height');
		setAttrib(elm, 'onmouseover', onmouseoversrc);
		setAttrib(elm, 'onmouseout', onmouseoutsrc);
		setAttrib(elm, 'id');
		setAttrib(elm, 'dir');
		setAttrib(elm, 'lang');
		setAttrib(elm, 'longdesc');
		setAttrib(elm, 'usemap');
		setAttrib(elm, 'style');
		setAttrib(elm, 'class', getSelectValue(formObj, 'classlist'));
		setAttrib(elm, 'align', getSelectValue(formObj, 'align'));


		// Repaint if dimensions changed
		if (formObj.width.value != orgImageWidth || formObj.height.value != orgImageHeight)
			inst.repaint();

		// Refresh in old MSIE
		if (tinyMCE.isMSIE5)
			elm.outerHTML = elm.outerHTML;
	} else {
	
        var obj = embedObjectList[0], size = 'small', selectedSize = ez.$('embedsizelistsrc').postData(true);
		var html = "<img", sizeObj = obj['data_map']['image']['content'][size];
		
		// TODO: if selectedSize is not loaded, preload it and change src string!!!

		html += makeAttrib('src', eZPublishRoot + sizeObj['url'], true);   //convertURL(src, tinyMCE.imgElement));
		html += makeAttrib('mce_src', eZPublishRoot + sizeObj['url'], true); 
		html += makeAttrib('alt', selectedSize);
		html += makeAttrib('title', sizeObj['alternative_text']);
		html += makeAttrib('border', 0, true);
		html += makeAttrib('id', 'eZObject_' + obj['contentobject_id']);
		html += makeAttrib('align', ez.$('embedalignlistsrc').postData(true) );
		html += makeAttrib('inline', (ez.$('embedinlinesrc').el.checked ? 'true' : 'false') );
		html += " />";

		tinyMCEPopup.execCommand("mceInsertContent", false, html);
	}

	tinyMCE._setEventsEnabled(inst.getBody(), false);
	tinyMCEPopup.close();
	return false;
}

function cancelAction() {
	tinyMCEPopup.close();
}

function init2()
{
  if ( !currentSelectedEmbed )
  {
      // todo: use animation
      var editForm = ez.$('EditForm');
      editForm.el.style.display = "none";
  }
}

function preUpload( t )
{
  t.action = window.location;
  return true;
}

function postUpload( obj, searchId )
{
  if ( ez.val( searchId ) ) obj = ezajaxSearchObject['list'][ searchId ];
  var div = ez.$('embed_preview');
  var img = new Image, attribObj = obj['data_map']['image']['content'];
  img.src = eZPublishRoot + attribObj['small']['url'];
  img.alt = obj['name'];
  div.el.appendChild( img );
  
  var editForm = ez.$('EditForm');
  editForm.el.style.display = "";
  
  obj['domNode'] = img;
  
  embedObjectList.push( obj );
  
  var select = ez.$('embedsizelistsrc'), opt;
  for (size in attribObj)
  {
     opt = new Option;
     opt.innerHTML = size;
     opt.value = size;
     select.el.appendChild( opt );
  }
}

function ezajaxSearchEnter( e, isButton )
{
    if ( isButton ) return ezajaxSearchPost();
    e = e || window.event;
    key = e.which || e.keyCode;
    if ( key == 13) return ezajaxSearchPost();
    return true;
}

function ezajaxSearchPost()
{
    var val = ez.string.trim( document.getElementById('SearchText').value );    
    var url = ezajaxSearchUrl + '/' + val + '/0/10/ezajaxSearchObject/search.js';
    if ( val ) ez.script( url, ezajaxSearchPostBack);
    return false;
}

function ezajaxSearchPostBack()
{
    if ( !ezajaxSearchObject ) return false;
    var div = ez.$('search_box_prev'), html, arr = ezajaxSearchObject['list'], img;
    div.el.innerHTML = "";
    for(var i = 0, l = arr.length; i<l; i++ )
    {
        html = '<br /><a href="JavaScript:postUpload(0,' + i + ');">' + arr[i].name;
        html += ' &nbsp; [ ' + arr[i].class_identifier + ' ]';
        if ( arr[i]['data_map'] && arr[i]['data_map']['image']  )
        {
            img = arr[i]['data_map']['image']['content']['small'];
            //TODO: use a image not found image on images that return null (original image is missing)
            if (img) html += '<img src="' + eZPublishRoot + img['url'] + '" alt="' + arr[i].name +'" \/>';
        }
        html += '<\/a>';
        div.el.innerHTML += html;
    }
}
    -->
    </script>
<style>
<!--
div#search_box_prev
{
    position: relative;
}
div#search_box_prev a img
{
    display: none;
}

div#search_box_prev a:hover img
{
    display: block;
    position: absolute;
    top: -10px;
    right: 100px;
    border: 1px solid #cdcdcd;
}
-->
</style>
{/literal}
    <base target="_self" />
	<!-- link rel="stylesheet" type="text/css" media="all" href="/css/base.css" / -->
</head>
<body id="advimage" onload="tinyMCEPopup.executeOnLoad('init2();');" style="overflow: hidden; width: 480px">
<div style="width: 990px">
    <form onsubmit="return insertAction( this );" action="JavaScript:void(0)" method="POST" name="EditForm" id="EditForm" enctype="multipart/form-data"
    style="float:left; width: 470px; height: 400px; margin-right: 10px;">
    
		<fieldset id="edit_box" style="background-color: white;">
				<legend>Properties</legend>

				<table class="properties">
					<tr id="embedsizelist">
						<td class="column1"><label for="embedsizelistsrc">Size</label></td>
						<td colspan="2" id="embedsizelistsrccontainer">
						  <select name="embedsizelistsrc" id="embedsizelistsrc">
                          </select>
                        </td>
					</tr>
					<tr id="embedalignlist">
						<td class="column1"><label for="embedalignlistsrc">Alingment</label></td>
						<td colspan="2" id="embedalignlistsrccontainer">
						  <select name="embedalignlistsrc" id="embedalignlistsrc">
						    <option value="left">Left</option>
   						    <option value="middle">Center</option>
						    <option value="right">Right</option>
                          </select>
                        </td>
					</tr>
					<tr id="embedinlinelist">
						<td class="column1"><label for="embedinlinesrc">Inline</label></td>
						<td colspan="2" id="embedinlinelistsrccontainer">
						  <input type="checkbox" id="embedinlinesrc" value="true" />
                        </td>
					</tr>
					<tr> 
						<td colspan="3"><input id="SaveButton" name="SaveButton" type="submit" value="Save" /></td> 
						<!-- todo: go back button -->
					</tr>
				</table>
		</fieldset>
		
		<fieldset id="preview_box" style="background-color: white;">
			<legend>Preview</legend>
			<div id="embed_preview" style="text-aling: center;">
			</div>
	    </fieldset>
    
    
    </form>
    <form onsubmit="return preUpload( this );" action={"/"|ezurl} method="POST" target="embed_upload" name="EmbedForm" id="EmbedForm" enctype="multipart/form-data"
    style="float:left; width: 470px; height: 400px">

		<fieldset id="upload_box" style="background-color: white;">
				<legend>Upload</legend>

				<table class="properties">
					<tr>
						<td class="column1"><label id="srclabel" for="src">Browse</label></td>
						<td colspan="2"><table border="0" cellspacing="0" cellpadding="0">
							<tr> 
							  <td colspan="2"><input name="fileName" type="file" id="fileName" value="" /></td> 
							</tr>
						  </table></td>
					</tr>
					<tr id="embedlistsrcrow">
						<td class="column1"><label for="location">Placement</label></td>
						<td colspan="2" id="embedlistsrccontainer">
						  <select name="location" id="location">
						    <option value="auto">Auto</option>

						    {if $object.published}
						     <option value="{$object.main_node_id}">{$object.name} (this)</option>
						    {/if}

						    {def $root_node_value=ezini( 'LocationSettings', 'RootNode', 'upload.ini' )
						         $root_node=cond( $root_node_value|is_numeric, fetch( content, node, hash( node_id, $root_node_value ) ),
						                         fetch( content, node, hash( node_path, $root_node_value ) ) )
							     $selection_list=fetch( 'content', 'tree',
						                                 hash( 'parent_node_id', $root_node.node_id,
						                                       'class_filter_type', include,
						                                       'class_filter_array', ezini( 'LocationSettings', 'ClassList', 'upload.ini' ),
						                                       'depth', ezini( 'LocationSettings', 'MaxDepth', 'upload.ini' ),
															   'depth_operator', 'lt',
															   'load_data_map', false(),
						                                       'limit', ezini( 'LocationSettings', 'MaxItems', 'upload.ini' ) ) )}
					        {foreach $selection_list as $item}
							{if $item.can_create}
							 <option value="{$item.node_id}">{'&nbsp;'|repeat( sub( $item.depth, $root_node.depth, 1 ) )}{$item.name|wash}</option>
							{/if}
					        {/foreach}

                          </select>
                        </td>
					</tr>
					<tr> 
						<td class="column1"><label id="titlelabel" for="title">Name</label></td> 
						<td colspan="2"><input id="objectName" name="objectName" type="text" value="" /></td> 
					</tr>
					<tr> 
						<td class="column1"><label id="titlelabel" for="title">Caption</label></td> 
						<td colspan="2"><input id="objectText" name="objectText" type="text" value="" disabled="disabled" style="border-color: #ccc;" size="32" /></td> 
					</tr>
					<tr> 
						<td colspan="3"><input id="uploadButton" name="uploadButton" type="submit" value="Upload" /></td> 
					</tr>
				</table>
				<iframe id="embed_upload" name="embed_upload" style=" border: 0; width: 99%; height: 28px; margin: 0;"></iframe>
		</fieldset>

		<fieldset id="search_box" style="background-color: white;">
			<legend>Search</legend>
			<input id="SearchText" name="SearchText" type="text" value="" onkeypress="return ezajaxSearchEnter(event)" />
			<input type="submit" name="SearchButton" id="SearchButton" value="Search"  onclick="return ezajaxSearchEnter(event, true)" />
			<div id="search_box_prev"></div>
			<!-- todo: paging support -->
		</fieldset>
    
    
     </form>
</div>
</body>
</html>