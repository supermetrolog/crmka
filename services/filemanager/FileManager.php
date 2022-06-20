<?php


namespace app\services\filemanager;

class FileManager
{
    public static function UnlinkFiles($path)
    {
        array_map('unlink', glob($path . "/*"));
    }
}
