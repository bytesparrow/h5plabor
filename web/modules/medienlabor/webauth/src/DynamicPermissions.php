<?php

namespace Drupal\webauth; 

/**
 * Class DynamicPermissions
 * @package Drupal\mymodule
 */
class DynamicPermissions
{
 

  /**
   * @return array
   */
  public function permissions()
  {
    $permissions = [];

    $count = 1;
    while ($count <= 5) {
      $permissions += [
        "mymodule permission $count" => [
          'title' => $this->t('mymodule permission @number', ['@number' => $count]),
          'description' => $this->t('This is a sample permission generated dynamically.'),
          'restrict access' => $count == 2 ? true : false,
        ],
      ];
      $count++;
    }
    return $permissions;
  }

}