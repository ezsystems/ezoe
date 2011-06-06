{default input_handler=$attribute.content.input
         attribute_base='ContentObjectAttribute'
         editorRow=10}

{if gt($attribute.contentclass_attribute.data_int1,1)}
    {set editorRow=$attribute.contentclass_attribute.data_int1}
{/if}

{if $input_handler.is_editor_enabled}
<!-- Start editor -->

    {def $layout_settings = $input_handler.editor_layout_settings}

    {run-once}
    {* code that only run once (common for all xml blocks) *}

    {def $plugin_list = ezini('EditorSettings', 'Plugins', 'ezoe.ini',,true() )
         $ez_locale        = ezini( 'RegionalSettings', 'Locale', 'site.ini')
         $language         = '-'|concat( $ez_locale )
         $dependency_js_list   = array( 'ezoe::i18n::'|concat( $language ) )
    }


    {foreach $plugin_list as $plugin}
        {set $dependency_js_list = $dependency_js_list|append( concat( 'plugins/', $plugin|trim, '/editor_plugin.js' ))}
    {/foreach}

    <!-- Load TinyMCE code -->
    {ezscript_require( 'ezjsc::jquery' )}
    {ezscript_require( 'tiny_mce_jquery.js' )}
    {ezscript_require( $dependency_js_list )}
    <!-- Init TinyMCE script -->
    {ezscript_require( concat( 'ezoe::tinyMCEGlobalInit::', $attribute.id, '::', $attribute.version ) )}
    {/run-once}



    <div class="oe-window">
        <textarea class="box" id="{$attribute_base}_data_text_{$attribute.id}" name="{$attribute_base}_data_text_{$attribute.id}" cols="88" rows="{$editorRow}">{$input_handler.input_xml}</textarea>
    </div>

    <div class="block">
        {if $input_handler.can_disable}
            <input class="button{if $layout_settings['buttons']|contains('disable')} hide{/if}" type="submit" name="CustomActionButton[{$attribute.id}_disable_editor]" value="{'Disable editor'|i18n('design/standard/content/datatype')}" />
        {/if}
        {ezscript_require( concat( 'ezoe::ezoeAttributeInit::', $attribute.id, '::', $attribute.version, '::', $attribute_base ) )}
    </div>
<!-- End editor -->
{else}
    {* Require jQuery even when disabled to make sure user don't get cache issues when they enable editor *}
    {ezscript_require( 'ezjsc::jquery' )}
    {let aliased_handler=$input_handler.aliased_handler}
    {include uri=concat("design:content/datatype/edit/",$aliased_handler.edit_template_name,".tpl") input_handler=$aliased_handler}
    <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_enable_editor]" value="{'Enable editor'|i18n('design/standard/content/datatype')}" /><br />
    {/let}
{/if}
{/default}
