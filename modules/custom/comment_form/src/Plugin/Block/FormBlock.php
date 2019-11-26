<?php
/**
 * @file
 * Contains \Drupal\comment_form\Plugin\Block\FormBlock.
 */
namespace Drupal\comment_form\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
/**
 * Provides a 'Form' block.
 *
 * @Block(
 *   id = "comment_form_block",
 *   admin_label = @Translation("Comment form block"),
 *   category = @Translation("Custom form block")
 * )
 */
class FormBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\comment_form\Form\WorkForm');
    return $form;
  }
}
