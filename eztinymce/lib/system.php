<?php
//
// Created on: <08-Nov-2006 17:10:02 ks>
//
// Copyright (C) 1999-2006 eZ systems as. All rights reserved.
//

//include_once( "lib/ezutils/classes/ezini.php" );
//include_once( "lib/ezutils/classes/ezsys.php" );

// This function is needed for compatibility with 3.6/3.7 and 3.8 releases prior to 3.8.7
function imagePath( $imgName )
{
    $OEini = eZINI::instance( 'eztinymce.ini' );
    $withHost = true;
    if ( $OEini->hasVariable( 'SystemSettings', 'RelativeURL' ) &&
         $OEini->variable( 'SystemSettings', 'RelativeURL' ) === 'enabled' )
    {
        $withHost = false;
    }

    if ( $withHost )
    {
        // Check if SSL port is defined in site.ini
        $ini = eZINI::instance();
        $sslPort = 443;
        if ( $ini->hasVariable( 'SiteSettings', 'SSLPort' ) )
        {
            $sslPort = $ini->variable( 'SiteSettings', 'SSLPort' );
        }
            
        if ( eZSys::serverPort() == $sslPort )
            $protocol = 'https';
        else
            $protocol = 'http';

        $imgSrc = extension_path( 'eztinymce', true, true, $protocol );
    }
    else
        $imgSrc = '/' . extension_path( 'eztinymce' );

    $imgSrc .= '/design/standard/images/ezdhtml/' . $imgName;

    return $imgSrc;
}

function serverURL()
{
    $OEini = eZINI::instance( 'eztinymce.ini' );
    if ( $OEini->hasVariable( 'SystemSettings', 'RelativeURL' ) &&
         $OEini->variable( 'SystemSettings', 'RelativeURL' ) === 'enabled' )
    {
        return '';
    }

    $domain = eZSys::hostname();
    $protocol = 'http';
    
    // Default to https if SSL is enabled
    // Check if SSL port is defined in site.ini
    $sslPort = 443;
    $ini = eZINI::instance();
    if ( $ini->hasVariable( 'SiteSettings', 'SSLPort' ) )
    {
        $sslPort = $ini->variable( 'SiteSettings', 'SSLPort' );
    }
    
    if ( eZSys::serverPort() == $sslPort )
    {
        $protocol = 'https';
    }
    
    $URL = $protocol . '://' . $domain;
    $URL .= eZSys::wwwDir();

    return $URL;
}
