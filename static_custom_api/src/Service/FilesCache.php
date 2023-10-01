<?php

namespace Drupal\static_custom_api\Service;

/**
 * Servicio personalizado que no hace nada más que copiar.
 */
class FilesCache {

    private $base_folder_files = 'custom-build';

    public function saveEntitySerialized($entity_serialized, $entity_type, $bundle, $id, $lang) {
        if(!\Drupal::service('static_custom_api.config_cache')->isEntitySaveable($entity_type, $bundle)) {
            return NULL;
        }

        $response = $entity_serialized;       
        try {
            $this->deleteFileIfExists($entity_type, $id, $lang);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            \Drupal::logger("FilesCache")->alert(print_r($message, true));
        }
        if($response !== NULL) {
            $this->createJsonFile($response, $entity_type, $id, $lang);
        }   
    }

    public function saveEntityJson($entity) {
       
        $entity_type = $entity->getEntityTypeId();
        $bundle = $entity->bundle();
        if(!\Drupal::service('static_custom_api.config_cache')->isEntitySaveable($entity_type, $bundle)) {
            return NULL;
        }
        $id = $entity->id();
        $lang = $entity->language()->getId(); 
        $response = NULL;       
        try {
            $this->deleteFileIfExists($entity_type, $id, $lang);
            $response = \Drupal::service("custom_api.entity_normalize")->getEntity($entity_type, $id, ["display"=> 'default']);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            \Drupal::logger("FilesCache")->alert(print_r($message, true));
        }
        if($response !== NULL) {
            $this->createJsonFile($response, $entity_type, $id, $lang);
        }       
    }

    private function deleteFileIfExists($entity_type, $id, $lang) {
        $fileSystem = \Drupal::service('file_system');
        $publicDirectory = $fileSystem->realpath("public://"); 
        $filename = $this->getFileName($entity_type, $id, $lang);
        $filePath = $publicDirectory . '/' . $this->base_folder_files . '/' . $filename;
        \Drupal::service('file_system')->delete($filePath, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
    }

    public function getEntityJson($entity_type, $id, $lang = '') { 
        try {
            $fileSystem = \Drupal::service('file_system');
            $publicDirectory = $fileSystem->realpath("public://");        
            $filename = $this->getFileName($entity_type, $id, $lang);
            $filePath = $publicDirectory . '/' . $this->base_folder_files . '/' . $filename;
            return json_decode(file_get_contents($filePath), true);
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }

    public function createJsonFile($entity_array, $entity_type, $id, $lang = '') {
       $filename = $this->getFileName($entity_type, $id, $lang);
        
       $fileSystem = \Drupal::service('file_system');
       $publicDirectory = $fileSystem->realpath("public://");  

       $directorio = 'public://' . $this->base_folder_files;
       \Drupal::service('file_system')->prepareDirectory($directorio, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
       
       $filePath = $publicDirectory . '/' . $this->base_folder_files . '/' . $filename;
       $fileSystem->saveData(json_encode($entity_array), $filePath, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE); 
    }
   
    private function getFileName($entity_type, $id, $lang = '') {
       return $entity_type  . "--" . $id . "--" . $lang . ".json";
    }
}
