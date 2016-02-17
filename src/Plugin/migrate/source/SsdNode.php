<?php

/**
 * @file
 * Contains \Drupal\s_exampsdle\Plugin\migrate\source\SsdNode.
 */

namespace Drupal\ssd_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for ssd content.
 *
 * @MigrateSource(
 *   id = "ssd_node"
 * )
 */
class SsdNode extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. Here we might be tempted to add a join to
     * ssd_example_ssd_topic_node in our query, to pull in the
     * relationships to our categories. Doing this would cause the query to
     * return multiple rows for a given node, once per related value, thus
     * processing the same node multiple times, each time with only one of the
     * multiple values that should be imported. To avoid that, we simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     */
    $query = $this->select('ssd_example_ssd_node', 'b')
                 ->fields('b', ['bbid', 'title', 'dt_created','abstract','article','aid']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'bbid' => $this->t('Ssd ID'),
      'title' => $this->t('Title of ssd'),
      'dt_created' => $this->t('Creation date of ssd'),
      'abstract' => $this->t('Abstract for this ssd'),
      'article' => $this->t('Article of ssd'),
      'aid' => $this->t('Account ID of the author'),
      'terms' => $this->t('Applicable styles'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'bbid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /**
     * As explained above, we need to pull the style relationships into our
     * source row here, as an array of 'style' values (the unique ID for
     * the ssd_term migration).
     */
    $terms = $this->select('ssd_example_ssd_topic_node', 'bt')
                 ->fields('bt', ['style'])
      ->condition('bbid', $row->getSourceProperty('bbid'))
      ->execute()
      ->fetchCol();
    $row->setSourceProperty('terms', $terms);

    // As we did for favorite ssd in the user migration, we need to explode
    // the multi-value country names.
    if ($value = $row->getSourceProperty('countries')) {
      $row->setSourceProperty('countries', explode('|', $value));
    }
    return parent::prepareRow($row);
  }

}
