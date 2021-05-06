<?php

namespace Drupal\h5plabor\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Cache\Cache;

class PersonalizedPageLink extends MenuLinkDefault {

  public function getTitle() {
    if (\Drupal::currentUser()->isAuthenticated()) {
      return ucfirst(\Drupal::currentUser()->getAccountName());
    }
    return null;
  }

  /**
   * Don't cache! Else: username for everyone the same.
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

  public function isEnabled() {
    return \Drupal::currentUser()->isAuthenticated();
  }

}
