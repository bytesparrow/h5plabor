<?php

namespace Drupal\webauth\Authentication\Provider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\UserSession;
use Drupal\Core\Session\SessionConfigurationInterface;
use Drupal\user\Entity\User;

/**
 * Cookie based authentication provider.
 */
class WebauthAuthentication implements AuthenticationProviderInterface {

  /**
   * The session configuration.
   *
   * @var \Drupal\Core\Session\SessionConfigurationInterface
   */
  protected $sessionConfiguration;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;
  protected $userInfoService;

  /**
   * Constructs a new webserver authentication provider.
   *
   * @param \Drupal\Core\Session\SessionConfigurationInterface $session_configuration
   *   The session configuration.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   *
   * @param \Drupal\webserver_auth\WebserverAuthHelper $helper
   *   Helper class that brings some helper functionality related to webserver authentication.
   */
  public function __construct(SessionConfigurationInterface $session_configuration, Connection $connection) {
    $this->sessionConfiguration = $session_configuration;
    $this->connection = $connection;
    $this->userInfoService = \Drupal::service('webauth.user_info');
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request) {


    // If our module is enabled, we want this auth provider to
    // be always preferable.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {

    if (!$this->userInfoService->isAuthenticated()) {
      return null;
    }

    /* GEHT
      $session = \Drupal::request()->getSession();
      $session->set('setter', "in authenticate"); */
    return $this->getUserFromSession($request->getSession(), $request);
  }

  /**
   * Returns the UserSession object for the given session.
   *
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\Core\Session\AccountInterface|null The UserSession object for the current user, or NULL if this is an
   *   The UserSession object for the current user, or NULL if this is an
   *   anonymous session.
   */
  protected function getUserFromSession(SessionInterface $session, Request $request) {

    //make VAR?
    $webauth_cookie_name = 'webauth_at';

    // Checking if we got remote user set.
    $current_authname = $this->userInfoService->getCurrentAuthname();
    $current_username = $this->userInfoService->getCurrentUsername();

    // Loging user out if no authname provided, but drupal still keeps user logged in.
    if ($current_authname && empty($_COOKIE[$webauth_cookie_name])) {
      #    var_dump("FAIL: " . $session->get('webauth_auth'));
      // We don't to keep user logged in anymore.
      $session->remove('webauth_uname');
      $session->remove('webauth_switch_uname');
      return NULL;
    }

    $uid = $this->userInfoService->getUidForUsername($current_username);


    // Check if the user data was found. We've already validated status by that point.
    if (!$uid && $current_username) {
      //register new account
      $newuserobject = $this->createNewUser($current_username);
      $uid = $newuserobject->uid;
    }



    $values = $this->userInfoService->getValuesForUname($current_username);

    $user_session = new UserSession($values);
    return $user_session;
  }

  public function createNewUser($authname) {
    // Generating password. It won't be used, but we still don't want
    // to use empty password or same password for all users.
    $pass = \Drupal::service("password_generator")->generate(12);
    
    $data = [
      'name' => $authname,
      'pass' => $pass,
    ];

    $user = User::create($data);
    $user->activate();
    $user->save();

    return $user;
  }

}
