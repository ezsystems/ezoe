{set scope=global persistent_variable=hash('title', 'New %tag_name tag'|i18n('design/standard/ezoe', '', hash( '%tag_name', concat('&lt;', $tag_name_alias, '&gt;') )),
                                           'scripts', array('ezoe/ez_core.js',
                                                            'ezoe/ez_core_animation.js',
                                                            'ezoe/ez_core_accordion.js',
                                                            'ezoe/popup_utils.js'),
                                           'css', array()
                                           )}

<script type="text/javascript">
eZOEPopupUtils.embedObject = {$embed_data};
eZOEPopupUtils.settings.customAttributeStyleMap = {$custom_attribute_style_map};
eZOEPopupUtils.settings.tagEditTitleText = "{'Edit %tag_name tag'|i18n('design/standard/ezoe', '', hash( '%tag_name', concat('&lt;', $tag_name_alias, '&gt;') ))|wash('javascript')}";
var defaultEmbedSize = '{$default_size}', selectedSize = defaultEmbedSize, contentType = '{$content_type}', attachmentIcon = {"tango/mail-attachment32.png"|ezimage};
var viewListData = {$view_list}, classListData = {$class_list}, attributeDefaults = {$attribute_defaults}, selectedTagName = '', compatibilityMode = '{$compatibility_mode}';

{literal}

tinyMCEPopup.onInit.add( eZOEPopupUtils.BIND( eZOEPopupUtils.init, window, {
    tagName: 'embed',
    form: 'EditForm',
    cancelButton: 'CancelButton',
    cssClass: compatibilityMode !== 'enabled' ? 'ezoeItemNonEditable ezoeItemContentTypeFiles' : '',
    onInitDone: function( el, tag, ed )
    {        
        var selectors = ez.$('embed_size_source', 'embed_align_source', 'embed_class_source', 'embed_view_source', 'embed_inline_source');
        var tag = selectors[4].el.checked ? 'embed-inline' : 'embed', def = attributeDefaults[ tag ]
        inlineSelectorChange.call( selectors[4], false, selectors[4].el  );
        selectors[4].addEvent('click', inlineSelectorChange );
        var align = el ? el.getAttribute('align') || '' : def['align']  || '';
        if ( align === 'center' ) align = 'middle';

        jQuery('#embed_preview').addClass('object_preview float-break').css( eZOEPopupUtils.ie56 ? {'margin': '0 5px 5px 5px'} : {});
        selectors[1].el.value = align;
        selectors.callEach('addEvent', 'change', loadEmbedPreview );

        if ( el && el.nodeName !== 'IMG' )//&& el.id.split('_')[1] == eZOEPopupUtils.embedObject.id )
            jQuery('#embed_preview').html( el.innerHTML );
        else
            loadEmbedPreview();

        var slides = ez.$$('div.panel'), navigation = ez.$$('#tabs li.tab');
        slides.accordion( navigation, {duration: 100, transition: ez.fx.sinoidal, accordionAutoFocusTag: 'input[type=text]'}, {opacity: 0, display: 'none'} );
    },
    tagGenerator: function( tag, customTag )
    {
        if ( contentType === 'images' || compatibilityMode === 'enabled' )
            return '<img id="__mce_tmp" src="JavaScript:void(0);" />';
        if ( jQuery('#embed_inline_source').attr( 'checked' ) )
           return '<span id="__mce_tmp"></span>';
        return '<div id="__mce_tmp"></div>';
    },
    onTagGenerated:  function( el, ed, args )
    {
        // append a paragraph if user just inserted a embed tag in editor and it's the last tag
        if ( el.nodeName !== 'DIV' )
            return false;

        var edBody = el.parentNode, doc = ed.getDoc();
        if ( edBody.nodeName !== 'BODY' )
        {
            el = edBody;
            edBody = edBody.parentNode
        }
        if ( edBody.nodeName === 'BODY'
        && edBody.childNodes.length <= (jQuery.inArray( el, edBody.childNodes ) +1) )
        {
            var p = doc.createElement('p');
            p.innerHTML = ed.isIE ? '&nbsp;' : '<br \/>';
            edBody.appendChild( p );
        }
    },
    tagAttributeEditor: function( ed, el, args )
    {
        args['inline'] = jQuery('#embed_inline_source').attr( 'checked' ) ? 'true' : 'false';
        el = eZOEPopupUtils.switchTagTypeIfNeeded( el, (contentType === 'images' || compatibilityMode === 'enabled' ? 'img' : (args['inline'] === 'true' ? 'span' : 'div') ) );
        if ( compatibilityMode === 'enabled' )
        {
            args['src'] = attachmentIcon;
            args['width'] = args['height'] = 32;
        }
        else
        {
            if ( args['align'] === 'middle' ) args['align'] = 'center';
            ed.dom.setHTML( el, jQuery('#embed_preview').html() );
        }
        args['title']   = eZOEPopupUtils.safeHtml( eZOEPopupUtils.embedObject['name'] );
        ed.dom.setAttribs( el, args );
        return el;
    }
}));


function inlineSelectorChange( e, el )
{
    // toogles data when the user clicks inline, since
    // embed and embed-inline have different settings
    var viewList = jQuery('#embed_view_source'), classList = jQuery('#embed_class_source'), inline = el.checked;
    var tag = inline ? 'embed-inline' : 'embed', editorEl = eZOEPopupUtils.settings.editorElement, def = attributeDefaults[ tag ];
    if ( tag === selectedTagName ) return;
    selectedTagName = tag;
    eZOEPopupUtils.settings.selectedTag = tag;
    eZOEPopupUtils.removeChildren( viewList[0] );
    eZOEPopupUtils.removeChildren( classList[0] );
    eZOEPopupUtils.addSelectOptions( viewList[0], viewListData[ tag ] );
    eZOEPopupUtils.addSelectOptions( classList[0], classListData[ tag ] );
    jQuery( inline ? '#embed_customattributes' : '#embed-inline_customattributes' ).hide();
    jQuery( !inline ? '#embed_customattributes' : '#embed-inline_customattributes' ).show();

    if ( editorEl )
    {
        var viewValue = editorEl.getAttribute('view');
        var classValue = jQuery.trim( editorEl.className.replace(/(webkit-[\w\-]+|Apple-[\w\-]+|mceItem\w+|ezoeItem\w+|mceVisualAid)/g, '') );
    }

    if ( viewValue && viewListData[ tag ].join !== undefined && (' ' + viewListData[ tag ].join(' ') + ' ').indexOf( ' ' + viewValue + ' ' ) !== -1 )
        viewList.val( viewValue );
    else if ( def['view'] !== undefined )
        viewList.val( def['view'] );

    if ( classValue && classListData[ tag ][ classValue ] !== undefined )
        classList.val( classValue );
    else if ( def['class'] !== undefined )
        classList.val( def['class'] );
    
    if ( tinymce.isIE && contentType !== 'images' && e !== false )
        loadEmbedPreview();
}


function setEmbedAlign( e, el )
{
    jQuery('#embed_preview_image').attr( 'align', el.value );
}

function loadEmbedPreview( )
{
    // Dynamically loads embed preview when attributes change
    var url = tinyMCEPopup.editor.settings.ez_extension_url + '/embed_view/' + eZOEPopupUtils.embedObject['contentobject_id'];
    var getData = eZOEPopupUtils.jqSafeSerilizer('embed_attributes');
    jQuery.get( url, getData, function( data )
    {
        jQuery('#embed_preview').html( data );
    });
}
</script>
{/literal}

<div class="tag-view tag-type-{$tag_name} embed-view embed-class-{$embed_object.class_identifier}  embed-content-type-{$content_type}">

    <form action="JavaScript:void(0)" method="post" name="EditForm" id="EditForm" enctype="multipart/form-data">

        <div id="tabs" class="tabs">
        <ul>
            <li class="tab"><span><a href="JavaScript:void(0);">{'Properties'|i18n('design/standard/ezoe')}</a></span></li>
        </ul>
        </div>

<div class="panel_wrapper" style="height: auto;">
    <div class="panel">
        <div class="attribute-title">
            <h2 style="padding: 0 0 4px 0;" id="tag-edit-title">{'New %tag_name tag'|i18n('design/standard/ezoe', '', hash( '%tag_name', concat('&lt;', $tag_name_alias, '&gt;') ))}</h2>
        </div>
        {def $embed_relaton_options = hash(concat('eZObject_', $embed_object.id), concat('Object'|i18n('design/standard/ezoe'), ': ', $embed_object.name|shorten( 35 ) ))}
        {foreach $embed_object.assigned_nodes as $assigned_node}
            {set $embed_relaton_options = $embed_relaton_options|merge( hash(concat('eZNode_', $assigned_node.node_id), concat('Node'|i18n('design/standard/ezoe'), ': ', $assigned_node.path_identification_string|shorten( 35, '...' , 'middle' ) )) )}
        {/foreach}
        {include uri="design:ezoe/generalattributes.tpl"
                 tag_name = 'embed'
                 attributes = hash('id', $embed_relaton_options,
                                 'view', 'select',
                                 'alt', 'hidden',
                                 'class', 'select',
                                 'align', hash(
                                               '-0-', 'None'|i18n('design/standard/ezoe'),
                                               'left', 'Left'|i18n('design/standard/ezoe'),
                                               'middle', 'Center'|i18n('design/standard/ezoe'),
                                               'right', 'Right'|i18n('design/standard/ezoe')
                                               ),
                                 'inline', 'checkbox'
                                 )
                 attribute_mapping = hash( 'alt', 'size' )
                 i18n = hash('id', 'Relation'|i18n('design/standard/ezoe'),
                             'view', 'View'|i18n('design/standard/ezoe'),
                             'class', 'Class'|i18n('design/standard/ezoe'),
                             'align', 'Align'|i18n('design/standard/ezoe'),
                             'inline', 'Inline'|i18n('design/standard/ezoe')
                             )
                 attribute_defaults = hash('id', concat( $embed_type, '_', $embed_id ),
                                           'inline', 'true',
                                           'size', $default_size )
        }

        {include uri="design:ezoe/customattributes.tpl" tag_name="embed" hide=$tag_name|ne('embed') custom_attributes=$custom_attributes.embed}
        {include uri="design:ezoe/customattributes.tpl" tag_name="embed-inline" hide=$tag_name|ne('embed-inline') custom_attributes=$custom_attributes.embed-inline}

        <div class="block"> 
            <div class="left">
                <input id="SaveButton" name="SaveButton" type="submit" value="{'OK'|i18n('design/standard/ezoe')}" />
                <input id="CancelButton" name="CancelButton" type="reset" value="{'Cancel'|i18n('design/standard/ezoe')}" />
            </div>
            <div class="right" style="text-align: right;">
                <a id="embed_switch_link" href={concat( 'ezoe/upload/', $object_id,'/', $object_version,'/', $content_type )|ezurl}>{'Switch embed file'|i18n('design/standard/ezoe')}</a>
                {if $embed_object.can_read}
                    &nbsp; <a href="{concat('content/edit/', $embed_object.id, '/f/', $embed_object.current_language )|ezurl('no')}" target="_blank">{'Edit file'|i18n('design/standard/ezoe')}</a>
                {/if}
            </div>
        </div>

        <div class="block">
            <h4 id="embed_preview_heading">{'Preview'|i18n('design/standard/node/view')}:</h4>
            <div id="embed_preview">
            </div>
        </div>
    </div>

    <!-- div class="panel" id="crop_container" style="display: none;">
    </div -->

{if is_set( $attribute_panel_output )}
{foreach $attribute_panel_output as $attribute_panel_output_item}
    {$attribute_panel_output_item}
{/foreach}
{/if}

</div>
    </form>
</div>