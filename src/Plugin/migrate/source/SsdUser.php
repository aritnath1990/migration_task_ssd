<?php

/**
 * @file
 * Contains \Drupal\ssd_example\Plugin\migrate\source\SsdUser.
 */

namespace Drupal\ssd_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for Ssd user accounts.
 *
 * @MigrateSource(
 *   id = "ssd_user"
 * )
 */
class SsdUser extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('ssd_example_ssd_account', 'mea')
      ->fields('mea', ['aid', 'status', 'registered', 'username', 'nickname',
                            'password', 'email', 'sex', 'ssds']);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'aid' => $this->t('Account ID'),
      'status' => $this->t('Blocked/Allowed'),
      'registered' => $this->t('Registered date'),
      'username' => $this->t('Account name (for login)'),
      'nickname' => $this->t('Account name (for display)'),
      'password' => $this->t('Account password (raw)'),
      'email' => $this->t('Account email'),
      'sex' => $this->t('Gender'),
      'ssds' => $this->t('Favorite ssds, pipe-separated'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'aid' => [
        'type' => 'integer',
        'alias' => 'mea',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /**
     * prepareRow() is the most common place to perform custom run-time
     * processing that isn't handled by an existing process plugin. It is called
     * when the raw data has been pulled from the source, and provides the
     * opportunity to modify or add to that data, creating the canonical set of
     * source data that will be fed into the processing pipeline.
     *
     * In our particular case, the list of a user's favorite ssds is a pipe-
     * separated list of ssd IDs. The processing pipeline deals with arrays
     * representing multi-value fields naturally, so we want to explode that
     * string to an array of individual ssd IDs.
     */
    if ($value = $row->getSourceProperty('ssds')) {
      $row->setSourceProperty('ssds', explode('|', $value));
    }
    /**
     * Always call your parent! Essential processing is performed in the base
     * class. Be mindful that prepareRow() returns a boolean status - if FALSE
     * that indicates that the item being processed should be skipped. Unless
     * we're deciding to skip an item ourselves, let the parent class decide.
     */
    return parent::prepareRow($row);
  }

}
