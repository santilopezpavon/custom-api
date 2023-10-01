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
   //  $response = \Drupal::service("custom_api.entity_normalize")->getEntity("node", "17", ["display"=> 'default']);
    //\Drupal::service("static_custom_api.files_cache")->createJsonFile($response, 'node', 'article', 17, "en");
    /*$storage = \Drupal::entityTypeManager()->getStorage("paragraph");
        $entity = $storage->load(13);
        dump("Aqui estamos");
        dump($entity);*/
    
  //      $resp = \Drupal::service("static_custom_api.files_cache")->getEntityJson('node', 'article', 17, "en");
    //dump($resp);

    //\Drupal::service("static_custom_api.config_cache")->isEntitySaveable("node", "pepito");
   /* $fileSystem = \Drupal::service('file_system');
    $publicDirectory = $fileSystem->realpath("public://");  
    dump($publicDirectory);
    dump(file_create_url("public://photos/hello.jpg"));*/

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
