<?php

require(dirname(dirname(dirname(dirname(__DIR__)))).'/vendor/autoload.php');
use Symfony\Component\HttpFoundation\RedirectResponse;
 #var_dump($_COOKIE);
foreach(array_keys($_COOKIE) as $cookietitle)
{
  if(strstr($cookietitle, 'SSESS'))
  {
    $drupal_cookiename = $cookietitle;
  }
}

$drupal_sid = $_COOKIE[$drupal_cookiename];
if(!$drupal_cookiename || !$drupal_sid)
{ # var_dump($_COOKIE);
  echo ("no drupal cookie found! - login cancelled");
  return;
}


if(!empty($_SERVER['WEBAUTH_USER']))
{
  $webauth_uname =  $_SERVER['WEBAUTH_USER'];
 }



 
//weiterleiten per $_GET geht ja auch nicht, da ich nicht auf den secret key komme
   
//MUST require settings before secured_en_decrypt
require($_SERVER['DOCUMENT_ROOT'] . '/sites/default/settings.php');
require(dirname(dirname(__FILE__)).'/secured_en_decrypt.php');

$secret_uname_b64 = base64_encode(secured_encrypt($drupal_sid.'::'.$webauth_uname));
/*
echo "My Account is: $webauth_uname";
echo "<br>";
echo "encrypted +base64 is: ".$secret_uname_b64;
echo "<br>";
echo "goto: ".'/webauth/login/'.$secret_uname_b64;
*/
 $redirectResponse =  new RedirectResponse('/webauth/login/'.$secret_uname_b64);
 $redirectResponse->send();
 
return;
 $encryption_key = \Drupal::state()->get('webauth_key'); 
 var_dump($encryption_key);
return;


//DB geht auch nicht, da hier noch kein Zugriff besteht
require('/var/www/vhosts/div.onlinekurslabor.de/h5plabor.div.onlinekurslabor.de/web/web/autoload.php');
/** @var \Drupal\Core\Database\Connection $connection */
$connection = \Drupal::service('database');
$result = $connection->insert('webauth')
  ->fields([
    'sid' => $drupal_sid,
    'webauth_uname' => 'strehlbe'
  ])
  ->execute();
var_dump("insert-success: ");
var_dump($result);
return;

/////////////FOLGENDER SHICE GEHT WEGEN DIVERSER WARNINGS NICHT
  echo '$drupal_cookiename:'.$drupal_cookiename;
##\Symfony\Component\HttpFoundation\Session::start();
use Symfony\Component\HttpFoundation\Session\Session;
  session_set_cookie_params([
            'secure' => true,
            'path' => '/',
            'domain' => '.h5plabor.div.onlinekurslabor.de',
            'httponly' => true,
        ]);
  var_dump( "before name");
        session_name($drupal_cookiename);
        
 $session =   new Session();
 $session->start();
  $session->migrate();
    $session->set('ml_webauth_sid', $_SERVER['WEBAUTH_USER']);
   
 $sessid = session_id();
        var_dump('$sessid:'.$sessid);
        return;
# $session->setOptions(array('domain' => '','httponly'=>true));
  var_dump( "after name");
 
  #$session->regenerate();
#var_dump("started: ".$session->isStarted());
if (!$session->isStarted()) {
   $session->migrate();
}
 
  

if(!empty($_SERVER['WEBAUTH_USER']))
{
  $session->set('ml_webauth_sid', $_SERVER['WEBAUTH_USER']);
    $session->save();  
    echo "after set";
}

else
  echo "no sess!";
 #var_dump($_SESSION['ml_webauth_sid']);
var_dump( "all good?");
 return;
 var_dump('ml_webauth_sid:'. $session->get('ml_webauth_sid'));
 #return new RedirectResponse(Drupal\Core\Url::fromUri('admin')); 
 echo 1234;
 $session->getFlashBag()->add('notice', 'sth is odd');
 #echo \Drupal::url('<front>', [], ['absolute' => TRUE]);
 $session->save();
 var_dump('ID:'.$session->getId());var_dump('ID:'.$session->getId());
 var_dump($_SESSION);
//todo: fix path
# header('location:/admin');

?>
