<?php
define('DS', DIRECTORY_SEPARATOR);

function copy_r( $path, $dest )
{
    if( is_dir($path) )
    {
        @mkdir( $dest );
        $objects = scandir($path);
        if( sizeof($objects) > 0 )
        {
            foreach( $objects as $file )
            {
                if( $file == "." || $file == ".." )
                    continue;
                // go on
                if( is_dir( $path.DS.$file ) )
                {
                    copy_r( $path.DS.$file, $dest.DS.$file );
                }
                else
                {
                    copy( $path.DS.$file, $dest.DS.$file );
                }
            }
        }
        return true;
    }
    elseif( is_file($path) )
    {
        return copy($path, $dest);
    }
    else
    {
        return false;
    }
}
