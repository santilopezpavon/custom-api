<?php

namespace Drupal\static_custom_api\Service;

/**
 * Servicio personalizado que no hace nada más que copiar.
 */
class ConfigCache {

    private $config_key = 'static_custom_api.settings';

    public function isEntitySaveable($entity_type, $bundle = NULL) {
        $config_bundles = \Drupal::config($this->config_key)->get($entity_type . "_bundles");
        if(
            !empty($config_bundles) && 
            array_key_exists($bundle, $config_bundles) &&
            $config_bundles[$bundle] != "0"            
        ) {
            return TRUE;
        }
        return FALSE;
    }

}
