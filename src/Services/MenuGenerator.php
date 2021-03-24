<?php

namespace Drupal\custom_api\Services;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Menu\MenuLinkTreeInterface;

/**
 * Defines a class to get the Menu Information. 
 */
class MenuGenerator {

  /**
   * The menu tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menu_tree;

  /**
   * Construct a MenuGenerator object.
   * 
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *      The menu tree service
   */
  function __construct(MenuLinkTreeInterface $menu_tree) {
      $this->menu_tree = $menu_tree;
  }

  /**
   * Function for get all menu items by menu name.
   * 
   * @param string $name
   *      The menu machine name
   * 
   * @return array 
   *      The Menu data in array.
   */
  public function getMenuItems($name) {
    $resultado = [];
    $menu = $this->menu_tree->load($name, new MenuTreeParameters());
    $resultado = $this->processMenu($menu);
    return $resultado;
  }

  /**
   * Function for extract the relevant and friendly info of the menu items.
   * 
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $menu
   *      The MenuLinkTreeElement array
   * 
   * @return array 
   *      The Menu data in array.
   */
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