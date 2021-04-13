<?php

/**
 * @file
 * Contains \Drupal\webauth\Controller\WebauthController.
 */

namespace Drupal\webauth\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Site\Settings;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

class WebauthController extends ControllerBase {

  /**
   * webauth/preparelogin
   * setzt das drupal-session-cookie und leitet auf webauth weiter
   * @return RedirectResponse
   */
  public function prepareLogin() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    //setzt nur das doofe cookie
    $session = \Drupal::service('session');
    $session->start();
    //speichere irgendwas, sonst wird die session nicht persistent

    $session->set('webauth_login_prepared', true);
    $session->migrate();

    return new RedirectResponse('/' . drupal_get_path('module', 'webauth') . '/webauth_protected?q=' . time(), 302);
  }

  /**
   * webauth/login
   * man kam von webauth, dieses leitet einen hierauf weiter. 
   * Der Parameter $base64_encryptedlogin beinhaltet den verschlüsselten Text DRUPALSESSID::WEBAUTH_UNAME
   * Dieser WEBAUTH_UNAME wird nun in der session gespeichert. Anschließend wird auf <home> weitergeleitet, wo dann der Webauth-Authentifizierungs-Provider greif 
   * @param type $base64_encryptedlogin verschlüsselter Text DRUPALSESSID::WEBAUTH_UNAME
   * @return RedirectResponse
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   */
  public function login($base64_encryptedlogin) {
    $encryptedlogin = base64_decode($base64_encryptedlogin);


    $decrypted = secured_decrypt($encryptedlogin);
    if (!$decrypted) {
      $this->messenger()->addError($this->t('Login decryption unsuccessful'));
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($this->t('Login decryption unsuccessful'));
    }
    $decrypted_explode = explode('::', $decrypted);
    $decrypted_sid = $decrypted_explode[0];
    $decrypted_uname = $decrypted_explode[1];

    if (session_id() != $decrypted_sid) {
      $this->messenger()->addError($this->t('Your Login was not accepted'));
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($this->t('Your Login was not accepted'));
    }

    $session = \Drupal::service('session');
    $session->start();

    $session->set('webauth_uname', $decrypted_uname);
    $this->messenger()->addMessage($this->t('Hello %s, you are logged in now', array('%s' => $decrypted_uname)));
    return new RedirectResponse(\Drupal::url('<front>', [], ['absolute' => TRUE]));
    /*
      return [
      '#markup' => 'session_id:' . session_id() . '<br>' .
      '$decrypted_uname:' . $decrypted_uname . '<br>' .
      'encrypted login:' . $encryptedlogin . '<p>' . '<br>' .
      'decrypted:' . $decrypted . '<p>' . '<br>' .
      $this->t('Simple page: The quick brown fox jumps over the lazy dog.') . '</p>',
      ]; */
  }

  /**
   * switch to another username 
   * access is defined in webauth.services.yml and implemented in switchAccess()
   * @param type $switch_uname
   * @return RedirectResponse
   */
  public function switch($switch_uname) {


    $session = \Drupal::request()->getSession();
    $session->set('webauth_switch_uname', $switch_uname);
    $this->messenger()->addMessage($this->t('You are switching to %s', array('%s' => $switch_uname)));
    return new RedirectResponse(\Drupal::url('<front>', [], ['absolute' => TRUE]));
  }

  public function test() {
    //https://h5plabor.div.onlinekurslabor.de/webauth/switch/strehlbe_user1
    //
    //TODO CHECK: AM I ADMIN?

    $session = \Drupal::request()->getSession();

    //das hat leider keine auswirkung! WARUM????
    $session->set('test_variable', "in_test_func");
    return [
      '#markup' => 'die seite geht.<br>'
    ];
  }

  public function set() {
    //https://h5plabor.div.onlinekurslabor.de/webauth/switch/strehlbe_user1
    //
    //TODO CHECK: AM I ADMIN?
    #    $session_manager = \Drupal::service('session_manager');
    #  $session_manager->start();
    $session = \Drupal::request()->getSession();
    //das hat leider keine auswirkung! WARUM????
    $session->set('setter', "funzioniert");
    return [
      '#markup' => 'tempstore:' . $session->get('setter') . '<br>' . '<p>' . '<br>'
    ];
  }

  public function get() {
    //https://h5plabor.div.onlinekurslabor.de/webauth/switch/strehlbe_user1
    //
    //TODO CHECK: AM I ADMIN?
    #    $session_manager = \Drupal::service('session_manager');
    #  $session_manager->start();
    $session = \Drupal::request()->getSession();
    //das hat leider keine auswirkung! WARUM????

    return [
      '#markup' => 'tempstore:' . $session->get('setter') . '<br>' . '<p>' . '<br>'
    ];
  }

}
