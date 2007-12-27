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


$Module = array( "name" => 'eZtinymce' );

$ViewList = array();
$ViewList["insertimage"] = array(
    "ui_context" => 'edit',
    "script" => 'insertimage.php',
    "params" => array( 'ObjectID', 'ObjectVersion', 'EmbedID' )
    );
    
$ViewList["search"] = array(
    "script" => "search.php",
    'params' => array( 'SearchStr', 'SearchOffset', 'SearchLimit', 'VarName')
    );

$ViewList["expand"] = array(
    "script" => "expand.php",
    'params' => array('NodeID', 'CurrentID', 'VarName')
    );

?>
