<?php
namespace Drupal\custom_api\Services;

use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;



class ViewControl {
    
    private $normalize; 

    public function __construct($entity_normalize) {
        $this->normalize = $entity_normalize;
    }

    public function getView($view_id, $display_id, $schema) {
        $view = Views::getView($view_id);
        $view->setDisplay($display_id);

       /* $filters = [
            'title' => 'di',          
            // 'field_facet' => ['A', 'B'],
        ];*/
        $filters = $this->normalize->content;
        if(!empty($this->normalize->parametersGet["current_page"])) {
            $view->setCurrentPage($this->normalize->parametersGet["current_page"]); 
        } else {
            $view->setCurrentPage(0); 
        }
       // $view->setCurrentPage(2);
        //$filter_input = $view->getExposedInput();
        $view->setExposedInput($filters);
        $view->execute();

        $results = [];
        foreach ($view->result as $value) {
            $entity = $value->_entity;
            $results[] = $this->normalize->convertJson($entity, $schema);
        }
        $result_array = [
            "current_page" => $view->pager->current_page,
            "items_page" => $view->pager->options["items_per_page"],
            "total_items" => $view->pager->total_items,
            "results" => $results
        ];

        return $result_array;

    }
}