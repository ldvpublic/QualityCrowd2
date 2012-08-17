<?php

function rcopy($path, $dest, $dmode = 0755, $fmode = 0644)
{
    if(is_dir($path))
    {
        @mkdir($dest);
        @chmod($dest, $dmode);
        $objects = scandir($path);
        if(sizeof($objects) > 0)
        {
            foreach($objects as $file)
            {
                if($file == "." || $file == ".." )
                    continue;
                // go on
                if(is_dir($path.DS.$file))
                {
                    rcopy($path.DS.$file, $dest.DS.$file, $dmode, $fmode);
                }
                else
                {
                    copy($path.DS.$file, $dest.DS.$file);
                    @chmod($dest.DS.$file, $fmode);
                }
            }
        }
        return true;
    }
    elseif(is_file($path))
    {
        $r = copy($path, $dest);
        @chmod($dest.DS.$file, $fmode);
        return $r;
    }
    else
    {
        return false;
    }
}

function rrmdir($dir) 
{   
    if (is_dir($dir)) 
    {
        $objects = scandir($dir);
        if(sizeof($objects) > 0)
        {
            foreach ($objects as $file) 
            {
                if ($file == "." || $file == "..") 
                    continue;
                
                if (is_dir($dir.DS.$file)) 
                {
                    rrmdir($dir.DS.$file);
                } else 
                {   
                    @chmod($dir.DS.$file, 0777);
                    unlink($dir.DS.$file);
                }
            }
        }
        
        @chmod($dir, 0777);
        rmdir($dir);
    }
}

function rchmod($path, $filemode, $dirmode) { 
    if (!is_dir($path))
    {
        return chmod($path, $filemode); 
    }

    $dh = opendir($path); 
    while (($file = readdir($dh)) !== false) 
    { 
        if($file != '.' && $file != '..') 
        { 
            $fullpath = $path.DS.$file; 
            if(is_link($fullpath)) {
                return false; 
            } elseif(!is_dir($fullpath) && !chmod($fullpath, $filemode)) {
                return false; 
            } elseif(!rchmod($fullpath, $filemode, $dirmode)) {
                return false;
            }
        }
    }

    closedir($dh);

    if(chmod($path, $dirmode)) {
        return true; 
    } else { 
        return false;
    }
}