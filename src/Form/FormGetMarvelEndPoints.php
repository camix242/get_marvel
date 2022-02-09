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
class FormGetMarvelEndPoints extends ConfigFormBase {
 
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
    return 'get_marvel.endpoints';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['get_marvel.endpoints'];
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

    $config = $this->config($this->getFormId());
    $endpoints = [];

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Add the different Marvel API Endpoints.'),
    ];

    $num_endpoints = $form_state->get('num_endpoints');

    // We have to ensure that there is at least one name field.
    if ($num_endpoints === NULL) {
      $form_state->set('num_endpoints', 1);
      $num_endpoints = 1;

      $endpoints = $config->get('endpoints');
      if ($endpoints !== NULL) {
        $form_state->set('num_endpoints', count($endpoints));
        $num_endpoints = count($endpoints);
      }
    }

    $form['#tree'] = TRUE;
    $form['endpoints_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('End Points Marvel API'),
      '#prefix' => '<div id="endpoints-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_endpoints; $i++) {
      
      $form['endpoints_fieldset']['endpoints'][$i]['entity'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Tipo Entidad'),
        '#default_value' => isset($endpoints[$i]) ? $endpoints[$i]['entity'] : '',
        '#prefix' => '<div class="endpoints-row">',
        '#description' => 'Identificador de entidad asginado en la API de Marvel Eje: characters, comics, creators...'
      ];
      
      $form['endpoints_fieldset']['endpoints'][$i]['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#default_value' => isset($endpoints[$i]) ? $endpoints[$i]['label'] : '',
        '#suffix' => '</div>',
        '#description' => 'Etiqueta con al que se presentará el tipo de entidad Eje: Personajes, Comics, Creadores...'
      ];
    }

    $form['endpoints_fieldset']['actions'] = [
      '#type' => 'actions',
    ];

    $form['endpoints_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'endpoints-fieldset-wrapper',
      ],
    ];
    
    if ($num_endpoints > 1) {
      $form['endpoints_fieldset']['actions']['remove_name'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove one'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'endpoints-fieldset-wrapper',
        ],
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#attached']['library'][] = 'get_marvel/get_marvel.styling_admin';

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['endpoints_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_endpoints');
    $add_button = $name_field + 1;
    $form_state->set('num_endpoints', $add_button);
    // Since our buildForm() method relies on the value of 'num_endpoints' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_endpoints');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_endpoints', $remove_button);
    }
    // Since our buildForm() method relies on the value of 'num_endpoints' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config($this->getFormId());

    foreach ($values as $key => $value) {
      if ($key === 'endpoints_fieldset') {

        if (isset($value['endpoints'])) {
          $items = $value['endpoints'];
          foreach ($items as $key => $item) {
            if ($item === '' || !$item) {
              unset($items[$key]);
            }
          }
          $config->set('endpoints', $items);
        }
      } else {
        $config->set($key, $value);
      }
    }

    $config->save();
  }

}
