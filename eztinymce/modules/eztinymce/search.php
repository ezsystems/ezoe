<?php
//
// Created on: <30-Jul-2007 00:00:00 ar>
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

//include_once( 'kernel/classes/ezsearch.php' );
include_once( 'extension/eztinymce/classes/ezajaxcontent.php' );


$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'SearchStr' ) )
    $searchStr = trim( $http->postVariable( 'SearchStr' ) );
elseif ( isSet( $Params['SearchStr'] ) )
    $searchStr = trim( $Params['SearchStr'] );

$varName = '';
if ( $http->hasPostVariable( 'VarName' ))
    $varName = trim( $http->postVariable( 'VarName' ) );
elseif ( isSet( $Params['VarName'] ) )
    $varName = trim( $Params['VarName'] );

if ( $varName )
    $varName .= ' = ';
    

if ( !$searchStr )
{
    echo $varName . "false;";
    eZExecution::cleanExit();
}


$searchOffset = 0;
if ( $http->hasPostVariable( 'SearchOffset' ))
    $searchOffset = (int) $http->postVariable( 'SearchOffset' );
elseif ( isSet( $Params['SearchOffset'] ) )
    $searchOffset = (int) $Params['SearchOffset'];

$searchLimit = 5;
if ( $http->hasPostVariable( 'SearchLimit' ))
    $searchLimit = (int) $http->postVariable( 'SearchLimit' );
elseif ( isSet( $Params['SearchLimit'] ) )
    $searchLimit = (int) $Params['SearchLimit'];


//Preper the search params
$param = array( 'SearchOffset' => $searchOffset,
                'SearchLimit' => $searchLimit+1,
                'SortArray' => array('published', 0)
              );


// if no checkbox select class_attr first if valid
if ( $http->hasPostVariable( 'SearchContentClassAttributeID' ) )
    $param['SearchContentClassAttributeID'] = explode( ',', $http->postVariable( 'SearchContentClassAttributeID' ) );
elseif ( $http->hasPostVariable( 'SearchContentClassID' ) )
    $param['SearchContentClassID'] = explode( ',', $http->postVariable( 'SearchContentClassID' ) );
              
if ( $http->hasPostVariable( 'SearchSubTreeArray' ) )
    $param['SearchSubTreeArray'] = explode( ',', $http->postVariable( 'SearchSubTreeArray' ) );

if ( $http->hasPostVariable( 'SearchSectionID' ) )
    $param['SearchSectionID'] = explode( ',', $http->postVariable( 'SearchSectionID' ) );

if ( $http->hasPostVariable( 'SearchTimestamp' ) )
    $param['SearchTimestamp'] = explode( ',', $http->postVariable( 'SearchTimestamp' ) );

if ( isSet( $param['SearchTimestamp'][0] ) && !isSet( $param['SearchTimestamp'][1] ) )
    $param['SearchTimestamp'] = $param['SearchTimestamp'][0];

 
$searchList = eZSearch::search( $searchStr, $param );


if (!$searchList  || count($searchList["SearchResult"]) == 0)
{
    echo $varName . "false;";
    eZExecution::cleanExit();
}


$r = eZAjaxContent::encode( $searchList["SearchResult"], array('dataMap' => array('image') ) );


echo $varName . '{list:' . $r . ",\ncount:" . $searchList['SearchCount'] .
     ",\noffset:" . $searchOffset . ",\nlimit:" . $searchLimit . "\n};";

eZExecution::cleanExit();
//$GLOBALS['show_page_layout'] = false;

