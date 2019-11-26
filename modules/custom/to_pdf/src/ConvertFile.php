<?php

namespace Drupal\to_pdf;

class ConvertFile{
    public static function converter($fileUri, $bin, $outputDirectory)
    {

        $filePath =  \Drupal::service('file_system')->realpath($fileUri);

        $cmd=($bin).' --headless --convert-to pdf '.escapeshellarg($filePath). ' --outdir '.escapeshellarg($outputDirectory);

        return shell_exec($cmd);

    }

}
