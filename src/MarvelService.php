<?php

namespace Drupal\get_marvel;

use \Drupal\node\Entity\Node;
use \Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerTrait;
/**
 * @file
 * Service RADIONIC TOP
 */

class MarvelService {

  use MessengerTrait;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
  * Passing parameter to construct method
  */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory){
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * Get data from Url
   */
  public function getDataUrl($url, $timeout = 5)
  {
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    
    $data = curl_exec($ch);
    
    $error_msg = curl_error($ch);
    
    if ($error_msg) {
        \Drupal::logger('get_marvel')->error($error_msg. "->". $url );
        return false;
    }
    
    curl_close($ch);

    return $data;
  }

  public function setTitle ($text_title) {
    $siteName = \Drupal::config('system.site')->get('name');
    $title = [ '#tag' => 'title', '#value' =>  $siteName." | ".$text_title];

    return [$title, 'title'];
  }

  /**
   * Crear el contenido asociado al usuario que lo escogió como favorito
   */
  public function setContent ($result, $account_id = 0) {

    if (isset($result['title'])) {
      $title = $result['title'];
    } else {
      $title = $result['name'];
    }

    $node = Node::create([
      'type'        => 'marvel_content',
      'title'       => $title,
      'field_id_mv' => $result['id'],
      'field_description_mv' => $result['description'],
      'field_resource_uri' => $result['resourceURI'],
      'field_path_thumbnail_mv' => $result['thumbnail']['path'].'.'.$result['thumbnail']['extension'],
      'uid' => $account_id,
    ]);
    $node->save();

    return $node;
  }

  // public function contentExits($date, $uid = null) {
    

  //   $query = \Drupal::entityQuery('node');
  //   $query->condition('status', 1)
  //   ->condition('type', 'marvel_content')
  //   ->condition('uid', $uid, '=')
  //   ->condition('field_date_24', $formatted_cosult, '=');

  //   if ($nid !== null) {
  //     $query->condition('nid', $nid, '<>');
  //   }

  //   $query->sort('nid', 'DESC')->range(0, 1);
  //   $nids = $query->execute();

  //   if (count($nids)> 0) { 
  //     return true;
  //   }
    
  //   return false;
  // }

  /**
   * Get frist value form array value
   */
  public function getFirstValueArray($array) {

    if (isset($array[0]['value'])) {
          return $array[0]['value'];
    } elseif (isset($array[0]['target_id'])) {
          return $array[0]['target_id'];
    } elseif (isset($array[0])) {

      if (count($array[0]) == 0) {
        return '';
      }
      
      return $array[0];
    }

    return '';
  }

  /**
   * Redirección de acceso denegado 403
   */
  public function getOutAccess() {
    $redirect_url = new \Drupal\Core\Url('system.403');
    $response = new RedirectResponse($redirect_url->toString());
    $response->send();
    
    return $response;
  }

  /**
   * Redirect to page 404
   * @return RedirectResponse
   */
  public function getOutFind() {
    $redirect_url = new \Drupal\Core\Url('system.404');
    $response = new RedirectResponse($redirect_url->toString());
    $response->send();
    
    return $response;
  }

    /**
   * Redirect to page 404
   * @return RedirectResponse
   */
  public function redirectTo($url) {
    
    $response = new RedirectResponse($url);
    $response->send();
    
    return $response;
  }

}