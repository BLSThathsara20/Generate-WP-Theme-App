


<?php

if (isset($_POST['generate'])) {
    if (isset($_POST['generate'])) {
        $theme_name = $_POST['theme_name'];
        $author = $_POST['author'];
        $use_woocommerce = isset($_POST['use_woocommerce']) && $_POST['use_woocommerce'] == 'on' ? true : false;
    
    
        $parent_dir = 'wp-content/themes/';
    if (!is_dir($parent_dir)) {
        mkdir($parent_dir, 0755, true);
    }
    
    
    $pathname = $parent_dir . $theme_name;
    if (!is_dir($pathname)) {
        mkdir($pathname, 0755, true);
    }
    
    
    $style_css = <<<EOT
    /*
    Theme Name: $theme_name
    Author: $author
    Description: A basic WordPress theme.
    */
    EOT;
    
    file_put_contents("$pathname/style.css", $style_css);
    mkdir("$pathname/images");
    
    
    if ($use_woocommerce) {
        $functions_php = <<<EOT
    <?php
    add_action( 'after_setup_theme', 'woocommerce_support' );
    function woocommerce_support() {
        add_theme_support( 'woocommerce' );
    }
    EOT;
        file_put_contents("$pathname/functions.php", $functions_php);
    }
    
    
    $zip = new ZipArchive;
    if ($zip->open("$pathname.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo "Error: Could not create zip file.";
        exit;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pathname),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($pathname) + 1);
            if (!$zip->addFile($filePath, $relativePath)) {
                echo "Error: Could not add file $filePath to zip.";
                $zip->close();
                exit;
            }
        }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='$theme_name.zip'");
    header('Content-Length: ' . filesize("$pathname.zip"));
    header("Location: $pathname.zip");
    
    }
}

