<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ Systems AS
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

class eztinymceInfo
{
    static function info()
    {
        return array( 'name'      => "eZ TinyMce Editor",
                      'version'   => "0.3",
                      'copyright' => "Copyright (C) 2007 eZ systems AS",
                      'license'   => "GNU General Public License v2.0",
                      'Includes the following third-party software' => array( 'Name' => "TinyMce Javascript HTML WYSIWYG editor",
                                                                              'Version' => "2.1.3",
                                                                              'Copyright' => "Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.",
                                                                              'License' => "GNU Lesser General Public License v2.1")
                    );
    }
}

?>