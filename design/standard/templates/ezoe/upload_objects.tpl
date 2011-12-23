{set scope=global persistent_variable=hash('title', 'Upload new Object'|i18n('design/standard/ezoe'),
                                           'scripts', array('ezoe/ez_core.js',
                                                            'ezoe/ez_core_animation.js',
                                                            'ezoe/ez_core_accordion.js',
                                                            'ezoe/popup_utils.js'),
                                           'css', array()
                                           )}
<script type="text/javascript">
var contentType = '{$content_type}', classFilter = [];

{foreach $class_filter_array as $class_filter}
    classFilter.push('{$class_filter}');
{/foreach}

{literal}

tinyMCEPopup.onInit.add( function(){
    var slides = ez.$$('div.panel'), navigation = ez.$$('#tabs li.tab');
    slides.accordion( navigation, {duration: 100, transition: ez.fx.sinoidal, accordionAutoFocusTag: 'input[type=text]'}, {opacity: 0, display: 'none'} );
    // custom link generator, to redirect links to browse view if not in browse view
    eZOEPopupUtils.settings.browseLinkGenerator = function( n, mode, ed )
    {
        if ( n.children_count )
        {
           var tag = document.createElement("a");
           tag.setAttribute('href', 'JavaScript:eZOEPopupUtils.browse(' + n.node_id + ');');
           tag.setAttribute('title', ed.getLang('browse') + ': ' + n.url_alias );
           if ( mode !== 'browse' ) ez.$( tag ).addEvent('click', function(){ slides.accordionGoto( 2 ); });
           return tag;
        }
        var tag = document.createElement("span");
        tag.setAttribute('title', n.url_alias );
        return tag;
    };
});
</script>
{/literal}

<div class="upload-view">
    <form action={concat('ezoe/upload/', $object_id, '/', $object_version, '/auto/1' )|ezurl} method="post" target="embed_upload" name="EmbedForm" id="EmbedForm" enctype="multipart/form-data" onsubmit="document.getElementById('upload_in_progress').style.display = '';">

        <div id="tabs" class="tabs">
        <ul>
            <li class="tab" title="{'Upload file from your local machine.'|i18n('design/standard/ezoe/wai')}"><span><a href="JavaScript:void(0);">{'Upload'|i18n('design/admin/content/upload')}</a></span></li>
            <li class="tab" title="{'Search for content already in eZ Publish.'|i18n('design/standard/ezoe/wai')}"><span><a href="JavaScript:void(0);">{'Search'|i18n('design/admin/content/search')}</a></span></li>
            <li class="tab" title="{'Browse the content tree in eZ Publish.'|i18n('design/standard/ezoe/wai')}"><span><a href="JavaScript:void(0);">{'Browse'|i18n('design/standard/ezoe')}</a></span></li>
            <li class="tab" title="{'Select or browse content among your personal eZ Publish bookmarks.'|i18n('design/standard/ezoe/wai')}"><span><a href="JavaScript:void(0);">{'Bookmarks'|i18n( 'design/admin/content/browse' )}</a></span></li>
        </ul>
        </div>

<div class="panel_wrapper" style="min-height: 360px;">
        <div class="panel">
            <table class="properties">

                {include uri="design:ezoe/upload/common_attributes.tpl"}

                <!-- Next attribute is file / media specific  -->
                <tr>
                    <td class="column1"><label id="descriptionlabel" for="objectDescription">{'Description'|i18n('design/standard/ezoe')}</label></td>
                    <td colspan="2"><input id="objectDescription" name="ContentObjectAttribute_description" size="53" type="text" value="" title="{'Description to the file your uploading, so internet clients can read more about it before they decide to download it.'|i18n('design/standard/ezoe/wai')}" /></td>
                </tr>
                <tr>
                    <td colspan="3">
                    <input id="uploadButton" name="uploadButton" type="submit" value="{'Upload local file'|i18n('design/standard/ezoe')}" />
                    <span id="upload_in_progress" style="display: none; color: #666; background: #fff url({"stylesheets/skins/default/img/progress.gif"|ezdesign('single')}) no-repeat top left scroll; padding-left: 32px;">{'Upload is in progress, it may take a few seconds...'|i18n('design/standard/ezoe')}</span>
                    </td>
                </tr>
            </table>

            <iframe id="embed_upload" name="embed_upload" frameborder="0" scrolling="no" style="border: 0; width: 99%; height: 30px; margin: 0; overflow: auto; overflow-x: hidden;"></iframe>

            {* Related objects *}
            {if and( $related_contentobjects|count|gt( 0 ), $grouped_related_contentobjects.objects|count|gt( 0 ) )}
            <div class="block contenttype_object">
            <h2>{'Related content'|i18n('design/standard/ezoe')}</h2>
                    <table class="list" cellspacing="0">
                    <tr>
                        <th class="name">{'Name'|i18n( 'design/admin/content/edit' )}</th>
                        <th class="class">{'Type'|i18n( 'design/admin/content/edit' )}</th>
                    </tr>
                    {foreach $grouped_related_contentobjects.objects as $relation sequence array( bglight, bgdark ) as $sequence}
                        <tr class="{$sequence}">
                            <td class="name">{$relation.object.class_name|class_icon( small, $relation.object.class_name )}&nbsp;<a href="JavaScript:eZOEPopupUtils.selectByEmbedId( {$relation.object.id} )">{$relation.object.name|wash|shorten( 35 )}</a></td>
                            <td class="class">{$relation.object.class_name|wash}</td>
                        </tr>
                    {/foreach}
                    </table>
            </div>
            {else}
            <div class="block">
                <p>{"There are no related objects."|i18n("design/standard/ezoe")}</p>
            </div>
            {/if}
        </div>

{include uri="design:ezoe/box_search.tpl" box_embed_mode=false() box_class_filter_array=$class_filter_array}

{include uri="design:ezoe/box_browse.tpl" box_embed_mode=false() box_class_filter_array=$class_filter_array}

{include uri="design:ezoe/box_bookmarks.tpl" box_embed_mode=false()}

</div>
     </form>
</div>