<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\custom_api\Services\EntityNormalizer;
use Drupal\custom_api\Services\GenerateResponses;
use Drupal\custom_api\Services\RegionsPrint;
use Drupal\custom_api\Services\MenuGenerator;
use Drupal\custom_api\Services\ViewControl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base controller class for custom API controllers.
 */
class ApiControllerBase extends ControllerBase {

  /**
   * The entity normalize service.
   *
   * @var \Drupal\custom_api\Services\EntityNormalizer
   */
  protected $entity_normalize;

  /**
   * The entity responses service.
   *
   * @var \Drupal\custom_api\Services\GenerateResponses
   */
  protected $entity_responses;

  /**
   * The regions print service.
   *
   * @var \Drupal\custom_api\Services\RegionsPrint
   */
  protected $regionsprint;

  /**
   * The menu generator service.
   *
   * @var \Drupal\custom_api\Services\MenuGenerator
   */
  protected $menu_generator;

  /**
   * The view control service.
   *
   * @var \Drupal\custom_api\Services\ViewControl
   */
  protected $view_control;
   
  /**
   * Constructs a new ApiControllerBase object.
   *
   * @param \Drupal\custom_api\Services\EntityNormalizer $entity_normalize
   *   The entity normalize service.
   * @param \Drupal\custom_api\Services\GenerateResponses $entity_responses
   *   The entity responses service.
   * @param \Drupal\custom_api\Services\RegionsPrint $regionsprint
   *   The regions print service.
   * @param \Drupal\custom_api\Services\MenuGenerator $menu_generator
   *   The menu generator service.
   * @param \Drupal\custom_api\Services\ViewControl $view_control
   *   The view control service.
   */
  public function __construct(
    EntityNormalizer $entity_normalize, 
    GenerateResponses $entity_responses, 
    RegionsPrint $regionsprint, 
    MenuGenerator $menu_generator, 
    ViewControl $view_control
  ) {
    $this->entity_normalize = $entity_normalize;
    $this->entity_responses = $entity_responses;
    $this->regionsprint = $regionsprint;
    $this->menu_generator = $menu_generator;
    $this->view_control = $view_control;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): ApiControllerBase {
    return new static(
      $container->get('custom_api.entity_normalize'),
      $container->get('custom_api.entity_responses'),
      $container->get('custom_api.regionsprint'),
      $container->get('custom_api.menu_generator'),
      $container->get('custom_api.view_control')
    );
  }
}
