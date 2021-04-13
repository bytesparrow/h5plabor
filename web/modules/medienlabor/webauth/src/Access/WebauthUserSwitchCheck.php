<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\webauth\Access;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;

/**
 * Checks access for displaying configuration translation page.
 */
class WebauthUserSwitchCheck implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    $userinfoservice = \Drupal::service('webauth.user_info');
 
    return AccessResult::allowedIf($userinfoservice->canCurrentUserSwitch());
    /* return $userinfoservice->canCurrentUserSwitch()?AccessResult::allowed():;
      return AccessResult::forbidden(); */
    ;

#    return ($account->hasPermission('access content overview within business hours') && $this->checkTimeAccess()) ? AccessResult::allowed() : AccessResult::forbidden();
  }

}
