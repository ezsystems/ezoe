<?php
//
// Created on: <27-Mar-2006 15:28:40 ks>
// Forked on: <20-Des-2007 13:02:06 ar> from eZDHTMLInputParser class
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Online Editor extension for eZ Publish
// SOFTWARE RELEASE: 5.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2012 eZ Systems AS
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



/*! \file ezoeinputparser.php
*/

/*!
  \class eZOEInputParser
  \brief The class eZOEInputParser does

*/
class eZOEInputParser extends eZXMLInputParser
{
    /**
     * Used to strip out ezoe, tinymce & browser specific classes
     */
     const HTML_CLASS_REGEX = "/(webkit-[\w\-]+|Apple-[\w\-]+|mceItem\w+|ezoeItem\w+|ezoeAlign\w+|mceVisualAid)/i";

    /**
     * Maps input tags (html) to a output tag or a hander to
     * decide what kind of ezxml tag to use.
     *
     * @var array $InputTags
     */
    public $InputTags = array(
        'section' => array( 'name' => 'section' ),
        'b'       => array( 'name' => 'strong' ),
        'bold'    => array( 'name' => 'strong' ),
        'strong'  => array( 'name' => 'strong' ),
        'i'       => array( 'name' => 'emphasize' ),
        'em'      => array( 'name' => 'emphasize' ),
        'pre'     => array( 'name' => 'literal' ),
        'div'     => array( 'nameHandler' => 'tagNameDivnImg' ),
        'u'       => array( 'nameHandler' => 'tagNameCustomHelper' ),
        'sub'       => array( 'nameHandler' => 'tagNameCustomHelper' ),
        'sup'       => array( 'nameHandler' => 'tagNameCustomHelper' ),
        'img'     => array( 'nameHandler' => 'tagNameDivnImg',
                            'noChildren' => true ),
        'h1'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h2'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h3'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h4'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h5'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h6'      => array( 'nameHandler' => 'tagNameHeader' ),
        'p'       => array( 'name' => 'paragraph' ),
        'br'      => array( 'name' => 'br',
                            'noChildren' => true ),
        'span'    => array( 'nameHandler' => 'tagNameSpan' ),
        'table'   => array( 'nameHandler' => 'tagNameTable' ),
        'td'      => array( 'name' => 'td' ),
        'tr'      => array( 'name' => 'tr' ),
        'th'      => array( 'name' => 'th' ),
        'ol'      => array( 'name' => 'ol' ),
        'ul'      => array( 'name' => 'ul' ),
        'li'      => array( 'name' => 'li' ),
        'a'       => array( 'nameHandler' => 'tagNameLink' ),
        'link'    => array( 'nameHandler' => 'tagNameLink' ),
       // Stubs for not supported tags.
        'tbody'   => array( 'name' => '' ),
        'thead'   => array( 'name' => '' ),
        'tfoot'   => array( 'name' => '' )
    );

    /**
     * Maps output tags (ezxml) to varius handlers at different stages
     * decide what kind of ezxml tag to use.
     *
     * @var array $OutputTags
     */
    public $OutputTags = array(
        'section'   => array(),

        'embed'     => array( 'initHandler'    => 'transformStyles',
                              'structHandler'  => 'appendLineParagraph',
                              'publishHandler' => 'publishHandlerEmbed',
                              'attributes'     => array( 'alt' => 'size',
                                                         'html_id' => 'xhtml:id' ) ),

        'embed-inline' => array( 'initHandler'    => 'transformStyles',
                                 'structHandler'  => 'appendLineParagraph',
                                 'publishHandler' => 'publishHandlerEmbed',
                                 'attributes'     => array( 'alt' => 'size',
                                                            'html_id' => 'xhtml:id' ) ),

        'table'     => array( 'initHandler'   => 'transformStyles',
                              'structHandler' => 'appendParagraph',
                              'attributes'    => array( 'border' => false,
                                                        'ezborder' => 'border' ) ),

        'tr'        => array(),

        'td'        => array( 'initHandler' => 'transformStyles',
                              'attributes'  => array( 'width' => 'xhtml:width',
                                                      'colspan' => 'xhtml:colspan',
                                                      'rowspan' => 'xhtml:rowspan' ) ),

        'th'        => array( 'initHandler' => 'transformStyles',
                              'attributes'  => array( 'width' => 'xhtml:width',
                                                      'colspan' => 'xhtml:colspan',
                                                      'rowspan' => 'xhtml:rowspan' ) ),

        'ol'        => array( 'structHandler' => 'structHandlerLists' ),

        'ul'        => array( 'structHandler' => 'structHandlerLists' ),

        'li'        => array( 'autoCloseOn' => array( 'li' ) ),

        'header'    => array( 'initHandler'   => 'initHandlerHeader',
                              'autoCloseOn'   => array( 'paragraph' ),
                              'structHandler' => 'structHandlerHeader' ),

        'paragraph' => array( 'parsingHandler' => 'parsingHandlerParagraph',
                              'autoCloseOn'    => array( 'paragraph' ),
                              'initHandler'    => 'transformStyles',
                              'structHandler'  => 'structHandlerParagraph' ),

        'line'      => array(),

        'br'        => array( 'parsingHandler' => 'breakInlineFlow',
                              'structHandler'  => 'structHandlerBr',
                              'attributes'     => false ),

        'literal'   => array( 'parsingHandler' => 'parsingHandlerLiteral',
                              'structHandler'  => 'appendParagraph',
                              'attributes'     => array( 'class' => 'class' ) ),

        'strong'    => array( 'structHandler' => 'appendLineParagraph' ),

        'emphasize' => array( 'structHandler' => 'appendLineParagraph' ),

        'link'      => array( 'structHandler'  => 'appendLineParagraph',
                              'publishHandler' => 'publishHandlerLink',
                              'attributes'     => array( 'title' => 'xhtml:title',
                                                         'id' => 'xhtml:id' ) ),

        'anchor'    => array( 'structHandler' => 'appendLineParagraph' ),

        'custom'    => array( 'initHandler'   => 'initHandlerCustom',
                              'structHandler' => 'structHandlerCustom' ),

        '#text'     => array( 'structHandler' => 'structHandlerText' )
    );

     /**
     * Constructor
     * For more info see {@link eZXMLInputParser::eZXMLInputParser()}
     *
     * @param int $validateErrorLevel
     * @param int $detectErrorLevel
     * @param bool $parseLineBreaks flag if line breaks should be given meaning or not
     * @param bool $removeDefaultAttrs signal if attributes of default value should not be saved.
     */
    function eZOEInputParser( $validateErrorLevel = eZXMLInputParser::ERROR_NONE,
                              $detectErrorLevel = eZXMLInputParser::ERROR_NONE,
                              $parseLineBreaks = false,
                              $removeDefaultAttrs = false )
    {
        $this->eZXMLInputParser( $validateErrorLevel,
                                 $detectErrorLevel,
                                 $parseLineBreaks,
                                 $removeDefaultAttrs );

        $ini = eZINI::instance( 'content.ini' );
        if ( $ini->hasVariable( 'header', 'AnchorAsAttribute' ) )
            $this->anchorAsAttribute = $ini->variable( 'header', 'AnchorAsAttribute' ) !== 'disabled';
    }

    /**
     * Process html text and transform it to xml.
     *
     * @param string $text
     * @param bool $createRootNode
     * @return false|DOMDocument
    */
    public function process( $text, $createRootNode = true )
    {
        $text = preg_replace( '#<!--.*?-->#s', '', $text ); // remove HTML comments
        $text = str_replace( array( '&nbsp;', '&#160;', '&#xa0;' ), "\xC2\xA0", $text );
        return parent::process( $text, $createRootNode );
    }

     /**
     * tagNameSpan (tag mapping handler)
     * Handles span tag and maps it to embed|custom|strong|emphasize|custom.underline
     * Reuses {@link eZOEInputParser::tagNameDivnImg()} for embed and custom tag mapping.
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameSpan( $tagName, &$attributes )
    {
        // embed / custom tag detection code in tagNameDivnImg
        $name = $this->tagNameDivnImg( $tagName, $attributes );

        if ( $name === '' && isset( $attributes['style'] ) )
        {
            if ( strpos( $attributes['style'], 'font-weight: bold' ) !== false )
            {
                $name = 'strong';
                unset( $attributes['style'] );
            }
            elseif ( strpos( $attributes['style'], 'font-style: italic' ) !== false )
            {
                $name = 'emphasize';
                unset( $attributes['style'] );
            }
            elseif ( strpos( $attributes['style'], 'text-decoration: underline' ) !== false
                  && self::customTagIsEnabled('underline') )
            {
                $name = 'custom';
                unset( $attributes['style'] );
                $attributes['name'] = 'underline';
                $attributes['children_required'] = 'true';
            }
        }
        return $name;
    }

     /**
     * tagNameHeader (tag mapping handler)
     * Handles H[1-6] tags and maps them to header tag
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameHeader( $tagName, &$attributes )
    {
        $attributes['level'] = $tagName[1];
        return 'header';
    }

     /**
     * tagNameTable (tag mapping handler)
     * Handles table tag and cleanups some attributes for it
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameTable( $tagName, &$attributes )
    {
        $name = 'table';
        if ( isset( $attributes['border'] ) && !isset( $attributes['ezborder'] ) )
        {
            $attributes['ezborder'] = $attributes['border'];
        }
        if ( isset( $attributes['class'] ) )
            $attributes['class'] = self::tagClassNamesCleanup( $attributes['class'] );
        return $name;
    }

     /**
     * tagNameDivnImg (tag mapping handler)
     * Handles div|img tags and maps them to embed|embed-inline|custom tag
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameDivnImg( $tagName, &$attributes )
    {
        $name = '';
        if ( isset( $attributes['id'] ) )
        {
            if ( strpos( $attributes['id'], 'eZObject_' ) !== false
              || strpos( $attributes['id'], 'eZNode_' ) !== false )
            {
                // decide if inline or block embed tag
                if ( isset( $attributes['inline'] ) && $attributes['inline'] === 'true' )
                    $name = 'embed-inline';
                else
                    $name = 'embed';

                unset( $attributes['inline'] );// unset internal stuff to make sure custom attr with same name works

                if ( isset( $attributes['class'] ) )
                {
                    $attributes['class'] = self::tagClassNamesCleanup( $attributes['class'] );
                }
            }
        }

        if ( $name === '' && isset( $attributes['type'] ) && $attributes['type'] === 'custom' )
        {
            $name = 'custom';
            unset( $attributes['type'] );// unset internal stuff to make sure custom attr with same name works
            if ( $tagName === 'div' )
                $attributes['children_required'] = 'true';
            $attributes['name'] = self::tagClassNamesCleanup( $attributes['class'] );
            unset( $attributes['class'] );// unset internal stuff to make sure custom attr with same name works
        }

        return $name;
    }

     /**
     * tagNameLink (tag mapping handler)
     * Handles a|link tags and maps them to link|anchor tag
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameLink( $tagName, &$attributes )
    {
        $name = '';
        if ( $tagName === 'link'
          && isset( $attributes['href'] )
          && isset( $attributes['rel'] )
          && ( $attributes['rel'] === 'File-List'
            || $attributes['rel'] === 'themeData'
            || $attributes['rel'] === 'colorSchemeMapping' )
          && ( strpos( $attributes['href'], '.xml' ) !== false
            || strpos( $attributes['href'], '.thmx' ) !== false) )
        {
            // empty check to not store buggy links created
            // by pasting content from ms word 2007
        }
        else if ( isset( $attributes['href'] ) )
        {
            // normal link tag
            $name = 'link';
            if ( isset( $attributes['name'] ) && !isset( $attributes['anchor_name'] ) )
            {
                $attributes['anchor_name'] = $attributes['name'];
                unset( $attributes['name'] );// unset internal stuff to make sure custom attr with same name works
            }
        }
        else if ( isset( $attributes['name'] ) )
        {
            // anchor in regular sense
            $name = 'anchor';
        }
        else if ( isset( $attributes['class'] ) && $attributes['class'] === 'mceItemAnchor' )
        {
            // anchor in TinyMCE sense (was valid up until TinyMCE 3.2)
            $name = 'anchor';
            // ie bug with name attribute, workaround using id instead
            if ( isset( $attributes['id'] ) ) $attributes['name'] = $attributes['id'];
            unset( $attributes['class'] );// unset internal stuff to make sure custom attr with same name works
            unset( $attributes['id'] );
        }

        return $name;
    }

     /**
     * tagNameCustomHelper (tag mapping handler)
     * Handles u|sub|sup tags and maps them to custom tag if they are enabled
     *
     * @param string $tagName name of input (xhtml) tag
     * @param array $attributes byref value of tag attributes
     * @return string name of ezxml tag or blank (then tag is removed, but not it's content)
     */
    function tagNameCustomHelper( $tagName, &$attributes )
    {
        $name = '';
        if ( $tagName === 'u' && self::customTagIsEnabled('underline') )
        {
            $name = 'custom';
            $attributes['name'] = 'underline';
            $attributes['children_required'] = 'true';
        }
        else if ( ( $tagName === 'sub' || $tagName === 'sup' ) && self::customTagIsEnabled( $tagName ) )
        {
            $name = 'custom';
            $attributes['name'] = $tagName;
            $attributes['children_required'] = 'true';
        }
        return $name;
    }

     /**
     * tagClassNamesCleanup
     * Used by init handlers, removes any oe/tinMCE/browser specific classes and trims the result.
     *
     * @static
     * @param string $className 'Dirty' class name as provided by TinyMCE
     * @return string Cleaned and trimmed class name
     */
    public static function tagClassNamesCleanup( $className )
    {
        return trim( preg_replace( self::HTML_CLASS_REGEX, '', $className ) );
    }

     /**
     * parsingHandlerLiteral (parsing handler, pass 1)
     * parse content of literal tag so tags are threated like text.
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return bool|null
     */
    function parsingHandlerLiteral( $element, &$param )
    {
        $ret = null;
        $data = $param[0];
        $pos = $param[1];

        $prePos = strpos( $data, '</pre>', $pos );
        if ( $prePos === false )
            $prePos = strpos( $data, '</PRE>', $pos );

        if ( $prePos === false )
            return $ret;

        $text = substr( $data, $pos, $prePos - $pos );

        $text = preg_replace( "/^<p.*?>/i", '', $text );

        $text = preg_replace( "/<\/\s?p>/i", '', $text );

        $text = preg_replace( "/<p.*?>/i", "\n\n", $text );
        $text = preg_replace( "/<\/?\s?br.*?>/i", "\n", $text );

        $text = $this->entitiesDecode( $text );
        $text = $this->convertNumericEntities( $text );

        $textNode = $this->Document->createTextNode( $text );
        $element->appendChild( $textNode );

        $param[1] = $prePos + strlen( '</pre>' );
        $ret = false;

        return $ret;
    }

     /**
     * parsingHandlerParagraph (parsing handler, pass 1)
     * parse content of paragraph tag to fix empty paragraphs issues.
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return bool|null
     */
    function parsingHandlerParagraph( $element, &$param )
    {
        $data = $param[0];
        $pos = $param[1];

        $prePos = strpos( $data, '</p>', $pos );
        if ( $prePos === false )
            $prePos = strpos( $data, '</P>', $pos );

        if ( $prePos === false )
            return null;

        $text = substr( $data, $pos, $prePos - $pos );
        // Fix empty paragraphs in Gecko (<p><br></p>)
        if ( $text === '<br>' || $text === '<BR>' || $text === '<br />' )
        {
            if ( !$this->XMLSchema->Schema['paragraph']['childrenRequired'] )
            {
                $textNode = $this->Document->createTextNode( $this->entitiesDecode( '&nbsp;' ) );
                $element->appendChild( $textNode );
            }
        }
        // Fix empty paragraphs in IE  (<P>&nbsp;</P>)
        else if ( $text === '&nbsp;' && $this->XMLSchema->Schema['paragraph']['childrenRequired'] )
        {
            $parent = $element->parentNode;
            $parent->removeChild( $element );
        }

        return true;
    }

     /**
     * breakInlineFlow (parsing handler, pass 1)
     * handle flow around <br> tags, legazy from oe 4.x
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return bool|null
     */
    function breakInlineFlow( $element, &$param )
    {
        // Breaks the flow of inline tags. Used for non-inline tags caught within inline.
        // Works for tags with no children only.
        $ret = null;
        $data =& $param[0];
        $pos =& $param[1];
        $tagBeginPos = $param[2];
        $parent = $element->parentNode;

        $wholeTagString = substr( $data, $tagBeginPos, $pos - $tagBeginPos );

        if ( $parent &&
             //!$this->XMLSchema->isInline( $element ) &&
             $this->XMLSchema->isInline( $parent ) //&&
             //!$this->XMLSchema->check( $parent, $element )
             )
        {
            $insertData = '';
            $currentParent = $parent;
            end( $this->ParentStack );
            do
            {
                $stackData = current( $this->ParentStack );
                $currentParentName = $stackData[0];
                $insertData .= '</' . $currentParentName . '>';
                $currentParent = $currentParent->parentNode;
                prev( $this->ParentStack );
            }
            while( $this->XMLSchema->isInline( $currentParent ) );

            $insertData .= $wholeTagString;

            $currentParent = $parent;
            end( $this->ParentStack );
            $appendData = '';
            do
            {
                $stackData = current( $this->ParentStack );
                $currentParentName = $stackData[0];
                $currentParentAttrString = '';
                if ( $stackData[2] )
                    $currentParentAttrString = ' ' . $stackData[2];
                $appendData = '<' . $currentParentName . $currentParentAttrString . '>' . $appendData;
                $currentParent = $currentParent->parentNode;
                prev( $this->ParentStack );
            }
            while( $this->XMLSchema->isInline( $currentParent ) );

            $insertData .= $appendData;

            $data = $insertData . substr( $data, $pos );
            $pos = 0;
            $element = $parent->removeChild( $element );
            $ret = false;
        }

        return $ret;
    }

     /**
     * initHandlerCustom (init handler, pass 2 before childre tags)
     * seesm to be doing nothing
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return bool|null
     */
    function initHandlerCustom( $element, &$params )
    {
        if ( $this->XMLSchema->isInline( $element ) )
            return null;

        self::elementStylesToAttribute( $element );
        return null;
    }

     /**
     * initHandlerHeader (init handler, pass 2 before childre tags)
     * sets anchor as attribute if setting is enabled
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return bool|null
     */
    function initHandlerHeader( $element, &$params )
    {
        if ( $this->anchorAsAttribute )
        {
            $anchorElement = $element->firstChild;
            if ( $anchorElement->nodeName === 'anchor' )
            {
                $element->setAttribute( 'anchor_name', $anchorElement->getAttribute( 'name' ) );
                $anchorElement = $element->removeChild( $anchorElement );
            }
        }
        self::elementStylesToAttribute( $element );
        return null;
    }

     /**
     * transformStyles (init handler, pass 2 before childre tags)
     * tryes to convert css styles to attributes.
     *
     * @param DOMElement $element
     * @param array $params
     * @return null|array changes structure if it contains 'result' key
     */
    function transformStyles( $element, &$params )
    {
        self::elementStylesToAttribute( $element );
        return null;
    }

     /**
     * appendLineParagraph (Structure handler, pass 2 after childre tags)
     * Structure handler for inline nodes.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function appendLineParagraph( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;
        if ( !$parent instanceof DOMElement )
        {
            return $ret;
        }

        $parentName = $parent->nodeName;
        $next = $element->nextSibling;
        $newParentName = $newParent != null ? $newParent->nodeName : '';

        // Correct schema by adding <line> and <paragraph> tags.
        if ( $parentName === 'line' || $this->XMLSchema->isInline( $parent ) )
        {
            return $ret;
        }

        if ( $newParentName === 'line' )
        {
            $element = $parent->removeChild( $element );
            $newParent->appendChild( $element );
            $ret['result'] = $newParent;
        }
        elseif ( $parentName === 'paragraph' )
        {
            $newLine = $this->createAndPublishElement( 'line', $ret );
            $element = $parent->replaceChild( $newLine, $element );
            $newLine->appendChild( $element );
            $ret['result'] = $newLine;
        }
        elseif ( $newParentName === 'paragraph' )
        {
            $newLine = $this->createAndPublishElement( 'line', $ret );
            $element = $parent->removeChild( $element );
            $newParent->appendChild( $newLine );
            $newLine->appendChild( $element );
            $ret['result'] = $newLine;
        }
        elseif ( $this->XMLSchema->check( $parent, 'paragraph' ) )
        {
            $newLine = $this->createAndPublishElement( 'line', $ret );
            $newPara = $this->createAndPublishElement( 'paragraph', $ret );
            $parent->replaceChild( $newPara, $element );
            $newPara->appendChild( $newLine );
            $newLine->appendChild( $element );
            $ret['result'] = $newLine;
        }
        return $ret;
    }

     /**
     * structHandlerBr (Structure handler, pass 2 after childre tags)
     * Structure handler for temporary <br> elements
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerBr( $element, $newParent )
    {
        $ret = array();
        if ( $newParent && $newParent->nodeName === 'line' )
        {
            $ret['result'] = $newParent->parentNode;
        }
        return $ret;
    }

     /**
     * appendParagraph (Structure handler, pass 2 after childre tags)
     * Structure handler for in-paragraph nodes.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function appendParagraph( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;
        if ( !$parent )
            return $ret;

        $parentName = $parent->nodeName;

        if ( $parentName !== 'paragraph' )
        {
            if ( $newParent && $newParent->nodeName === 'paragraph' )
            {
                $element = $parent->removeChild( $element );
                $newParent->appendChild( $element );
                $ret['result'] = $newParent;
                return $ret;
            }
            if ( $newParent
              && $newParent->parentNode
              && $newParent->parentNode->nodeName === 'paragraph' )
            {
                $para = $newParent->parentNode;
                $element = $parent->removeChild( $element );
                $para->appendChild( $element );
                $ret['result'] = $newParent->parentNode;
                return $ret;
            }

            if ( $this->XMLSchema->check( $parentName, 'paragraph' ) )
            {
                $newPara = $this->createAndPublishElement( 'paragraph', $ret );
                $parent->replaceChild( $newPara, $element );
                $newPara->appendChild( $element );
                $ret['result'] = $newPara;
            }
        }
        return $ret;
    }

     /**
     * structHandlerText (Structure handler, pass 2 after childre tags)
     * Structure handler for #text.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerText( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;

        // Remove empty text elements
        if ( $element->textContent == '' )
        {
            $element = $parent->removeChild( $element );
            return $ret;
        }

        $ret = $this->appendLineParagraph( $element, $newParent );

        // Fix for italic/bold styles in Mozilla.
        $addStrong = $addEmph = null;
        $myParent = $element->parentNode;
        while( $myParent )
        {
            $style = $myParent->getAttribute( 'style' );
            if ( $style && $addStrong !== false && strpos( $style, 'font-weight: bold;' ) !== false )
            {
                $addStrong = true;
            }
            if ( $style && $addEmph !== false && strpos( $style, 'font-style: italic;' ) !== false )
            {
                $addEmph = true;
            }

            if ( $myParent->nodeName === 'strong' )
            {
                $addStrong = false;
            }
            elseif ( $myParent->nodeName === 'emphasize' )
            {
                $addEmph = false;
            }
            elseif ( $myParent->nodeName === 'td'
                  || $myParent->nodeName === 'th'
                  || $myParent->nodeName === 'section' )
            {
                break;
            }
            $tmp = $myParent;
            $myParent = $tmp->parentNode;
        }

        $parent = $element->parentNode;
        if ( $addEmph === true )
        {
            $emph = $this->Document->createElement( 'emphasize' );
            $emph = $parent->insertBefore( $emph, $element );
            $element = $parent->removeChild( $element );
            $emph->appendChild( $element );
        }
        if ( $addStrong === true )
        {
            $strong = $this->Document->createElement( 'strong' );
            $strong = $parent->insertBefore( $strong, $element );
            $element = $parent->removeChild( $element );
            $strong->appendChild( $element );
        }

        // Left trim spaces:
        if ( $this->TrimSpaces )
        {
            $trim = false;
            $currentElement = $element;

            // Check if it is the first element in line
            do
            {
                if ( $currentElement->previousSibling )
                {
                    break;
                }

                $currentElement = $currentElement->parentNode;

                if ( $currentElement instanceof DOMElement &&
                     ( $currentElement->nodeName === 'line' ||
                       $currentElement->nodeName === 'paragraph' ) )
                {
                    $trim = true;
                    break;
                }

            } while ( $currentElement instanceof DOMElement );

            if ( $trim )
            {
                // Trim and remove if empty
                $element->textContent = ltrim( $element->textContent );
                if ( $element->textContent == '' )
                {
                    $parent = $element->parentNode;
                    $element = $parent->removeChild( $element );
                }
            }
        }

        return $ret;
    }

     /**
     * structHandlerHeader (Structure handler, pass 2 after childre tags)
     * Structure handler for header tag.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerHeader( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;
        $level = $element->getAttribute( 'level' );
        if ( !$level )
        {
            $level = 1;
        }

        $element->removeAttribute( 'level' );
        if ( $level )
        {
            $sectionLevel = -1;
            $current = $element;
            while ( $current->parentNode )
            {
                $tmp = $current;
                $current = $tmp->parentNode;
                if ( $current->nodeName === 'section' )
                {
                    ++$sectionLevel;
                }
                elseif ( $current->nodeName === 'td' )
                {
                    ++$sectionLevel;
                    break;
                }
            }
            if ( $level > $sectionLevel )
            {
                $newTempParent = $parent;
                for ( $i = $sectionLevel; $i < $level; $i++ )
                {
                   $newSection = $this->Document->createElement( 'section' );
                   if ( $i == $sectionLevel )
                   {
                       $newSection = $newTempParent->insertBefore( $newSection, $element );
                   }
                   else
                   {
                       $newTempParent->appendChild( $newSection );
                   }
                   // Schema check
                   if ( !$this->processBySchemaTree( $newSection ) )
                   {
                       return $ret;
                   }
                   $newTempParent = $newSection;
                   unset( $newSection );
                }
                $elementToMove = $element;
                while( $elementToMove &&
                       $elementToMove->nodeName !== 'section' )
                {
                    $next = $elementToMove->nextSibling;
                    $elementToMove = $parent->removeChild( $elementToMove );
                    $newTempParent->appendChild( $elementToMove );
                    $elementToMove = $next;

                    if ( !$elementToMove ||
                         ( $elementToMove->nodeName === 'header' &&
                           $elementToMove->getAttribute( 'level' ) <= $level ) )
                        break;
                }
            }
            elseif ( $level < $sectionLevel )
            {
                $newLevel = $sectionLevel + 1;
                $current = $element;
                while( $level < $newLevel )
                {
                    $tmp = $current;
                    $current = $tmp->parentNode;
                    if ( $current->nodeName === 'section' )
                        --$newLevel;
                }
                $elementToMove = $element;
                while( $elementToMove &&
                       $elementToMove->nodeName !== 'section' )
                {
                    $next = $elementToMove->nextSibling;
                    $parent->removeChild( $elementToMove );
                    $current->appendChild( $elementToMove );
                    $elementToMove = $next;

                    if ( !$elementToMove ||
                         ( $elementToMove->nodeName === 'header' &&
                         $elementToMove->getAttribute( 'level' ) <= $level ) )
                        break;
                }
            }
        }
        return $ret;
    }

     /**
     * structHandlerCustom (Structure handler, pass 2 after childre tags)
     * Structure handler for custom tag.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerCustom( $element, $newParent )
    {
        $ret = array();
        $isInline = $this->XMLSchema->isInline( $element );
        if ( $isInline )
        {
            $ret = $this->appendLineParagraph( $element, $newParent );

            $value = $element->getAttribute( 'value' );
            if ( $value )
            {
                $value = $this->washText( $value );
                $textNode = $this->Document->createTextNode( $value );
                $element->appendChild( $textNode );
            }
        }
        else
        {
            $ret = $this->appendParagraph( $element, $newParent );
        }
        return $ret;
    }

     /**
     * structHandlerLists (Structure handler, pass 2 after childre tags)
     * Structure handler for ul|ol tags.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerLists( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;
        $parentName = $parent->nodeName;

        if ( $parentName === 'paragraph' )
            return $ret;

        // If we are inside a list
        if ( $parentName === 'ol' || $parentName === 'ul' )
        {
            // If previous 'li' doesn't exist, create it,
            // else append to the previous 'li' element.
            $prev = $element->previousSibling;
            if ( !$prev )
            {
                $li = $this->Document->createElement( 'li' );
                $li = $parent->insertBefore( $li, $element );
                $element = $parent->removeChild( $element );
                $li->appendChild( $element );
            }
            else
            {
                $lastChild = $prev->lastChild;
                if ( $lastChild->nodeName !== 'paragraph' )
                {
                    $para = $this->Document->createElement( 'paragraph' );
                    $element = $parent->removeChild( $element );
                    $prev->appendChild( $element );
                    $ret['result'] = $para;
                }
                else
                {
                    $element = $parent->removeChild( $element );
                    $lastChild->appendChild( $element );
                    $ret['result'] = $lastChild;
                }
                return $ret;
            }
        }
        else if ( $parentName === 'li' )
        {
            $prev = $element->previousSibling;
            if ( $prev )
            {
                $element = $parent->removeChild( $element );
                $prev->appendChild( $element );
                $ret['result'] = $prev;
                return $ret;
            }
        }
        $ret = $this->appendParagraph( $element, $newParent );
        return $ret;
    }

     /**
     * structHandlerParagraph (Structure handler, pass 2 after childre tags)
     * Structure handler for paragraph tag.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerParagraph( $element, $newParent )
    {
        $ret = array();

        $parentNode = $element->parentNode;
        if ( $parentNode->nodeName === 'custom' &&
                !$this->XMLSchema->isInline( $parentNode ) &&
                $parentNode->childNodes->length === 1 &&
                $parentNode->getAttribute( 'name' ) === $element->textContent )
        {
            // removing the paragraph as it is there only to handle the custom
            // in the rich text editor
            $parentNode->removeAttribute( 'children_required' );
            $parentNode->removeChild( $element );
            return $ret;
        }

        if ( $element->getAttribute( 'ezparser-new-element' ) === 'true' &&
             !$element->hasChildren() )
        {
            $element = $element->parentNode->removeChild( $element );
            return $ret;
        }

        // Removes single line tag
        $line = $element->lastChild;
        if ( $element->childNodes->length == 1 && $line->nodeName === 'line' )
        {
            $lineChildren = array();
            $lineChildNodes = $line->childNodes;
            foreach ( $lineChildNodes as $lineChildNode )
            {
                $lineChildren[] = $lineChildNode;
            }

            $line = $element->removeChild( $line );
            foreach ( $lineChildren as $lineChild )
            {
                $element->appendChild( $lineChild );
            }
        }

        return $ret;
    }

     /**
     * publishHandlerLink (Publish handler, pass 2 after schema validation)
     * Publish handler for link element, converts href to [object|node|link]_id.
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return null|array changes structure if it contains 'result' key
     */
    function publishHandlerLink( $element, &$params )
    {
        $ret  = null;
        $href = $element->getAttribute( 'href' );
        if ( $href )
        {
            $objectID = false;
            if ( strpos( $href, 'ezobject' ) === 0
              && preg_match( "@^ezobject://([0-9]+)/?(#.+)?@i", $href, $matches ) )
            {
                $objectID = $matches[1];
                if ( isset( $matches[2] ) )
                    $anchorName = substr( $matches[2], 1 );
                $element->setAttribute( 'object_id', $objectID );
                if ( !eZContentObject::exists( $objectID ))
                {
                    $this->Messages[] = ezpI18n::tr( 'design/standard/ezoe/handler',
                                                'Object %1 does not exist.',
                                                false,
                                                array( $objectID ) );
                }
            }
            /*
             * rfc2396: ^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
             * ezdhtml: "@^eznode://([^/#]+)/?(#[^/]*)?/?@i"
             */
            elseif ( strpos( $href, 'eznode' ) === 0
                  && preg_match( "@^eznode://([^#]+)(#.+)?@i", $href, $matches ) )
            {
                $nodePath = trim( $matches[1], '/' );
                if ( isset( $matches[2] ) )
                    $anchorName = substr( $matches[2], 1 );

                if ( is_numeric( $nodePath ) )
                {
                    $nodeID = $nodePath;
                    $node = eZContentObjectTreeNode::fetch( $nodeID );
                    if ( !$node instanceOf eZContentObjectTreeNode )
                    {
                        $this->Messages[] = ezpI18n::tr( 'design/standard/ezoe/handler',
                                                    'Node %1 does not exist.',
                                                    false,
                                                    array( $nodeID ) );
                    }
                }
                else
                {
                    $node = eZContentObjectTreeNode::fetchByURLPath( $nodePath );
                    if ( !$node instanceOf eZContentObjectTreeNode )
                    {
                        $this->Messages[] = ezpI18n::tr( 'design/standard/ezoe/handler',
                                                    'Node &apos;%1&apos; does not exist.',
                                                    false,
                                                    array( $nodePath ) );
                    }
                    else
                    {
                        $nodeID = $node->attribute( 'node_id' );
                    }
                    $element->setAttribute( 'show_path', 'true' );
                }

                if ( isset( $nodeID ) && $nodeID )
                {
                    $element->setAttribute( 'node_id', $nodeID );
                }

                if ( isset( $node ) && $node instanceOf eZContentObjectTreeNode )
                {
                    $objectID = $node->attribute( 'contentobject_id' );
                }
            }
            elseif ( strpos( $href, '#' ) === 0 )
            {
                $anchorName = substr( $href, 1 );
            }
            else
            {
                $temp = explode( '#', $href );
                $url = $temp[0];
                if ( isset( $temp[1] ) )
                {
                    $anchorName = $temp[1];
                }

                if ( $url )
                {
                    // Protection from XSS attack
                    if ( preg_match( "/^(java|vb)script:.*/i" , $url ) )
                    {
                        $this->isInputValid = false;
                        $this->Messages[] = "Using scripts in links is not allowed, '$url' has been removed";
                        $element->removeAttribute( 'href' );
                        return $ret;
                    }

                    // Check mail address validity following RFC 5322 and RFC 5321
                    if ( preg_match( "/^mailto:([^.][a-z0-9!#\$%&'*+-\/=?`{|}~^]+@([a-z0-9.-]+))/i" , $url, $mailAddr ) )
                    {
                        if ( !eZMail::validate( $mailAddr[1] ) )
                        {
                            $this->isInputValid = false;
                            if ( $this->errorLevel >= 0 )
                                $this->Messages[] = ezpI18n::tr( 'kernel/classes/datatypes/ezxmltext',
                                                            "Invalid e-mail address: '%1'",
                                                            false,
                                                            array( $mailAddr[1] ) );
                            $element->removeAttribute( 'href' );
                            return $ret;
                        }

                    }
                    // Store urlID instead of href
                    $url   = str_replace(array('&amp;', '%28', '%29'), array('&', '(', ')'), $url );
                    $urlID = eZURL::registerURL( $url );

                    if ( $urlID )
                    {
                        if ( !in_array( $urlID, $this->urlIDArray ) )
                            $this->urlIDArray[] = $urlID;

                        $element->setAttribute( 'url_id', $urlID );
                    }
                }
            }

            if ( $objectID && !in_array( $objectID, $this->linkedObjectIDArray ) )
                $this->linkedObjectIDArray[] = $objectID;

            if ( isset( $anchorName ) && $anchorName )
                $element->setAttribute( 'anchor_name', $anchorName );
        }
        return $ret;
    }

     /**
     * publishHandlerEmbed (Publish handler, pass 2 after schema validation)
     * Publish handler for embed element, convert id to [object|node]_id parameter,
     * fixes align=middle value (if embed was image) and tries to map css to attributes
     *
     * @param DOMElement $element
     * @param array $param parameters for xml element
     * @return null|array changes structure if it contains 'result' key
     */
    function publishHandlerEmbed( $element, &$params )
    {
        $ID = $element->getAttribute( 'id' );
        if ( $ID )
        {
            $objectID = false;
            $element->removeAttribute( 'id' );
            if ( strpos( $ID, 'eZObject_' ) !== false )
            {
                $objectID = substr( $ID, strpos( $ID, '_' ) + 1 );
                $element->setAttribute( 'object_id', $objectID );
                $object = eZContentObject::fetch( $objectID );
                if ( !$object )
                {
                    if ( !in_array( $objectID, $this->deletedEmbeddedObjectIDArray ) )
                        $this->deletedEmbeddedObjectIDArray[] = $objectID;
                }
                else if ( $object->attribute('status') == eZContentObject::STATUS_ARCHIVED )
                    $this->thrashedEmbeddedObjectIDArray[] = $objectID;
            }
            else if ( strpos( $ID, 'eZNode_' ) !== false )
            {
                $nodeID = substr( $ID, strpos( $ID, '_' ) + 1 );
                $element->setAttribute( 'node_id', $nodeID );

                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if ( $node )
                    $objectID = $node->attribute( 'contentobject_id' );
                else if ( !in_array( $nodeID, $this->deletedEmbeddedNodeIDArray ) )
                    $this->deletedEmbeddedNodeIDArray[] = $nodeID;
            }

            if ( $objectID && !in_array( $objectID, $this->embeddedObjectIDArray ) )
                $this->embeddedObjectIDArray[] = $objectID;
        }
        $align = $element->getAttribute( 'align' );
        if ( $align && $align === 'middle' )
        {
            $element->setAttribute( 'align', 'center' );
        }
        return null;
    }

     /**
     * processAttributesBySchema
     * Parses customattributes attribute and splits it into actual
     * custom: xml attributes, passes processing of normal attributes
     * to parent class.
     *
     * @param DOMElement $element
     */
    function processAttributesBySchema( $element )
    {
        // custom attributes conversion
        $attr = $element->getAttribute( 'customattributes' );
        if ( $attr )
        {
            $attrArray = explode( 'attribute_separation', $attr );
            foreach( $attrArray as $attr )
            {
                if ( $attr !== '' && strpos( $attr, '|' ) !== false )
                {
                    list( $attrName, $attrValue ) = explode( '|', $attr, 2 );
                    $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/custom/',
                                              'custom:' . $attrName,
                                              $attrValue );
                }
            }
        }

        parent::processAttributesBySchema( $element );
    }

    /*
     * Misc internally (by this and main xml handler) used functions
     */
    function getUrlIDArray()
    {
        return $this->urlIDArray;
    }

    function getEmbeddedObjectIDArray()
    {
        return $this->embeddedObjectIDArray;
    }

    function getLinkedObjectIDArray()
    {
        return $this->linkedObjectIDArray;
    }

     /**
     * Get list over currently deleted embeded objects, nodes and (optionally) objects in trash
     *
     * @param bool $includeTrash
     * @return array
     */
    function getDeletedEmbedIDArray( $includeTrash = false )
    {
        $arr = array();
        if ( $this->deletedEmbeddedNodeIDArray )
            $arr['nodes'] = $this->deletedEmbeddedNodeIDArray;
        if ( $this->deletedEmbeddedObjectIDArray )
            $arr['objects'] = $this->deletedEmbeddedObjectIDArray;
        if ( $includeTrash && $this->thrashedEmbeddedObjectIDArray )
            $arr['trash'] = $this->thrashedEmbeddedObjectIDArray;
        return $arr;
    }

     /**
     * Check if a custom tag is enabled
     *
     * @param string $name
     * @return bool
     */
    public static function customTagIsEnabled( $name )
    {
        if ( self::$customTagList === null )
        {
            $ini = eZINI::instance( 'content.ini' );
            self::$customTagList = $ini->variable( 'CustomTagSettings', 'AvailableCustomTags' );
        }
        return in_array( $name, self::$customTagList );
    }

     /**
     * Trying to convert CSS rules to XML attributes
     * (for the case of pasting from external source)
     *
     * @param DOMElement $element
     */
    protected static function elementStylesToAttribute( DOMElement $element )
    {
        $styleString = $element->getAttribute( 'style' );
        if ( $styleString )
        {
            $styleArray = explode( ';', $styleString );
            foreach( $styleArray as $style )
            {
                if ( !$style )
                    continue;

                list( $name, $value ) = explode( ':', $style );
                $name  = trim( $name );
                $value = trim( $value );

                if ( $name === 'float' || $name === 'text-align' )
                    $name = 'align';

                if ( $name )
                    $element->setAttribute( $name, $value );
            }
        }
    }

    protected $urlIDArray = array();
    protected $linkedObjectIDArray = array();
    protected $embeddedObjectIDArray = array();
    protected $deletedEmbeddedNodeIDArray = array();
    protected $deletedEmbeddedObjectIDArray = array();
    protected $thrashedEmbeddedObjectIDArray = array();


    protected $anchorAsAttribute = false;

    protected static $customTagList = null;
}

?>
