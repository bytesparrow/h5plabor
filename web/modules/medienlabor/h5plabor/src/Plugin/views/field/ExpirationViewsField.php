<?php

namespace Drupal\h5plabor\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("expiration_views_field")
 */
class ExpirationViewsField extends FieldPluginBase {

  /**
   * The current display.
   *
   * @var string
   *   The current display of the view.
   */
  protected $currentDisplay;

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->currentDisplay = $view->current_display;
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // First check whether the field should be hidden if the value(hide_alter_empty = TRUE) /the rewrite is empty (hide_alter_empty = FALSE).
    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {  #return 123;
    $node = $values->_entity;

    $sql = "select node_table.nid as nid, GREATEST(COALESCE(node_table.changed,0), COALESCE(history_maxed.latest_access,0)) as node_last_access from (
    #node+node_field_data ergibt bearbeitungszeitpunkt
    select h5p_nodes.nid, h5p_nodes.type, status, uid, title, created, changed from ( select node.nid, node.type from node where node.type = 'h5p') h5p_nodes  join node_field_data on h5p_nodes.nid=node_field_data.nid) node_table LEFT join 
    #+letzter zugriff in history
    (select nid, max(timestamp) as latest_access from history group by nid) history_maxed on
    node_table.nid = history_maxed.nid 
     
     where node_table.nid = :nid";

    $database = \Drupal::database();
    $query = $database->query($sql, [
      ':nid' => $node->id()
    ]);

    $result = $query->fetchField(1);

    if ($result) {
      $relative_delete_timediff = \Drupal::config('h5plabor.settings')->get('orphans.delete_timeout');

      $expirydate = strtotime(str_replace('-', '+', $relative_delete_timediff), $result);
      return date('d.m.Y', $expirydate);
    }
    else { //just a fallback, should not happen
      return "-";
    }
    
  }

}
