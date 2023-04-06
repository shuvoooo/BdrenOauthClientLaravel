<?php


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('storage_asset')) {
    function storage_asset($path): string
    {
        return asset(Storage::url($path));
    }
}


if (!function_exists('slugify')) {

    function slugify($text, string $divider = '-')
    {
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);

        $text = preg_replace('~-+~', $divider, $text);

        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a-' . Str::random(5);
        }
        return $text;
    }
}
