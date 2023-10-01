<?php
namespace Drupal\custom_api\Services;

use Symfony\Component\HttpFoundation\Request;

class RegionsPrint {
    
    public function getAuxiliarContent($output, $theme_name = "bartik") {
        $filters = [
            $output["type"][0]["target_type"] => $output["type"][0]["target_id"]
        ];
        $regions = $this->getBlocksTheme($theme_name);
        $regions_output = [];
        unset($regions["Content"]);
        foreach ($regions as $key => $region) {
            if(count($region) > 0) {
                $regions_output[$key] = [];
                foreach ($region as $key_block => $block) {
                    $entity_type_id = $block->getEntityTypeId();
                    $entity_id = $block->id();
                    if($this->isVisible($block, $filters)) {
                        $normalize = \Drupal::service("custom_api.entity_normalize");
                        $regions_output[$key][$key_block] = $normalize->convertJson($block);
                    }
                }
            }            
        }
        $regions_output["Content"] = $output;
        return $regions_output;        
    }
    
    public function isVisible($block, $filters) {
        $iterator_conditions = $block->getVisibilityConditions()->getIterator();
        return TRUE;
    }

    public function prepareFilters($node) {

    }

    public function getBlocksTheme($theme_name) {
        $theme = \Drupal::service("theme_handler")->getTheme($theme_name);
        $regions = $theme->info["regions"];
        $block_layout = [];
        foreach ($regions as $region) {  
            $blocks = \Drupal::entityTypeManager()
              ->getStorage('block')
              ->loadByProperties(['theme' => $theme->getname(), 'region' => $region]);            
            $block_layout[$region] = $blocks;
        }
        return $block_layout;
    }
}