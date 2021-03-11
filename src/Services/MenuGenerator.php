<?php
namespace Drupal\custom_api\Services;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Menu\MenuLinkTreeInterface;

class MenuGenerator {

  protected $menu_tree;

  function __construct(MenuLinkTreeInterface $menu_tree) {
    $this->menu_tree = $menu_tree;
  }

  public function getMenuItems($name) {
    $resultado = [];
    $menu = $this->menu_tree->load($name, new MenuTreeParameters());
    $resultado = $this->processMenu($menu);
    return $resultado;
  }

  public function processMenu($menu) {
    $resultado = [];
    foreach($menu as $key => $value) {
      if($value->hasChildren) {
        $resultado[] = [
          'sub' => $this->processMenu($value->subtree) ,
          'title' => $value->link->getTitle(),
          'link' => $value->link->getUrlObject()->toString()
        ];
      } else {
        $resultado[] = [
          'title' => $value->link->getTitle(),
          'link' => $value->link->getUrlObject()->toString()
        ];
      }
    }
    return $resultado;
  }
}