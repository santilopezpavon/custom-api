<?php

namespace Drupal\static_custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\custom_api\Controller\ApiControllerBaseQueries;

/**
 * Clase controladora para el ejemplo de batch api.
 */
class BuildController extends ControllerBase {

  /**
   * Método que muestra un formulario para iniciar el proceso batch.
   *
   * @return array
   *   Un array renderizable con el formulario.
   */
  public function mostrarFormulario() {


    /* $entity_type = "node";
    $bundle = "article";
    dump(\Drupal::entityTypeManager()->getDefinitions());*/
/*
    $storage = \Drupal::entityTypeManager()->getStorage(
      "node"
    );
    $entity = $storage->load(1);
    $schema = \Drupal::service("custom_api.entity_control_fields_show")->generateContextEntity($entity, ["display"=> 'default']);
    $entity_json = \Drupal::service("custom_api.entity_normalize")->convertJson($entity, $schema);
    //\Drupal::service("custom_api.entity_control_fields_show");
*/

    $url_generator = \Drupal::service('url_generator');
    $url = $url_generator->generateFromRoute('custom_api.getentity', [
      "entity_type" => 'node', "id" => 1
    ], ['absolute' => TRUE]);
    dump($url);
    $client = \Drupal::httpClient();
    $request = $client->post($url, [
      'json' => [
        'schema'=> ["display" => 'default']
      ]
    ]);
    $response = json_decode($request->getBody());
    dump("hola");
    dump($response);
    exit();
    $batch = [
      'title'            => 'Importing CSV...',
      'operations'       => [],
      'init_message'     => 'Starting...',
      'progress_message' => 'Processed @current out of @total.',
      'error_message'    => 'An error occurred during processing',
      'finished'         => '\Drupal\static_custom_api\Batch\MyCustomBatch::importFinished',
    ];

    $query = \Drupal::entityQuery('paragraph');
    $results = $query->execute();
    foreach ($results as $key => $value) {
      $batch['operations'][] = [
        '\Drupal\static_custom_api\Batch\MyCustomBatch::importLine',
        ['paragraph', $value],
      ];
    }
    batch_set($batch);
    return batch_process('user');
  }

}
