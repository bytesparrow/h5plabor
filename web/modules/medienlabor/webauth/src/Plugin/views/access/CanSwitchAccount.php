<?php

namespace Drupal\webauth\Plugin\views\access;

use Drupal\views\Plugin\views\access\AccessPluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

use Symfony\Component\Routing\Route;

/**
 * Access plugin that provides access control for switching to other accounts
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "canswitchaccount",
 *   title = @Translation("Can switch to other accounts"),
 *   help = @Translation("Is the authorized user as admin permission, he may switch to another account.")
 * )
 */

class CanSwitchAccount extends AccessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $userinfoservice = \Drupal::service('webauth.user_info');;
    return $userinfoservice->canCurrentUserSwitch();
  }

  /**
   * {@inheritdoc}
   */
   public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_access', 'TRUE');
  } 
}
