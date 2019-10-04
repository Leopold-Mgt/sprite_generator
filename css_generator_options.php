<?php

// SCAN RECURSIVE DIRECTORY
function my_recursive($path)
{
    $arr_files = array();
    $handle = opendir($path);
    $ignore_files = array(".", "..");
    $path_prefix = ($path === ".") ? "" : $path . "/";

    while ($file = readdir($handle)) {
        if (in_array($file, $ignore_files)) {
            continue;
        }
        $file = $path_prefix . $file;
        if (is_dir($file) && $handle) {
            $subdir_content = my_recursive($file);
            $arr_files = array_merge($arr_files, $subdir_content);
        } else if (substr($file, -4) == ".png") {
            array_push($arr_files, $file);
        }
    }
    closedir($handle);
    return ($arr_files);
}

// RENAME IMAGE 'sprite.png'
function output_image($argv)
{
    $newname = $argv[2];
    rename("sprite.png", $newname . ".png");
    echo "'sprite.png' has been changed to '$newname.png'\n";
}

// RENAME STYLE 'style.css'
function output_style($argv)
{
    $newname = $argv[2];
    rename("style.css", $newname . ".css");
    echo "'style.css' has been changed to '$newname.css'\n";
}

// MANUEL
function manual($argv, $argc)
{
    if ($argc == 2 && ($argv[1] === "-m" || $argv[1] === "--manual")) {
        echo file_get_contents("manual");
    }
}

?>