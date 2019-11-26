<?php

/**
 * @file
 * Contains \Drupal\dropzone\Controller\dropzoneController.
 */

namespace Drupal\dropzone\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\file\Entity\File;
use \Drupal\Core\File\FileSystemInterface;


class dropzoneController extends ControllerBase{
  public static function filesUpload($project){
      if(!empty($_FILES)){
        function incoming_files() {
          $files = $_FILES;
          $files2 = [];
          foreach ($files as $input => $infoArr) {
            $filesByInput = [];
            foreach ($infoArr as $key => $valueArr) {
              if (is_array($valueArr)) { // file input "multiple"
                foreach($valueArr as $i=>$value) {
                  $filesByInput[$i][$key] = $value;
                }
              }
              else { // -> string, normal file input
                $filesByInput[] = $infoArr;
                break;
              }
            }
            $files2 = array_merge($files2,$filesByInput);
          }
          $files3 = [];
          foreach($files2 as $file) { // let's filter empty & errors
            if (!$file['error']) $files3[] = $file;
          }
          return $files3;
        }
        $tmpFiles = incoming_files();
        foreach ($tmpFiles as $files){
          $temp = $files['tmp_name'];
          mkdir("public://".date("Y-m"));
          $folder = "public://" . date("Y-m");
          $destination_path = $folder . DIRECTORY_SEPARATOR;
          $target_path = $destination_path . $files['name'];
          $finalPath = \Drupal::service('file_system')
            ->move($temp, $target_path, FileSystemInterface::EXISTS_RENAME);

          $new_files = File::create([
            'uri' => $finalPath,
          ]);
          $new_files->save();

          $fileUri = $new_files->getFileUri();
          $path_parts = pathinfo($fileUri);
          $fileName = $path_parts['filename'];

          $document = Node::create([
            'type' => 'document',
            'title' => $fileName,
            'field_file' => [
              'target_id' => $new_files->id(),
            ],
            'field_project' => [
              'target_id' => $project,
            ],
          ]);
          $document->save();
        }
      }
  return new JsonResponse(array('test'));
  }
}
