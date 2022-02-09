<?php

namespace Drupal\get_marvel\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\get_marvel\MarvelService;

/**
 * Implement a settings form
 */
class FormGetMarvelConfig extends ConfigFormBase {
 
  use MessengerTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $EntityTypeManager;

    /**
   * The utilites Radionics Tops service.
   *
   * @var \Drupal\get_marvel\MarvelService
   */
  protected $MarvelUtilites;


  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManager $entity_type_manager, MarvelService $marvel_utilites) {
    $this->EntityTypeManager = $entity_type_manager;
    $this->MarvelUtilites = $marvel_utilites;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('entity_type.manager'),
      $container->get('get_marvel.utilities')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'get_marvel.config';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['get_marvel.config'];
  }

  /**
   * Configuration data from get data API Marvel 
   *
   * @param array $form
   * @param FormStateInterface $form_state
   *
   * @return type
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = $this->config('get_marvel.config');

    $form['marvel_api'] = array(
      '#type' => 'details',
      '#title' => $this->t('Configuración del los accesos al Api de Marvel'),
      '#open' => TRUE
    );

    $form['marvel_api']['base_url'] = [
        '#type' => 'url',
        '#title' => $this->t('Ruta Base del API'),
        '#description' => 'Url del API del Marvel Eje. https://gateway.marvel.com',
        '#required' => TRUE,
        '#default_value' => $config->get('base_url'),
    ];

    $form['marvel_api']['public_key'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Llave Pública'),
        '#description' => 'Lave pública asignada por Developer Marvel. Se requiere una cuenta, obtnerla <a target="_blank" href="https://developer.marvel.com/account">aquí</a>',
        '#required' => TRUE,
        '#default_value' => $config->get('public_key'),
    ];

    $form['marvel_api']['private_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Llave Privada'),
      '#description' => 'Lave privada asignada por Developer Marvel. Se requiere una cuenta, obtnerla <a target="_blank" href="https://developer.marvel.com/account">aquí</a>',
      '#required' => TRUE,
      '#default_value' => $config->get('private_key'),
    ];

    
    // $data_node = $this->MarvelUtilites->getDataNode($node);
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Genera el guardado de la configuración.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('get_marvel.config')
    ->set('base_url', $form_state->getValue('base_url'))
    ->set('public_key', $form_state->getValue('public_key'))
    ->set('private_key', $form_state->getValue('private_key'))
    ->save();
  }

}
