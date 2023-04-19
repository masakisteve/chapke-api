<?php
$zip_file_name = "my_zip_file.zip"; // Set the name of the zip file
$zip = new ZipArchive();
if ($zip->open($zip_file_name, ZipArchive::CREATE) === TRUE) {
    $dir = '.';
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($files as $file) {
        $file_path = realpath($file->getPathname());
        if (!$file->isDir()) {
            $relativePath = substr($file_path, strlen($dir) + 1);
            $zip->addFile($file_path, $relativePath);
        } else {
            $relativePath = substr($file_path, strlen($dir));
            $zip->addEmptyDir($relativePath);
        }
    }
    $zip->close();

    // Download the zip file
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename='.$zip_file_name);
    header('Content-Length: ' . filesize($zip_file_name));
    readfile($zip_file_name);
}
else {
    echo "Error creating zip file";
}
?>
