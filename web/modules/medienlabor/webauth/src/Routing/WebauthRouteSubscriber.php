<?php

namespace Drupal\webauth\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class WebauthRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    // Change path '/user/login' to '/webauth/preparelogin'.
    if ($route = $collection->get('user.login')) {
      #$route->setPath('/webauth/preparelogin');

      $options = $route->getOptions();
      $options['no_cache'] = TRUE;
      $route->setOptions($options);

      $route->setDefault('_controller', '\Drupal\webauth\Controller\WebauthController::prepareLogin');
    }
    // Always deny access to '/user/logout'.
    // Note that the second parameter of setRequirement() is a string.
    if ($route = $collection->get('user.logout')) {
      $route->setRequirement('_access', 'FALSE');
    }

    if ($route = $collection->get('view.webauth_user_switch.page')) {
      $route->setRequirement('_custom_access', 'webauth.userswitch_access::access');
    }
  }

}
