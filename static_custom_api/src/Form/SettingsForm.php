<?php

namespace Drupal\static_custom_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return [
      'static_custom_api.settings',
    ];
  }

  public function getFormId() {
    return 'static_custom_api.settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('static_custom_api.settings');
    
    /*$storage = \Drupal::entityTypeManager()->getStorage("node");
    $entity = $storage->load(3);
    \Drupal::service("static_custom_api.files_cache")->saveEntity($entity);*/
    // Serializar una entidad
    /*$storage = \Drupal::entityTypeManager()->getStorage("node");
    $entity = $storage->load(3);
    $json_entity = \Drupal::service("serializer")->serialize($entity, 'json', []);
    $array_entity = json_decode($json_entity, true);
    dump($array_entity);

    // Prepare Directory
    $folder1 = number_format($id / 100, 0);
    $folder2 = number_format($folder1 / 100, 0);

    $base_folder = "public://custom-build" . "/" . $folder1 . "/" . $folder2 . "/node";
    \Drupal::service('file_system')->prepareDirectory($base_folder, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);

    // Prepare File
    $file_path = $base_folder . '/' . "3-" . $entity->language()->getId() . ".json";
    dump($base_folder);
    dump($file_path);

    $server_path = \Drupal::service('file_system')->realpath($file_path); 
    dump($server_path);
    \Drupal::service('file_system')->saveData($json_entity, $file_path, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE); 
    dump("end");*/

    //         \Drupal::service('file_system')->delete($filePath, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);


    $form["node_bundles"] = [
        '#type' => 'checkboxes',
        '#title' => t('Bundles Nodes'),
        '#options' => $this->getAllBundlesByEntityType("node"),
        '#default_value' => $config->get('node_bundles'),
    ];

    $form["paragraph_bundles"] = [
        '#type' => 'checkboxes',
        '#title' => t('Bundles Paragraph'),
        '#options' => $this->getAllBundlesByEntityType("paragraph"),
        '#default_value' => $config->get('paragraph_bundles'),
    ];

    $form["menu_bundles"] = [
        '#type' => 'checkboxes',
        '#title' => t('Bundles Menu'),
        '#options' => $this->getAllMenus(),
        '#default_value' => $config->get('menu_bundles'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('static_custom_api.settings');
    $config->set('node_bundles', $form_state->getValue('node_bundles'));
    $config->set('paragraph_bundles', $form_state->getValue('paragraph_bundles'));
    $config->set('menu_bundles', $form_state->getValue('menu_bundles'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

  private function getAllBundlesByEntityType($entity_type) {
    $options = [];
    $all_entity_types = \Drupal::entityTypeManager()->getDefinitions();
    if(array_key_exists($entity_type, $all_entity_types)) {
        $bundles =  \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type);
        foreach ($bundles as $machine_name => $value) {
            $options[$machine_name] = $value["label"];
        }
    }  

    return $options;
  }

  private function getAllMenus() {

  $menuStorage = \Drupal::entityTypeManager()->getStorage('menu');
  $menuEntities = $menuStorage->loadMultiple();

  foreach ($menuEntities as $menuEntity) {
    $menus[$menuEntity->id()] = $menuEntity->label();
  }

  return $menus;
  }
  
  private function getAllContentTypes() {
    return \Drupal::entityTypeManager()->getDefinitions();

  }

}
