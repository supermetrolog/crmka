<?php


namespace app\services\filemanager;

class FileManager
{
    public static function UnlinkFiles(string $path, array $except = [])
    {
        $files = glob($path . '/*');
        foreach ($files as $fullname) {
            $filename = str_replace($path . '/', '', $fullname);
            if (!in_array($filename, $except)) {
                unlink($fullname);
            }
        }
    }
}
