<?php
/** Router only for the PHP development server. Never deploy this script. */
$root = dirname(__DIR__) . '/.runtime/wordpress';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = realpath($root . rawurldecode($path));
$theme_root = dirname(__DIR__) . '/theme/estatein/';
if (
    $file &&
    (str_starts_with($file, $root . DIRECTORY_SEPARATOR) ||
        str_starts_with($file, $theme_root)) &&
    is_file($file)
) {
    return false;
}
require $root . '/index.php';
