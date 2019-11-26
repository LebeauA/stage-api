<?php
/**
 * @file
 * Contains \Drupal\comment_form\Plugin\Block\FormBlock.
 */
namespace Drupal\to_excel\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides a button block to export a xlsx file block.
 *
 * @Block(
 *   id = "button_export_block",
 *   admin_label = @Translation("Button export to excel block"),
 *   category = @Translation("Custom form block")
 * )
 */
class ToExcelBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\to_excel\Form\ToExcelForm');
    return $form;
  }
}
