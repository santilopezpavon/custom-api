<?php

namespace Drupal\static_custom_api\Service;

/**
 * Servicio personalizado que no hace nada más que copiar.
 */
class FilesCache {

    private $base_folder_files = 'custom-build';

    private $entity_type_cached = ["node", "paragraph", "taxonomy_term", "media", "menu", "block_content"];

    public function saveEntity($entity) {
        $entity_data_for_json = $this->getEntityDataForSaveJson($entity);
        if(!in_array($entity_data_for_json["target_type"], $this->entity_type_cached)) {
            return FALSE;
        }
        $subfolder_directory = $this->getSubFolderEntityJson(
            $entity_data_for_json["target_type"],
            $entity_data_for_json["id"]
        );       

        $this->prepareFolder($subfolder_directory);
        $path_file = $this->getPathFile($entity_data_for_json);
        $this->saveEntityInJson($entity, $path_file["real_path_file"], $path_file["file_url"]);
    }

    public function getEntityFile($target_type, $id, $lang) {
        if(!in_array($target_type, $this->entity_type_cached)) {
            return FALSE;
        }

        $path_file = $this->getPathFile([
            "target_type" => $target_type,
            "id" => $id,
            "lang" => $lang
        ]);
        $path_file_real = $path_file["real_path_file"];
        if(file_exists($path_file_real)) {

            return json_decode(file_get_contents($path_file_real), true);
        } 

        return FALSE;
    }

    private function saveEntityInJson($entity, $file_path, $file_url) {
        $json_entity = \Drupal::service("serializer")->serialize($entity, 'json', []);
        // $json_entity = json_encode($entity_array);
        \Drupal::service('file_system')->saveData($json_entity, $file_path, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE); 
    }

    public function getPathFile($entity_data_for_json) {

        $subfolder_directory = $this->getSubFolderEntityJson(
            $entity_data_for_json["target_type"],
            $entity_data_for_json["id"]
        );

        $file_name = $this->getFileName(
            $entity_data_for_json["target_type"],
            $entity_data_for_json["id"],
            $entity_data_for_json["lang"]
        );

        $path_file = $subfolder_directory . "" . $file_name;

        return [
            "path_file" => $path_file,
            "real_path_file" => \Drupal::service('file_system')->realpath($path_file),
            "file_url" => file_create_url($path_file)
        ];
    }

    private function prepareFolder($folder) {
        \Drupal::service('file_system')->prepareDirectory($folder, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
    }

    private function getEntityDataForSaveJson($entity) {
        return [
            "target_type" => $entity->getEntityTypeId(),
            "bundle" => $entity->bundle(),
            "id" => $entity->id(),
            "lang" => $entity->language()->getId()
        ];
    }

    private function getFileName($target_type, $id, $lang) {
        return $target_type . "--" . $id . "--" . $lang . ".json";
    }

    private function getSubFolderEntityJson($target_type, $id) {
        $folder1 = number_format($id / 200, 0);
        $folder2 = number_format($folder1 / 200, 0);
        $folder3 = number_format($folder2 / 200, 0);

        return "public://custom-build" . "/" . $target_type . "/" . $folder1 . "/" . $folder2 . "/" . $folder3 . "/";
    }
}
