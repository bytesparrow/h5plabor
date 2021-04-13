## Summary

This plugin enables login to drupal via webauth https://uit.stanford.edu/service/authentication
In contradiction to other plugins you can still access drupal, but you can login using webauth.

* Procedure
- click login
- webauth/preparelogin is called. Here the drupal-session is started
- you are forwared to the folder /plugin/webauth_protected/
- the folder redirects you to your central webauth-login
- you authenticate on the central webauth-login and get redirected back to /plugin/webauth_protected/
- /plugin/webauth_protected/index.php is opened and your username gets encrypted.
- you are redirected to /webauth/login/--encryptedusername--
- now the function WebauthController::login decrypts the username and stores it in the session.
- on every page call WebauthAuthentication is invoked and checks your authentication status


## Instructions
- Create Two Random Keys And Save Them In Your Configuration File ---
<?php
// Create The First Key
echo base64_encode(openssl_random_pseudo_bytes(32));
// Create The Second Key
echo base64_encode(openssl_random_pseudo_bytes(64));
?>
- create an array:
$settings['webauth_secret_key'][0] = '--FIRSTKEY--';
$settings['webauth_secret_key'][1] = '--SECONDKEY--';
- save it in setttings.php

- enable this plugin.

### Related modules

* webserver_auth: a very mighty plugin. But it blocks access to your drupal for everyone not authenticated

## Current maintainers:
* Bernhard Strehl https://www.drupal.org/u/bernhard-strehl