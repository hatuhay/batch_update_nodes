<?php

namespace Drupal\batch_update_nodes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form with examples on how to use batch api.
 */
class UpdateNodesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adl_update_glossary_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['operation'] = array (
      '#type' => 'select',
      '#title' => ('Select Operation'),
      '#options' => array(
        'go' => t('Copy to temp'),
		    'return' => t('Copy from temp'),
      ),
    );
    $form['update_glossary'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Process'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', ['glossary_term', 'center', 'tab_page'], 'IN')
      ->execute();
    $chunks = array_chunk($nids, 10);
    $values = $form_state->getValues();
    $op = $values['operation'];
    $operations = [];
    foreach ($chunks as $key => $chunk) {
      $operations[] = ['batch_update_nodes_update_nodes', [$chunk, $op]];
    }
    $batch = [
      'title' => $this->t('Updating Nodes ...'),
      'operations' => $operations,
      'finished' => 'batch_update_nodes_update_finished',
    ];
    batch_set($batch);
  }

}
