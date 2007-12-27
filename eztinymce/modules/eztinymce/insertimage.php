<?php
//
// Created on: <5-Jul-2007 00:00:00 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ TinyMce extension for eZ Publish
// SOFTWARE RELEASE: 1.0
// COPYRIGHT NOTICE: Copyright (C) 2007 eZ systems AS
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

include_once( 'kernel/common/template.php' );
//include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
include_once( 'extension/eztinymce/classes/ezajaxcontent.php' );


//$menuINI =& eZINI::instance( 'contentstructuremenu.ini' );
//$classIconsSize = $menuINI->variable( 'TreeMenu', 'ClassIconsSize' );

$Module =& $Params['Module'];

$objectID      = isset( $Params['ObjectID'] ) ? (int) $Params['ObjectID'] : null;
$objectVersion = isset( $Params['ObjectVersion'] ) ? (int) $Params['ObjectVersion'] : null;

if ( isset( $Params['EmbedID'] )  && $Params['EmbedID'])
{
    // TODO: Support for existing relations
    echo $Params['EmbedID'];
    eZExecution::cleanExit();
}
    
if ( !$objectID  || !$objectVersion )
{
   echo "Missing Object ID or Object version";
   eZExecution::cleanExit();
}



$object    = eZContentObject::fetch( $objectID );
$http      = eZHTTPTool::instance();
$imageIni  = eZINI::instance( 'image.ini' );
$aliasList = $imageIni->hasVariable( 'AliasSettings', 'AliasList' ) ? $imageIni->variable( 'AliasSettings', 'AliasList' ) : false;

$params = array('dataMap' => array('image'));

if ( $aliasList )
{
    foreach ( $aliasList as $alias )
    {
        if ( $alias !== 'original' )
            $params['imgSizes'][] = $alias;
    }
}

$params['imgSizes'][] = 'original';


if ( !$object )
{
   echo "Object fetch returned false! &nbsp; ObjectId: " . $objectID;
   eZExecution::cleanExit();
}


if ( $http->hasPostVariable( 'uploadButton' ) )
{
    include_once( 'kernel/classes/ezcontentupload.php' );
    $upload = new eZContentUpload();
    $location = false;
    if ( $http->hasPostVariable( 'location' ) )
    {
        $location = $http->postVariable( 'location' );
        if ( $location === 'auto' || trim( $location ) === '' ) $location = false;
    }

    $objectName = '';
    if ( $http->hasPostVariable( 'objectName' ) )
    {
        $objectName = trim( $http->postVariable( 'objectName' ) );
    }

    $uploadedOk = $upload->handleUpload( $result, 'fileName', $location, false, $objectName );


    if ( $uploadedOk )
    {
        $newObject = $result['contentobject'];
        $newObjectID = $newObject->attribute( 'id' );
        $object->addContentObjectRelation( $newObjectID, $objectVersion, false, 0, eZContentObject::RELATION_EMBED );
        echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
        $json = eZAjaxContent::encode( $newObject, $params );
        echo 'window.parent.postUpload(' . $json . ');';
        // todo: create the image js object
        
        echo '</script></head><body></body></html>';

    }
    else
    {
        $errors = $result['errors'];
        echo var_export( $errors , true);
    }
    eZExecution::cleanExit();
}



$aliasList     = null;
$sizeTypeArray = array();
$siteIni       = eZINI::instance( 'site.ini' );
$contentIni    = eZINI::instance( 'content.ini' );

$groups             = $contentIni->variable( 'RelationGroupSettings', 'Groups' );
$defaultGroup       = $contentIni->variable( 'RelationGroupSettings', 'DefaultGroup' );
$imageDatatypeArray = $siteIni->variable( 'ImageDataTypeSettings', 'AvailableImageDataTypes' );

$classGroupMap         = array();
$groupClassLists       = array();
$groupedRelatedObjects = array();
$relatedObjects        = $object->relatedContentObjectArray( $objectVersion );

foreach ( $groups as $groupName )
{
    $groupedRelatedObjects[$groupName] = array();
    $setting                     = strtoupper( $groupName[0] ) . substr( $groupName, 1 ) . 'ClassList';
    $groupClassLists[$groupName] = $contentIni->variable( 'RelationGroupSettings', $setting );
    foreach ( $groupClassLists[$groupName] as $classIdentifier )
    {
        $classGroupMap[$classIdentifier] = $groupName;
    }
}

$groupedRelatedObjects[$defaultGroup] = array();

foreach ( $relatedObjects as $relatedObjectKey => $relatedObject )
{
    $srcString        = '';
    $relID            = $relatedObject->attribute( 'id' );
    $objectIsSelected = ( count( $relatedObjects ) == 1 || $relID == $newObjectID );
    $classIdentifier  = $relatedObject->attribute( 'class_identifier' );
    $groupName        = isset( $classGroupMap[$classIdentifier] ) ? $classGroupMap[$classIdentifier] : $defaultGroup;
    
    if ($groupName === 'images')
    {
        $contentObjectAttributes = $relatedObject->contentObjectAttributes();
        
        foreach ( $contentObjectAttributes as $contentObjectAttribute )
        {
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( in_array ( $classAttribute->attribute( 'data_type_string' ), $imageDatatypeArray ) )
            {
                $content = $contentObjectAttribute->content();
                if ( $content != null )
                {
                    $imageAlias = $content->imageAlias( 'small' );
                    $srcString = $URL . '/' . $imageAlias['url'];
                    foreach ( $sizeTypeArray as $sizeType )
                        $imageAlias = $content->attribute( $sizeType );

                    break;
                }
            }
        }
    }
    $item = array( 'object' => $relatedObjects[$relatedObjectKey],
                   'id' => 'eZObject_' . $relID,
                   'img' => $srcString,
                   'selected' => $objectIsSelected );
    $groupedRelatedObjects[$groupName][] = $item;
}

$tpl =& templateInit();
$tpl->setVariable( 'object', $object );
$tpl->setVariable( 'objectID', $objectID );
$tpl->setVariable( 'ObjectVersion', $ObjectVersion );
$tpl->setVariable( 'size_type_list', $sizeTypeArray );
$tpl->setVariable( 'related_contentobjects', $relatedObjects );
$tpl->setVariable( 'grouped_related_contentobjects', $groupedRelatedObjects );

$defaultSize = $contentIni->variable( 'ImageSettings', 'DefaultEmbedAlias' );
$tpl->setVariable( "default_size", $defaultSize );

echo $tpl->fetch( 'design:eztinymce/advimage.tpl' );


eZExecution::cleanExit();
//$GLOBALS['show_page_layout'] = false;
