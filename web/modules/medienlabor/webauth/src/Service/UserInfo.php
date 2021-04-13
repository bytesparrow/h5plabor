<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//https://www.drupal.org/forum/support/module-development-and-code-questions/2019-05-21/creating-a-custom-function-and
// The namespace is Drupal\[module_key]\[Directory\Path(s)]

namespace Drupal\webauth\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
/**
 * The DoStuff service. Does a bunch of stuff.
 */
class UserInfo {

  protected $session;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  public function __construct(SessionInterface $session,Connection $connection) {
    # var_dump("I GOT INJECTED");
    //uname
    //uname_switch
    //is_switched
    //can_switch
    $this->session = $session;
    $this->connection = $connection;
  }

  /**
   * Does something.
   *
   * @return string
   *   Some value.
   */
  public function doSomething() {
    // Build $value (not shown)
    $value = "HAHA";
    return $value;
  }

  public function getValuesForUname(String $uname): array {

    $uid = $this->getUidForUsername($uname);
    // Retrieving user data.
    $values = $this->connection->query('SELECT * FROM {users_field_data} u WHERE u.uid = :uid AND u.default_langcode = 1', [':uid' => $uid])
      ->fetchAssoc();

    // Add the user's roles.
    $rids = $this->connection->query('SELECT roles_target_id FROM {user__roles} WHERE entity_id = :uid', [':uid' => $uid])
      ->fetchCol();

    $values['roles'] = array_merge([AccountInterface::AUTHENTICATED_ROLE], $rids);
    
    return $values;
  }

  public function isAuthenticated(): bool {

    # var_dump($this->session->get('webauth_uname'));
    return !is_null($this->session->get('webauth_uname'));
  }

  public function getCurrentAuthname(): string {
    return $this->session->get('webauth_uname');
  }

  public function getCurrentUsername(): string {
    $switchname = $this->session->get('webauth_switch_uname');
    # var_dump($switchname);
    if (!$switchname) {
      return $this->getCurrentAuthname();
    }
    return $switchname;
  }

  /**
   * TOTEST
   * @return bool
   */
  public function canCurrentUserSwitch(): bool {

    if(!\Drupal::currentUser()->isAuthenticated())
    {
      return false;
    }
    $uname = $this->getCurrentAuthname();
    $uid = $this->getUidForUsername($uname);
    return User::load($uid)->hasRole('administrator');
  }

  public function getUidForUsername($uname): int {
    $us = user_load_by_name($uname);

    return is_object($us) ? $us->id() : 0;
  }

}
