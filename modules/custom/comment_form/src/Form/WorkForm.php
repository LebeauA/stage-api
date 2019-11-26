<?php
/**
 * @file
 * Contains \Drupal\comment_form\Form\WorkForm.
 */
namespace Drupal\comment_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;

class workForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'comment_form_block';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['comment_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Comment title :'),
      '#required' => TRUE,
    );
    $form['comment_description'] = array(
      '#type' => 'textarea',
      '#title' => t('Comment description :'),
      '#required' => TRUE,
    );
    $form['comment_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Select new status'),
      '#options' => [
        '1' => $this->t("For more information"),
        '2' => $this->t("Agreed"),
        '3' => $this->t("Agreed after correction"),
        '4' => $this->t("Rejected")
      ]
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#button_type' => 'primary',
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $currentNode = \Drupal::routeMatch()->getParameter('node');

    $comment_title = $form_state->getValue('comment_title');
    $comment_description =  $form_state->getValue('comment_description');
    $comment_status = $form_state->getValue('comment_status');
    $node = Node::create([
      'type' => 'comment',
      'title' => $comment_title,
      'body' => $comment_description,
      'field_status' => $comment_status,
      'field_document_bound' => $currentNode->id()
      ]);
    $node->save();

    $currentNode->field_status->setValue([
      'target_id' => $comment_status,
    ]);
    $currentNode->save();
  }
}
