<?php

namespace Drupal\get_marvel\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\get_marvel\MarvelService;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;


class GetMarvelController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The utilites Radionics Tops service.
   *
   * @var \Drupal\get_marvel\MarvelService
   */
  protected $MarvelUtilites;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $EntityTypeManager;

  /**
   * The session object.
   *
   * We will use this to store information that the user submits, so that it
   * persists across requests.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * @var AccountInterface $account
   */
  protected $account;

  public function __construct(EntityTypeManager $entity_type_manager, MarvelService $marvel_utilites, SessionInterface $session, AccountInterface $account) {
    $this->EntityTypeManager = $entity_type_manager;
    $this->MarvelUtilites = $marvel_utilites;
    $this->session = $session;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('get_marvel.utilities'),
      $container->get('session'),
      $container->get('current_user')
    );
  }

  /**
   * Presetenta la vista incial de los contenidos de Marvel
   */
  public function getDataMarvel () {
    $data = ['title' => $this->t('Marvel Contents')];
    $config = \Drupal::config('get_marvel.config');
    $endpoints = \Drupal::config('get_marvel.endpoints');

    $data['endpoints'] = $endpoints->get('endpoints');

    $public_key = $config->get('public_key');
    $private_key = $config->get('private_key'); 
    $base_url = $config->get('base_url');
    $ts = 1;

    $hash = md5($ts.$private_key.$public_key);
    
    $first_etity = trim($data['endpoints'][0]['entity']); 

    $url = $base_url."/v1/public/@entity?limit=30&ts=".$ts."&apikey=".$public_key."&hash=".$hash;
    $data['response'] = json_decode($this->MarvelUtilites->getDataUrl(str_replace('@entity', $first_etity,$url), 20), true);

    $build = [];
    $build['#theme'] = 'getdata';
    $build['#data'] = $data;
    $build['#attached']['library'][] = 'get_marvel/get_marvel.view_data';
    $build['#attached']['library'][] = 'get_marvel/get_marvel.bootstrap_cdn';
    $build['#attached']['library'][] = 'get_marvel/get_marvel.bootswatch';
    $build['#attached']['html_head'][] = $this->MarvelUtilites->setTitle($data['title']);
    $build['#attached']['drupalSettings']['url_marvel'] = $url;
    $build['#cache'] = ['contexts' => ['user']];

    return $build;
  }

  /**
   * Función para agregar por ajax un contenido de Marvel Como Favorito
   */
  public function setFavCallback($entity = NULL, $id = NULL) {

    if ($this->account->id() == 0) {
      return new JsonResponse([
        'status' => true, 
        'message' => "Requiere iniciar sessión para agregar a favoritos", 
        "entity" => $entity, 
        'id' => $id
      ]);
    }

    $output = $this->setFav($entity,$id, $this->account->id());
    $output['method'] = 'GET';

    return new JsonResponse($output);
  }

  /**
   * Establecer los datos para consultar el contenidos a guardar como favorito.
   */
  public function setFav($entity,$id, $account_id){
    $config = \Drupal::config('get_marvel.config');

    $public_key = $config->get('public_key');
    $private_key = $config->get('private_key'); 
    $base_url = $config->get('base_url');
    $ts = 1;

    $hash = md5($ts.$private_key.$public_key);
    
    $url = $base_url."/v1/public/".$entity."/".$id."?ts=".$ts."&apikey=".$public_key."&hash=".$hash;
    $data = json_decode($this->MarvelUtilites->getDataUrl($url, 20), true);

    if (!isset($data['data']['results'][0])) {
      return [
        'status' => true, 
        'message' => "Requiere iniciar sessión para agregar a favoritos", 
        "entity" => $entity, 
        'id' => $id
      ];
    }

    $result = $data['data']['results'][0];

    $node = $this->MarvelUtilites->setContent($result, $account_id);

    if ($node) {
      return [
        'status' => true, 
        'message' => "Contenido Guardado", 
        "entity" => $entity, 
        'id' => $id
      ];
    }

    return [
      'status' => false, 
      'message' => "No se puedo guardar", 
      "entity" => $entity, 
      'id' => $id
    ];


  }
 
}
