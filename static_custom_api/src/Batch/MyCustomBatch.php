<?php
namespace Drupal\static_custom_api\Batch;

use Drupal\Core\File\FileSystemInterface;

/**
 * Methods for running the CSV import in a batch.
 *
 * @package Drupal\my_custom_module
 */
class MyCustomBatch {

  /**
   * Handle batch completion.
   */
  public static function importFinished($success, $results, $operations) {
    $messenger = \Drupal::messenger();
    //$messenger->addMessage('Imported ' . $results['rows_imported'] . ' rows.');

   /* 
      Añadimos aquí cualquier ejecución final que necesitemos.
   */

    return 'The CSV import has completed.';
  }


  /**
   * Process a single line.
   */
  public static function importLine($type_content, $id_content, &$context) {
    //$context['results']['rows_imported']++;
    //$line = array_map('base64_decode', $line);
    //$context['message'] = t('Importing row ' . $context['results']['rows_imported']);
    \Drupal::logger("import")->alert(print_r($id_content, true));;
    /* 
      Añadimos aquí el procesado que necesitemos hacer.
    */
    //dump($context);
  }

}
