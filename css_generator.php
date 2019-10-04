<?php

include 'css_generator_options.php';

// CSS FILE GENERATED
function css_generator($arr_files, $path)
{
    $j = 1;
    $fopen = fopen("$path/style.css", 'w+');
    fwrite($fopen, ".sprite { background-image: url(spritesheet.png); background-repeat: no-repeat; display: block; }\n\n");
    foreach ($arr_files as $file) {
        $img = imagecreatefrompng($path . "/" . $file);
        $img_width = imagesx($img);
        $img_height = imagesy($img);
        fwrite($fopen, ".sprite-" . $j . " { width: " . $img_width . "px ; height: " . $img_height . "px; } \n\n");
        $j++;
    }
}

// SPRITE MERGE
function sprite_merge($arr_files, $path, $sprite)
{
    $x_pos = 0;

    foreach ($arr_files as $file) {
        $img = imagecreatefrompng($path . "/" . $file);
        $img_width = imagesx($img);
        $img_height = imagesy($img);
        imagecopymerge($sprite, $img, $x_pos, 0, 0, 0, $img_width, $img_height, 100);
        imagedestroy($img);
        $x_pos += $img_width;
    }
    imagepng($sprite, "$path/sprite.png");
    css_generator($arr_files, $path);
}

// SPRITE CREATION
function sprite_create($arr_files, $path)
{
    if (!file_exists("sprite.png")) {
        $arr_img_height = array();
        $img_max_width = 0;
        foreach ($arr_files as $file) {
            $img = imagecreatefrompng($path . "/" . $file);
            $img_width = imagesx($img);
            $img_height = imagesy($img);
            $img_max_width += $img_width;
            array_push($arr_img_height, $img_height);
            imagedestroy($img);
        }
        sort($arr_img_height);
        $img_max_height = array_pop($arr_img_height);
        $sprite = imagecreatetruecolor($img_max_width, $img_max_height);
        $bg = imagecolorallocate($sprite, 0, 0, 0);
        imagecolortransparent($sprite, $bg);
        imagefilledrectangle($sprite, 0, 0, imagesx($sprite), imagesy($sprite), $bg);
        imagepng($sprite, "$path/sprite.png");
        sprite_merge($arr_files, $path, $sprite);
    } else {
        echo "Error : file 'sprite.png' already exists.\n";
    }
}

// SCAN DIRECTORY
function my_scandir($path)
{
    $handle = opendir($path);
    $arr_files = array();

    while ($file = readdir($handle)) {
        if (substr($file, -4) == ".png") {
            array_push($arr_files, $file);
        }
    }
    sprite_create($arr_files, $path);
}

// SCRIPT INITIALISATION
function init($argv, $argc)
{
    if ($argc == 2 && is_dir($argv[1])) {
        $path = $argv[1];
        my_scandir($path);
    } else if ($argc == 3 && is_dir($argv[2])) {
        if ($argv[1] === "-r" || $argv[1] === "--recursive") {
            $path = $argv[2];
            my_recursive($path);
            $arr_files = my_recursive($path);
            sprite_create($arr_files, $path);
        } else {
            echo "Error : command unknown\n";
        }
    } else if ($argc == 4 && is_dir($argv[3])) {
        $path = $argv[3];
        if ($argv[1] === "-i" || $argv[1] === "--output-image" && file_exists($path . "sprite.png")) {
            output_image($argv);
        } else if ($argv[1] === "-s" || $argv[1] === "--output-style" && file_exists($path . "style.css")) {
            output_style($argv);
        } else {
            echo "Error : the file doesn't exists or has been modified\n";
        }
    } else if ($argc <= 1 || !$argv[1] === "-m" || !$argv[1] === "--manual") {
        echo "Error : this is not a folder\n";
    }
}

init($argv, $argc);
manual($argv, $argc);

?>