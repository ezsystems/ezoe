<?php
//
// Created on: <15-Aug-2007 00:00:00 ar>
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

//include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
include_once( 'extension/eztinymce/classes/ezajaxcontent.php' );


$nodeID    = (int) $Params['NodeID'];;
$currentID = (int) $Params['CurrentID']; //object id you want to hide ( for instance)
$varName   = trim( $Params['VarName'] );

if ( !$nodeID )
{
    echo 'No NodeID!';
    eZExecution::cleanExit();
}

if ( $varName )
    $varName .= ' = ';
    


$node = eZContentObjectTreeNode::fetch( $nodeID );

if (!$node)
{
    echo 'No Parent Node!';
    eZExecution::cleanExit();
}

$nodeArray = $node->subTree(  array( 'Depth' => 1,
        'Limit'            => $maxNodes,
        'Offset'           => 0,
        'SortBy'           => $node->attribute( 'sort_array' ),
		'DepthOperator'    => 'eq',
        'ClassFilterType'  => 'include',
        'ClassFilterArray' => $showClasses
		));


if (!$nodeArray)
{
	echo 'No Child Nodes!';
    eZExecution::cleanExit();
}

$r = eZAjaxContent::encode( $nodeArray );


echo $varName . '{list:' . $r . ",\ncount:" . $searchList['SearchCount'] .
     ",\noffset:" . 0 . ",\nlimit:" . 0 . "\n};";


eZExecution::cleanExit();
//$GLOBALS['show_page_layout'] = false;
