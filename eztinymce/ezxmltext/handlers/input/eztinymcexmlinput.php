<?php
//
// Definition of eZTINYMCEXMLInput class
//
// Created on: <06-Nov-2002 15:10:02 wy>
//
// Copyright (C) 1999-2006 eZ systems as. All rights reserved.
//

/*! \file eztinymcexmlinput.php
*/

/*!
  \class eZTINYMCEXMLInput
  \brief The class eZTINYMCEXMLInput does

*/
require_once( 'kernel/common/template.php' );
include_once( 'lib/eztemplate/classes/eztemplateincludefunction.php' );
/*include_once( "kernel/classes/datatypes/ezimage/ezimagevariation.php");
include_once( "kernel/classes/datatypes/ezimage/ezimage.php");
include_once( "lib/ezimage/classes/ezimagelayer.php" );
include_once( "lib/ezimage/classes/ezimagetextlayer.php" );
include_once( "lib/ezimage/classes/ezimagefont.php" );
include_once( "lib/ezimage/classes/ezimageobject.php" );
include_once( "lib/eztemplate/classes/eztemplateimageoperator.php" );
include_once( "lib/ezutils/classes/ezini.php" );
include_once( "lib/ezutils/classes/ezsys.php" );
include_once( "kernel/classes/ezcontentobject.php");
include_once( 'kernel/classes/datatypes/ezurl/ezurlobjectlink.php' );
*/

include_once( 'extension/eztinymce/lib/system.php' );
include_once( 'extension/eztinymce/ezinfo.php' );

datatype_class( 'ezxmltext', 'eZXMLInputHandler' );

class eZTINYMCEXMLInput extends eZXMLInputHandler
{
    /*!
     Constructor
    */
    function eZTINYMCEXMLInput( &$xmlData, $aliasedType, $contentObjectAttribute )
    {
        $this->eZXMLInputHandler( $xmlData, $aliasedType, $contentObjectAttribute );
        $contentIni = eZINI::instance( 'content.ini' );
        if ( $contentIni->hasVariable( 'header', 'UseStrictHeaderRule' ) )
        {
            if ( $contentIni->variable( 'header', 'UseStrictHeaderRule' ) == "true" )
                $this->IsStrictHeader = true;
        }

        include_once( 'lib/version.php' );
        $this->eZPublishVersion = eZPublishSDK::majorVersion() + eZPublishSDK::minorVersion() * 0.1;

        $this->browserType = $this->browserSupportsDHTMLType();

        $ini = eZINI::instance( 'ezxml.ini' );
        if ( $ini->hasVariable( 'InputSettings', 'TrimSpaces' ) )
        {
            $trimSpaces = $ini->variable( 'InputSettings', 'TrimSpaces' );
            $this->trimSpaces = $trimSpaces == 'true' ? true : false;
        }

        if ( $ini->hasVariable( 'InputSettings', 'AllowMultipleSpaces' ) )
        {
            $allowMultipleSpaces = $ini->variable( 'InputSettings', 'AllowMultipleSpaces' );
            $this->allowMultipleSpaces = $allowMultipleSpaces == 'true' ? true : false;
        }
    }

    /*!
     \reimp
    */
    function hasAttribute( $name )
    {
        return ( $name == 'is_editor_enabled' or
                 $name == 'browser_supports_dhtml_type' or
                 $name == 'is_compatible_version' or
                 $name == 'version' or
                 $name == 'required_version' or
                 $name == 'ezpublish_version' or
                 eZXMLInputHandler::hasAttribute( $name ) );
    }

    /*!
     \reimp
    */
    function attribute( $name )
    {
        if ( $name === 'is_editor_enabled' )
            $attr = eZTINYMCEXMLInput::isEditorEnabled();
        else if ( $name === 'browser_supports_dhtml_type' )
            $attr = eZTINYMCEXMLInput::browserSupportsDHTMLType();
        else if ( $name === 'is_compatible_version' )
            $attr = eZTINYMCEXMLInput::isCompatibleVersion();
        else if ( $name === 'version' )
            $attr = eZTINYMCEXMLInput::version();
        else if ( $name === 'required_version' )
            $attr = eZTINYMCEXMLInput::requiredVersion();
        else if ( $name === 'ezpublish_version' )
            $attr = $this->eZPublishVersion;
        else
            $attr = eZXMLInputHandler::attribute( $name );
        return $attr;
    }

    /*!
     \static
     \return true if the browser supports DHTML editing.
    */
    static function browserSupportsDHTMLType()
    {
        $supportsDHTMLType = true;
        $userAgent = eZSys::serverVariable( 'HTTP_USER_AGENT' );

        if ( eregi('MSIE[ \/]([0-9\.]+)', $userAgent, $browserInfo ) )
        {
            $version = $browserInfo[1];
            if ( $version < 6.0 )
            {
                $supportsDHTMLType = false;
            }
        }

        return $supportsDHTMLType;
    }

    /*!
     \return boolean
    */
    function isCompatibleVersion()
    {
        return $this->eZPublishVersion >= 4.0;
    }

    /*!
     \static
     \return OE version
    */
    static function version()
    {
        $info = eztinymceInfo::info();
        $version = $info['version'];
        return $version;
    }

    /*!
     This function is deprecated, left for compatibility.
     \static
     \return eZ publish required OE version list
    */
    static function requiredVersion()
    {
        $requiredVersion = false;

        if ( file_exists( 'kernel/common/ezoe.php' ) )
        {
            include_once( 'kernel/common/ezoe.php' );
            $requiredVersionList = eZOE::requiredVersionList();
            $requiredVersion = implode( "," , $requiredVersionList );
        }

        return $requiredVersion;
    }

    /*!
     \static
     \return true if the editor is enabled. The editor can be enabled/disabled by a
             button in the web interface.
    */
    static function isEditorEnabled()
    {
        $dhtmlInput = true;
        $http = eZHTTPTool::instance();
        if ( $http->hasSessionVariable( 'eZTINYMCEXMLInputExtension' ) )
            $dhtmlInput = $http->sessionVariable( 'eZTINYMCEXMLInputExtension' );
        return $dhtmlInput;
    }

    /*!
     Sets whether the DHTML editor is enabled or not.
    */
    static function setIsEditorEnabled( $isEnabled )
    {
        $http = eZHTTPTool::instance();
        $http->setSessionVariable( 'eZTINYMCEXMLInputExtension', $isEnabled );
    }

    /*!
     \static
     \return true if the editor can be used. This is determinded by whether the browser supports DHTML and that
             the editor is enabled.
    */
    static function isEditorActive()
    {
        if ( !eZTINYMCEXMLInput::browserSupportsDHTMLType() )
            return false;

        return eZTINYMCEXMLInput::isEditorEnabled();
    }

    /*!
     \reimp
    */
    function isValid()
    {
        return eZTINYMCEXMLInput::browserSupportsDHTMLType();
    }

    /*!
     \reimp
    */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute )
    {
        switch ( $action )
        {
            case 'enable_editor':
            {
                eZTINYMCEXMLInput::setIsEditorEnabled( true );
            } break;
            case 'disable_editor':
            {
                eZTINYMCEXMLInput::setIsEditorEnabled( false );
            } break;
            default :
            {
                $debug = eZDebug::instance();
                $debug->writeError( 'Unknown custom HTTP action: ' . $action, 'eZTINYMCEXMLInput' );
            } break;
        }
    }

    /*!
     \reimp
    */
    function editTemplateSuffix( &$contentobjectAttribute )
    {
        return 'eztinymce';
    }

    /*!
      Updates URL - object links.
    */
    function updateUrlObjectLinks( $contentObjectAttribute, $urlIDArray )
    {
        $objectAttributeID = $contentObjectAttribute->attribute( "id" );
        $objectAttributeVersion = $contentObjectAttribute->attribute('version');

        foreach( $urlIDArray as $urlID )
        {
            $linkObjectLink = eZURLObjectLink::fetch( $urlID, $objectAttributeID, $objectAttributeVersion );
            if ( $linkObjectLink == null )
            {
                $linkObjectLink = eZURLObjectLink::create( $urlID, $objectAttributeID, $objectAttributeVersion );
                $linkObjectLink->store();
            }
        }
    }

    /*!
     \reimp
    */
    function validateInput( $http, $base, $contentObjectAttribute )
    {
        $this->ContentObjectAttributeID = $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $this->ContentObjectAttributeVersion = $contentObjectAttributeVersion = $contentObjectAttribute->attribute('version');

        if ( !$this->isEditorEnabled() )
        {
            $aliasedHandler = $this->attribute( 'aliased_handler' );
            return $aliasedHandler->validateInput( $http, $base, $contentObjectAttribute );
        }
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $text = $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) );

            $text = preg_replace( '#<!--.*?-->#s', '', $text ); // remove HTML comments
            $text = str_replace( "\r", '', $text);

            if ( $this->browserType === 'IE' )
            {
                $text = preg_replace( "/[\n\t]/", '', $text);
            }
            else
            {
                $text = preg_replace( "/[\n\t]/", ' ', $text);
            }


            include_once( 'extension/eztinymce/ezxmltext/handlers/input/eztinymceinputparser.php' );

            $parser = new eZTINYMCEInputParser();

            $document = $parser->process( $text );

            // Remove last empty paragraph (added in the output part)
            $parent = $document->documentElement;
            $lastChild = $parent->lastChild;
            while( $lastChild && $lastChild->nodeName !== 'paragraph' )
            {
                $parent = $lastChild;
                $lastChild = $parent->lastChild;
            }

            if ( $lastChild && $lastChild->nodeName === 'paragraph' )
            {
                $textChild = $lastChild->lastChild;
                if ( !$textChild ||
                     ( $lastChild->childNodes->length == 1 &&
                       $textChild->nodeType == XML_TEXT_NODE &&
                       ( $textChild->textContent == ' ' || $textChild->textContent == '' ) ) )
                {
                    $parent->removeChild( $lastChild );
                }
            }

            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( $classAttribute->attribute( 'is_required' ) == true )
            {
                $root = $document->documentElement;
                if ( $root->childNodes->length == 0 )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Content required' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }

            // Update URL-object links
            $urlIDArray = $parser->getUrlIDArray();
            if ( count( $urlIDArray ) > 0 )
            {
                $this->updateUrlObjectLinks( $contentObjectAttribute, $urlIDArray );
            }

            $contentObject = $contentObjectAttribute->attribute( 'object' );
            $contentObject->appendInputRelationList( $parser->getEmbeddedObjectIDArray(), eZContentObject::RELATION_EMBED );
            $contentObject->appendInputRelationList( $parser->getLinkedObjectIDArray(), eZContentObject::RELATION_LINK );

            $xmlString = eZXMLTextType::domString( $document );

            $contentObjectAttribute->setAttribute( 'data_text', $xmlString );
            $contentObjectAttribute->setValidationLog( $parser->Messages );

            return eZInputValidator::STATE_ACCEPTED;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
    }


    /*

      Editor inner output implementation

    */

    // Get section level and reset cuttent node according to input header.
    function &sectionLevel( &$sectionLevel, $headerLevel, &$TagStack, &$currentNode, &$domDocument )
    {
        if ( $sectionLevel < $headerLevel )
        {
            if ( $this->IsStrictHeader )
            {
                $sectionLevel += 1;
            }
            else
            {
                if ( ( $sectionLevel + 1 ) == $headerLevel )
                {
                    $sectionLevel += 1;
                }
                else
                {
                    for ( $i=1;$i<=( $headerLevel - $sectionLevel - 1 );$i++ )
                    {
                        // Add section tag
                        unset( $subNode );
                        $subNode = new DOMElemenetNode( 'section' );
                        $currentNode->appendChild( $subNode );
                        $childTag = $this->SectionArray;
                        $TagStack[] = array( "TagName" => "section", "ParentNodeObject" => &$currentNode, "ChildTag" => $childTag );
                        $currentNode = $subNode;
                    }
                    $sectionLevel = $headerLevel;
                }
            }
        }
        elseif ( $sectionLevel == $headerLevel )
        {
            $lastNodeArray = array_pop( $TagStack );
            $lastNode = $lastNodeArray["ParentNodeObject"];
            $currentNode = $lastNode;
            $sectionLevel = $headerLevel;
        }
        else
        {
            for ( $i=1;$i<=( $sectionLevel - $headerLevel + 1 );$i++ )
            {
                $lastNodeArray = array_pop( $TagStack );
                $lastTag = $lastNodeArray["TagName"];
                $lastNode = $lastNodeArray["ParentNodeObject"];
                $lastChildTag = $lastNodeArray["ChildTag"];
                $currentNode = $lastNode;
            }
            $sectionLevel = $headerLevel;
        }
        return $currentNode;
    }

    /*!
     Returns the input XML representation of the datatype.
    */
    function inputXML( )
    {
        $node = null;
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->preserveWhiteSpace = false;
        $success = false;
        if ( $this->XMLData )
        {
            $success = $dom->loadXML( $this->XMLData );
        }

        eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $this->XMLData, 'eZTINYMCEXMLInput::inputXML xml string stored in database' );

        $output = '';

        if ( $success )
        {
            $rootSectionNode = $dom->documentElement;
            $output .= $this->inputSectionXML( $rootSectionNode, 0 );
        }

        if ( $this->browserType === 'IE' )
        {
            $output = str_replace( '<p></p>', '<p>&nbsp;</p>', $output );
        }
        else
        {
            $output = str_replace( '<p></p>', '<p><br /></p>', $output );
        }

        $output = str_replace( "\n", '', $output );

        if ( $this->browserType === 'IE' )
        {
            $output .= '<p>&nbsp;</p>';
        }
        else
        {
            $output .= '<p><br /></p>';
        }

        eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $output, 'eZTINYMCEXMLInput::inputXML xml output to return' );

        $output = htmlspecialchars( $output );

        return $output;
    }

    /*!
     \private
     \return the user input format for the given section
    */
    function &inputSectionXML( &$section, $currentSectionLevel, $tdSectionLevel = null )
    {
        $output = '';
        $imgName = 'anchor_image.gif';

        $imgSrc = extension_path( 'eztinymce', true, true, true );
        $imgSrc .= '/design/standard/images/ezdhtml/' . $imgName;

        foreach ( $section->childNodes as $sectionNode )
        {
            if ( $tdSectionLevel == null )
            {
                $sectionLevel = $currentSectionLevel;
            }
            else
            {
                $sectionLevel = $tdSectionLevel;
                $currentSectionLevel = $currentSectionLevel;
            }

            $tagName = $sectionNode instanceof DOMNode ? $sectionNode->nodeName : '';

            switch ( $tagName )
            {
                case 'header' :
                {
                    $level = $sectionLevel;
                    $headerClassName = $sectionNode->getAttribute( 'class' );

                    $headerClassString = $headerClassName != null ? " class='$headerClassName'" : '';

                    $tagContent = '';
                    // render children tags
                    $tagChildren = $sectionNode->childNodes;
                    foreach ( $tagChildren as $childTag )
                    {
                        $tagContent .= $this->inputTagXML( $childTag, $currentSectionLevel, $tdSectionLevel );
                        eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $tagContent, 'eZTINYMCEXMLInput::inputSectionXML tag content of header' );

                    }

                    switch ( $level )
                    {
                        case "2":
                        case "3":
                        case "4":
                        case "5":
                        case "6":
                        {
                            $archorName = $sectionNode->getAttribute( 'anchor_name' );
                            if ( $archorName != null )
                            {
                                $output .= "<h$level$headerClassString><img src=\"$imgSrc\" name=\"$archorName\" type=\"anchor\" />" . $sectionNode->textContent. "</h$level>";
                            }
                            else
                            {
                                $output .= "<h$level$headerClassString>" . $tagContent . "</h$level>";
                            }
                        }break;

                        default:
                        {
                            $archorName = $sectionNode->getAttribute( 'anchor_name' );
                            if ( $archorName != null )
                            {
                                $output .= "<h1$headerClassString><img src=\"$imgSrc\" name=\"$archorName\" type=\"anchor\" />" . $sectionNode->textContent. "</h1>";
                            }
                            else
                            {
                                $output .= "<h1$headerClassString>" . $tagContent . "</h1>";
                            }
                        }break;
                    }

                }break;

                case 'paragraph' :
                {
                    if ( $tdSectionLevel == null )
                    {
                        $output .= $this->inputParagraphXML( $sectionNode, $currentSectionLevel );
                    }
                    else
                    {
                        $output .= $this->inputParagraphXML( $sectionNode, $currentSectionLevel, $tdSectionLevel );
                    }
                }break;

                case 'section' :
                {
                    $sectionLevel += 1;
                    if ( $tdSectionLevel == null )
                    {
                        $output .= $this->inputSectionXML( $sectionNode, $sectionLevel );
                    }
                    else
                    {
                        $output .= $this->inputSectionXML( $sectionNode, $currentSectionLevel, $sectionLevel );
                    }
                }break;

                default :
                {
                    $debug = eZDebug::instance();
                    $debug->writeError( "Unsupported tag at this level: $tagName", "eZXMLTextType::inputSectionXML()" );
                }break;
            }
        }
        return $output;
    }

    /*!
     \private
     \return the user input format for the given table cell
    */
    function &inputListXML( &$listNode, $currentSectionLevel, $listSectionLevel = null, $noParagraphs = true )
    {
        $output = '';
        $tagName = $listNode instanceof DOMNode ? $listNode->nodeName : '';

        switch ( $tagName )
        {
            case 'paragraph' :
            {
                $output .= $this->inputParagraphXML( $listNode, $currentSectionLevel, $listSectionLevel, $noParagraphs );
            }break;

            case 'section' :
            {
                $listSectionLevel += 1;
                $output .= $this->inputSectionXML( $tdNode, $currentSectionLevel, $listSectionLevel );
            }break;

            default :
            {
                $debug = eZDebug::instance();
                $debug->writeError( "Unsupported tag at this level: $tagName", "eZXMLTextType::inputListXML()" );
            }break;
        }
        return $output;
    }

    /*!
     \private
     \return the user input format for the given section
    */
    function &inputTdXML( &$tdNode, $currentSectionLevel, $tdSectionLevel = null )
    {
        $output = '';
        $tagName = $tdNode instanceof DOMNode ? $tdNode->nodeName : '';

        switch ( $tagName )
        {
            case 'paragraph' :
            {
                $output .= $this->inputParagraphXML( $tdNode, $currentSectionLevel, $tdSectionLevel  );
            }break;

            case 'section' :
            {
                $tdSectionLevel += 1;
                $output .= $this->inputSectionXML( $tdNode, $currentSectionLevel, $tdSectionLevel );
            }break;

            default :
            {
                $debug = eZDebug::instance();
                $debug->writeError( "Unsupported tag at this level: $tagName", "eZXMLTextType::inputTdXML()" );
            }break;
        }
        return $output;
    }

    /*!
     \return the input xml of the given paragraph
    */
    function &inputParagraphXML( &$paragraph, $currentSectionLevel, $tdSectionLevel = null, $noRender = false )
    {
        $output = '';
        $children = $paragraph->childNodes;
        if ( $noRender )
        {
            foreach ( $children as $child )
            {
                $output .= $this->inputTagXML( $child, $currentSectionLevel, $tdSectionLevel );
            }
            return $output;
        }

        $paragraphClassName = $paragraph->getAttribute( 'class' );

        $customAttributePart = $this->getCustomAttrPart( $paragraph );

        if ( $paragraphClassName != null )
        {
            $openPara = "<p class='$paragraphClassName'$customAttributePart>";
        }
        else
        {
            $openPara = "<p$customAttributePart>";
        }
        $closePara = '</p>';

        if ( $children->length == 0 )
        {
            $output = $openPara . $closePara;
            return $output;
        }

        $lastChildInline = null;
        $innerContent = '';
        foreach ( $children as $child )
        {
            $childOutput = $this->inputTagXML( $child, $currentSectionLevel, $tdSectionLevel );

            $inline = !( $child->nodeName === 'ul' || $child->nodeName === 'ol');
            if ( $inline )
            {
                $innerContent .= $childOutput;
            }


            if ( ( !$inline && $lastChildInline ) ||
                 ( $inline && !$child->nextSibling ) )
            {
                $output .= $openPara . $innerContent . $closePara;
                $innerContent = '';
            }

            if ( !$inline )
            {
                $output .= $childOutput;
            }

            $lastChildInline = $inline;
        }

        eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $output, 'eZTINYMCEXMLInput::inputParagraphXML output' );
        return $output;
    }

    function getCustomAttrPart( $tag )
    {
        $customAttributePart = "";
        $customAttributes = array();

        foreach ( $tag->attributes as $attribute )
        {
            if ( $attribute->namespaceURI == 'http://ez.no/namespaces/ezpublish3/custom/' )
            {
                if ( $customAttributePart == '' )
                {
                    $customAttributePart = " customattributes=\"";
                    $customAttributePart .= $attribute->name . "|" . $attribute->value;
                }
                else
                {
                   $customAttributePart .= 'attribute_separation' . $attribute->name . "|" . $attribute->value;
                }
            }
        }

        if ( $customAttributePart != '' )
        {
            $customAttributePart .= "\"";
        }
        return $customAttributePart;
    }

    /*!
     \return the input xml for the given tag
    */
    function &inputTagXML( &$tag, $currentSectionLevel, $tdSectionLevel = null )
    {
        $output = '';
        $tagName = $tag->nodeName;
        $childTagText = '';
        // render children tags
        if ( $tag->hasChildNodes() )
        {
            $tagChildren = $tag->childNodes;
            foreach ( $tagChildren as $childTag )
            {
                $childTagText .= $this->inputTagXML( $childTag, $currentSectionLevel, $tdSectionLevel );
            }
        }
        switch ( $tagName )
        {
            case '#text' :
            {
                //$tagContent = htmlspecialchars( $tag->textContent );
                $tagContent = $tag->textContent;
                if ( !strlen( $tagContent ) )
                {
                    break;
                }

                $tagContent = htmlspecialchars( $tagContent );

                if ( $this->allowMultipleSpaces )
                {
                    $tagContent = str_replace( "  ", " &nbsp;", $tagContent );
                }
                else
                {
                    $tagContent = preg_replace( "/ {2,}/", " ", $tagContent );
                }

                if ( $tagContent[0] == ' ' )
                {
                    $tagContent[0] = ';';
                    $tagContent = '&nbsp' . $tagContent;
                }

                $output .= $tagContent;

            }break;

            case 'object' :
            {
                $size = "";
                $view = $tag->getAttribute( 'view' );
                $size = $tag->getAttribute( 'size' );
                $alignment = $tag->getAttribute( 'align' );
                $className = $tag->getAttribute( 'class' );
                $optionalLinkParameters = "";
                $hasLink = false;
                $linkID = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_id' );

                if ( $linkID !== null )
                {
                    $href = eZURL::url( $linkID );
                    $target = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_target' );
                    if ( $target === null )
                    {
                        $target = '_self';
                    }
                    $title = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_title' );
                    $id = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_linkid' );
                    $hasLink = true;
                    if ( $title !== null )
                    {
                        $optionalLinkParameters .= ' title=' . $title;
                    }
                    if ( $id !== null )
                    {
                        $optionalLinkParameters .= ' id=' . $id;
                    }
                }

                $customAttributePart = $this->getCustomAttributePart( $tag );;

                if ( strlen( $view ) === 0 )
                {
                    $view = 'embed';
                }
                $srcString = '';
                $objectID = $tag->getAttribute( 'id' );
                $object = eZContentObject::fetch( $objectID );

                if ( $object != null )
                {
                    $objectName = $object->attribute( 'name' );
                    $classID = $object->attribute( 'contentclass_id' );
                }
                else
                {
                    $objectName = 'Unknown';
                    $classID = 0;
                }

                $URL = serverURL();

                $ini = eZINI::instance( 'site.ini' );
                $imageClassArray = $ini->variable('MediaClassSettings', 'ImageClassID' );
                $imageDatatypeArray = $ini->variable( 'ImageDataTypeSettings', 'AvailableImageDataTypes' );
                if ( in_array( $classID, $imageClassArray ) )
                {
                    $contentObjectAttributes = $object->contentObjectAttributes();
                    foreach ( $contentObjectAttributes as $contentObjectAttribute )
                    {
                        $classAttribute = $contentObjectAttribute->contentClassAttribute();
                        $dataTypeString = $classAttribute->attribute( 'data_type_string' );
                        if ( in_array ( $dataTypeString, $imageDatatypeArray ) )
                        {
                            $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
                            $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );
                            $content = $contentObjectAttribute->content();
                            if ( $content !== null )
                            {
                                if ( $size == "" )
                                {
                                    $size = "medium";
                                }
                                $imageAlias = $content->imageAlias( $size );
                                $srcString = $URL . '/' . $imageAlias['url'];
                            }
                            else
                            {
                                $srcString = '';
                            }
                        }
                    }
                }
                else
                {
                    $srcString = $URL . "/";
                    $srcString .= extension_path( 'eztinymce', false, false, false );
                    $srcString .= '/design/standard/images/ezdhtml/object_insert.png';
                }
                if ( $className !== null )
                {
                    if ( $size == "" )
                    {
                        $output .= "<img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" class='$className' $customAttributePart />";
                    }
                    else if ( $size != '' and $hasLink )
                    {
                        $output .= "<a href='$href' target='$target'$optionalLinkParameters><img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" alt=\"$size\" class='$className' $customAttributePart /></a>";
                    }
                    else
                    {
                        $output .= "<img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" alt=\"$size\" class='$className' $customAttributePart />";
                    }
                }
                else
                {
                    if ( $size == '' )
                    {
                        $output .= "<img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" $customAttributePart />";
                    }
                    else if ( $size != '' and $hasLink )
                    {
                        $output .= "<a href='$href' target='$target'$optionalLinkParameters><img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" alt=\"$size\" $customAttributePart /></a>";
                    }
                    else
                    {
                        $output .= "<img id=\"eZObject_$objectID\" src=\"$srcString\" align=\"$alignment\" alt=\"$size\" $customAttributePart />";
                    }
                }
            }break;

            case 'embed' :
            case 'embed-inline' :
            {
                $view = $tag->getAttribute( 'view' );
                $size = $tag->getAttribute( 'size' );
                $alignment = $tag->getAttribute( 'align' );
                if ( !$alignment )
                {
                    $alignment = 'right';
                }

                $objectID = $tag->getAttribute( 'object_id' );
                $nodeID = $tag->getAttribute( 'node_id' );
                $showPath = $tag->getAttribute( 'show_path' );
                $htmlID = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'id' );
                $className = $tag->getAttribute( 'class' );
                $linkToObject = true;

                $objectAttr = '';

                if ( $size != null )
                {
                    $objectAttr .= " alt='$size'";
                }
                else
                {
                    $objectAttr .= " alt='medium'";
                    $size ='medium';
                }
                if ( $alignment != null )
                {
                    $objectAttr .= " align='$alignment'";
                }
                if ( $view != null )
                {
                    $objectAttr .= " view='$view'";
                }
                if ( $htmlID != '' )
                {
                    $objectAttr .= " html_id='$htmlID'";
                }
                if ( $className != '' )
                {
                    $objectAttr .= " class='$className'";
                }
                if ( $showPath == 'true' )
                {
                    $objectAttr .= " show_path='true'";
                }

                if ( $tagName == 'embed-inline' )
                {
                    $objectAttr .= " inline='true'";
                }
                else
                {
                    $objectAttr .= " inline='false'";
                }

                $customAttributePart = $this->getCustomAttrPart( $tag );

                if ( is_numeric( $objectID ) )
                {
                    $object = eZContentObject::fetch( $objectID );
                }
                elseif ( is_numeric( $nodeID ) )
                {
                    $linkToObject = false;
                    $object = eZContentObject::fetchByNodeID( $nodeID );
                }

                if ( $object != null )
                {
                    $objectName = $object->attribute( 'name' );
                    $classID = $object->attribute( 'contentclass_id' );
                }
                else
                {
                    $objectName = 'Unknown';
                    $classID = 0;
                }

                $URL = serverURL();

                $ini = eZINI::instance( 'site.ini' );
                $imageClassArray = $ini->variable('MediaClassSettings', 'ImageClassID' );
                $imageDatatypeArray = $ini->variable( 'ImageDataTypeSettings', 'AvailableImageDataTypes' );
                if ( in_array( $classID, $imageClassArray ) )
                {
                    $contentObjectAttributes = $object->contentObjectAttributes();
                    foreach ( $contentObjectAttributes as $contentObjectAttribute )
                    {
                        $classAttribute = $contentObjectAttribute->contentClassAttribute();
                        $dataTypeString = $classAttribute->attribute( 'data_type_string' );
                        if ( in_array ( $dataTypeString, $imageDatatypeArray ) )
                        {
                            $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
                            $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );
                            $content = $contentObjectAttribute->content();
                            if ( $content != null )
                            {
                                $imageAlias = $content->imageAlias( $size );
                                $srcString = $URL . '/' . $imageAlias['url'];
                            }
                            else
                            {
                                $srcString = '';
                            }
                        }
                    }
                }
                else
                {
                    $srcString = $URL . "/";
                    $srcString .= extension_path( 'eztinymce', false, false, false );
                    $srcString .= '/design/standard/images/ezdhtml/object_insert.png';
                }

                if ( $linkToObject )
                {
                    $output .= "<img id=\"eZObject_$objectID\" src=\"$srcString\" $objectAttr$customAttributePart />";
                }
                else
                {
                    $output .= "<img id=\"eZNode_$nodeID\" src=\"$srcString\" $objectAttr$customAttributePart />";
                }
            }break;

            case 'anchor' :
            {
                $name = $tag->getAttribute( 'name' );
                $imgName = "anchor_image.gif";

                $customAttributePart = $this->getCustomAttrPart( $tag );

                $src = imagePath( $imgName );
                $output .= "<img src=\"$src\" name=\"$name\" type=\"anchor\"$customAttributePart/>";
            }break;

            case 'custom' :
            {
                $name = $tag->getAttribute( 'name' );

                $customAttributePart = $this->getCustomAttrPart( $tag );

                $isInline = false;
                include_once( "lib/ezutils/classes/ezini.php" );
                $ini = eZINI::instance( 'content.ini' );

                $isInlineTagList = $ini->variable( 'CustomTagSettings', 'IsInline' );
                foreach ( $isInlineTagList as $key => $isInlineTagValue )
                {
                    if ( $isInlineTagValue && $name === $key && $isInlineTagValue !== 'false' )
                    {
                        $isInline = true;
                    }
                }

                if ( $isInline )
                {
                    $imgName = "customtag_insert.gif";

                    if ( !$childTagText )
                    {
                        $src = imagePath( $imgName );
                        $output .= "<img src=\"$src\" name=\"$name\" value=\"\" type=\"custom\"$customAttributePart />";
                    }
                    else
                    {
                        $output .= "<span class=\"$name\" name=\"$name\" type=\"custom\"$customAttributePart>$childTagText</span>";
                    }
                }
                else
                {
                    $customTagContent = "";
                    foreach ( $tag->childNodes as $tagChild )
                    {
                        $customTagContent .= $this->inputTdXML( $tagChild, $currentSectionLevel, $tdSectionLevel );
                    }
                    $output .= "<table id='custom' class='custom' title='$name' width='100%' border='1'$customAttributePart><tr><td class='" . $name . "'>$customTagContent</td></tr></table>";
                }
            }break;

            case 'literal' :
            {
                $literalText = "";
                foreach ( $tagChildren as $childTag )
                {
                    $literalText .= $childTag->textContent;
                }
                $className = $tag->getAttribute( 'class' );

                $customAttributePart = $this->getCustomAttrPart( $tag );

                $literalText = htmlspecialchars( $literalText );
                $literalText = str_replace( "  ", " &nbsp;", $literalText );
                $literalText = str_replace( "\n\n", "</p><p>", $literalText );
                $literalText = str_replace( "\n", "<br>", $literalText );

                if ( $className == null )
                {
                    $output .= "<table id='literal' width='100%' border='1' class='literal'$customAttributePart><tr><td><p>$literalText</p></td></tr></table>";
                }
                else
                {
                    $output .= "<table id='literal' width='100%' border='1' class='literal' title='$className'$customAttributePart><tr><td class='$className'><p>$literalText</p></td></tr></table>";
                }
            }break;

            case 'ul' :
            case 'ol' :
            {
                $listContent = "";

                $customAttributePart = $this->getCustomAttrPart( $tag );

                // find all list elements
                foreach ( $tag->childNodes as $listItemNode )
                {
                    $LIcustomAttributePart = $this->getCustomAttrPart( $listItemNode );

                    $noParagraphs = $listItemNode->childNodes->length <= 1;
                    $listItemContent = "";
                    foreach ( $listItemNode->childNodes as $itemChildNode )
                    {
                        $listSectionLevel = $currentSectionLevel;
                        if ( $itemChildNode->nodeName == "section" or $itemChildNode->nodeName == "paragraph" )
                        {
                            $listItemContent .= $this->inputListXML( $itemChildNode, $currentSectionLevel, $listSectionLevel, $noParagraphs );
                        }
                        else
                        {
                            $listItemContent .= $this->inputTagXML( $itemChildNode, $currentSectionLevel, $tdSectionLevel );
                        }
                    }

                    $listContent .= "<li$LIcustomAttributePart>$listItemContent</li>";
                }
                $className = $tag->getAttribute( 'class' );
                if ( $className != null )
                {
                    $output .= "<$tagName class='$className'$customAttributePart>$listContent</$tagName>";
                }
                else
                {
                    $output .= "<$tagName$customAttributePart>$listContent</$tagName>";
                }
            }break;

            case 'table' :
            {
                $tableRows = "";
                $border = $tag->getAttribute( 'border' );
                $width = $tag->getAttribute( 'width' );
                $tableClassName = $tag->getAttribute( 'class' );

                $customAttributePart = $this->getCustomAttrPart( $tag );

                // find all table rows
                foreach ( $tag->childNodes as $tableRow )
                {
                    $TRcustomAttributePart = $this->getCustomAttrPart( $tableRow );

                    $tableData = "";
                    foreach ( $tableRow->childNodes as $tableCell )
                    {
                        $TDcustomAttributePart = $this->getCustomAttrPart( $tableCell );

                        $cellAttribute = "";
                        $className = $tableCell->getAttribute( 'class' );

                        $colspan = $tableCell->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'colspan' );
                        $rowspan = $tableCell->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'rowspan' );
                        $cellWidth = $tableCell->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'width' );
                        if ( $className != null )
                        {
                            $cellAttribute .= " class='$className'";
                        }
                        if ( $cellWidth != null )
                        {
                            $cellAttribute .= " width='$cellWidth'";
                        }
                        if ( $colspan != null )
                        {
                            $cellAttribute .= " colspan='$colspan'";
                        }
                        if ( $rowspan != null )
                        {
                            $cellAttribute .= " rowspan='$rowspan'";
                        }
                        $cellContent = "";
                        if ( $tableCell->nodeName == "th" )
                        {
                            $tdSectionLevel = $currentSectionLevel;
                            foreach ( $tableCell->childNodes as $tableCellChildNode )
                            {
                                $cellContent .= $this->inputTdXML( $tableCellChildNode, $currentSectionLevel, $tdSectionLevel - $currentSectionLevel );
                            }
                            $tableData .= "<th" . $cellAttribute . $TDcustomAttributePart . ">" . $cellContent . "</th>";

                        }
                        else
                        {
                            $tdSectionLevel = $currentSectionLevel;
                            foreach ( $tableCell->childNodes as $tableCellChildNode )
                            {
                                $cellContent .= $this->inputTdXML( $tableCellChildNode, $currentSectionLevel, $tdSectionLevel - $currentSectionLevel );
                            }
                            $tableData .= "<td" . $cellAttribute . $TDcustomAttributePart . ">" . $cellContent . "</td>";
                        }
                    }
                    $tableRows .= "<tr$TRcustomAttributePart>$tableData</tr>";
                }
                if ( $this->browserType === 'IE' )
                {
                    $widthAttribute = "width='$width'";
                }
                else
                {
                    $widthAttribute = "style='width: $width;'";
                }

                if ( is_string( $border ) )
                {
                    $borderAttribute = "ezborder='$border'";
                    if ( $border == 0 )
                    {
                        $borderAttribute .= " border='1' bordercolor='red'";
                    }
                    else
                    {
                        $borderAttribute .= " border='$border'";
                    }
                }
                else
                {
                    $borderAttribute = "";
                }

                if ( $tableClassName != null )
                {
                    $classAttribute = "class='$tableClassName'";
                }
                else
                {
                    $classAttribute = "";
                }

                $output .= "<table id='table' $classAttribute $widthAttribute $borderAttribute$customAttributePart>$tableRows</table>";
            }break;

            // normal content tags
            case 'emphasize' :
            {
                $customAttributePart = $this->getCustomAttrPart( $tag );

                $className = $tag->getAttribute( 'class' );
                if ( $className != null )
                {
                    $output .= "<i class='$className'$customAttributePart>" . $childTagText . "</i>";
                }
                else
                {
                    $output .= "<i>" . $childTagText  . "</i>";
                }
            }break;

            case 'strong' :
            {
                $customAttributePart = $this->getCustomAttrPart( $tag );

                $className = $tag->getAttribute( 'class' );
                if ( $className != null )
                {
                    $output .= "<b class='$className'$customAttributePart>" . $childTagText . "</b>";
                }
                else
                {
                    $output .= "<b>" . $childTagText . "</b>";
                }
            }break;

            case 'line' :
            {
                $output .= $childTagText . "<br />";
            }break;

            case 'link' :
            {
                $customAttributePart = $this->getCustomAttrPart( $tag );

                $linkID = $tag->getAttribute( 'url_id' );

                $target = $tag->getAttribute( 'target' );
                $className = $tag->getAttribute( 'class' );
                $viewName = $tag->getAttribute( 'view' );
                $objectID = $tag->getAttribute( 'object_id' );
                $nodeID = $tag->getAttribute( 'node_id' );
                $anchorName = $tag->getAttribute( 'anchor_name' );
                $showPath = $tag->getAttribute( 'show_path' );
                $htmlID = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'id' );
                $htmlTitle = $tag->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/xhtml/', 'title' );

                if ( $objectID != null )
                {
                    $href = 'ezobject://' .$objectID;
                }
                elseif ( $nodeID != null )
                {
                    if ( $showPath == 'true' )
                    {
                        $node = eZContentObjectTreeNode::fetch( $nodeID );
                        $href = $node ? 'eznode://' . $node->attribute('path_identification_string') : 'eznode://' . $nodeID;
                    }
                    else
                    {
                        $href = 'eznode://' . $nodeID;
                    }
                }
                elseif ( $linkID != null )
                {
                    $href = eZURL::url( $linkID );
                }
                else
                {
                    $href = $tag->getAttribute( 'href' );
                }

                if ( $anchorName != null )
                {
                    $href .= '#' .$anchorName;
                }

                $attributes = array();
                if ( $className != '' )
                {
                    $attributes[] = "class='$className'";
                }
                if ( $viewName != '' )
                {
                    $attributes[] = "view='$viewName'";
                }

                $attributes[] = "href='$href'";
                if ( $target != '' )
                {
                    $attributes[] = "target='$target'";
                }
                if ( $htmlTitle != '' )
                {
                    $attributes[] = "title='$htmlTitle'";
                }
                if ( $htmlID != '' )
                {
                    $attributes[] = "id='$htmlID'";
                }

                $attributeText = '';
                if ( count( $attributes ) > 0 )
                {
                    $attributeText = ' ' .implode( ' ', $attributes );
                }
                $output .= "<a$attributeText$customAttributePart>" . $childTagText . "</a>";
            }break;
            case 'tr' :
            case 'td' :
            case 'th' :
            case 'li' :
            case 'paragraph' :
            {
            }break;
            default :
            {

            }break;
        }
        return $output;
    }


    public $LineTagArray = array( 'emphasize', 'strong', 'link', 'a', 'em', 'i', 'b', 'bold','anchor' );
    /// Contains the XML data
    public $XMLData;

    public $ContentObjectAttributeID;
    public $ContentObjectAttributeVersion;

    public $IsStrictHeader = false;
    public $SectionArray = array(  "h1", "h2", "h3", "h4", "h5", "h6", "p", "section" );

    public $browserType;
    public $eZPublishVersion;

    public $trimSpaces = true;
    public $allowMultipleSpaces = false;
}

?>
