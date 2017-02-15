<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates native session class
 */    

if ( ! class_exists('Native_session'))
{
    require_once(APPPATH.'libraries/Native_session'.EXT);
}

// sessions engine should run on cookies to minimize opportunities
// of session fixation attack
ini_set('session.use_only_cookies', 1);

$obj =& get_instance();
$obj->session = new Native_session();
$obj->ci_is_loaded[] = 'session';