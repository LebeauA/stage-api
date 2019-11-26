<?php
/**
 * @file
 * Contains \Drupal\dropzone\Plugin\Block\Dropzone.
 */

namespace Drupal\dropzone\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\NodeInterface;

/**
 * Provides a dropzone block.
 *
 * @Block(
 *   id = "dropzone_block",
 *   admin_label = @Translation("Files drop zone")
 * )
 */
class Dropzone extends BlockBase {
  public function build() {
    $items = array();
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
    }
    $build= array(
      '#items' => $items,
      '#theme' => 'dropzone',
      '#project' => $node->id(),

      '#attached' => array(
        'library' => array('Dropzone/dropzonePerso','Dropzone/dropzonejs'),
      ),
    );
    $build['#cache']['max-age']=0;
    return $build;


  }
}
