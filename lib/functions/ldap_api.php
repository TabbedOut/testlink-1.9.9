<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource  ldap_api.php
 * 
 * @author This piece of software has been copied and adapted from:
 *    Mantis - a php based bugtracking system (GPL)
 *    Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 *    Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
 * @author franciscom (code adaptation)
 *
 * LDAP API (authentication)
 *
 *
 * @internal revisions
 * @since 1.9.4
 * 20111204 - franciscom - TICKET 4830: Login Failure message has to be improved when using LDAP
 * 20111203 - franciscom - some minor improvements on info when connect/auth fail
 *               based again on Mantis code 
 *
 */
  
// Connect and bind to the LDAP directory
function ldap_connect_bind( $p_binddn = '', $p_password = '' ) 
{
  $ret = new stdClass();
  $ret->status = 0;
  $ret->handler = null;
  $ret->info  = 'LDAP CONNECT OK';

  $authCfg = config_get('authentication');
  
  $t_message = "Attempting connection to LDAP ";
  $t_ldap_uri = parse_url($authCfg['ldap_server']);
  if(count( $t_ldap_uri ) > 1) 
  {
    $t_message .= "URI {$authCfg['ldap_server']}.";
    $t_ds = ldap_connect($authCfg['ldap_server']);
  } 
  else 
  {
    $t_message .= "server {$authCfg['ldap_server']} port {$authCfg['ldap_port']}.";
    if(is_numeric($authCfg['ldap_port'])) 
    {
      $t_ds = ldap_connect($authCfg['ldap_server'],$authCfg['ldap_port']);
    } 
  }

  // IMPORTANT NOTICE from PHP Manual 
  // ldap_connect()
  // Returns a positive LDAP link identifier on success, or FALSE on error. 
  // When OpenLDAP 2.x.x is used, ldap_connect() will always return a resource as it does not 
  // actually connect but just initializes the connecting parameters. 
  // The actual connect happens with the next calls to ldap_* funcs, usually with ldap_bind(). 
  // 
  // For TestLink Developers
  // if you use -  echo 'ldap_errno:' . ldap_err2str(ldap_errno($t_ds ));
  // you will get Success!!!, no matter what has happened
  //
  if( $t_ds !== false && $t_ds > 0 ) 
  {
    ldap_set_option($t_ds, LDAP_OPT_PROTOCOL_VERSION, $authCfg['ldap_version']);
    ldap_set_option($t_ds, LDAP_OPT_REFERRALS, 0);
    $bind_method = $authCfg['ldap_tls'] ? 'ldap_start_tls' :'ldap_bind'; 

    $ret->handler=$t_ds;

    # If no Bind DN and Password is set, attempt to login as the configured
    #  Bind DN.
    if( is_blank( $p_binddn ) && is_blank( $p_password ) ) 
    {
      $p_binddn = $authCfg['ldap_bind_dn'];
      $p_password = $authCfg['ldap_bind_passwd'];
    }

    if ( !is_blank( $p_binddn ) && !is_blank( $p_password ) ) 
    {
      $t_br = $bind_method( $t_ds, $p_binddn, $p_password );
    } 
    else 
    {
      # Either the Bind DN or the Password are empty, so attempt an anonymous bind.
      $t_br = $bind_method( $t_ds );
    }
    
    if ( !$t_br ) 
    {
      $ret->status = ERROR_LDAP_BIND_FAILED;
      $ret->info  = 'ERROR_LDAP_BIND_FAILED';
    }
    
  } 
  else 
  {
    // IMPORTANT NOTICE from PHP Manual
    // ldap_connect()
    // When OpenLDAP 2.x.x is used, ldap_connect() will always return a resource as it does not 
    // actually connect but just initializes the connecting parameters. 
    //
    // For TestLink Developers: 
    // previous notice means that we will enter this section depending LDAP server we are using.
    //
    $ret->status = ERROR_LDAP_SERVER_CONNECT_FAILED;
    $ret->info = 'ERROR_LDAP_SERVER_CONNECT_FAILED';
  }

  // TICKET 4830: Login Failure message has to be improved when using LDAP
  // now we can check for errors because we have done an operation OTHER THAN ldap-connect()
  // See notice about PHP Manual
  $ret->errno = ldap_errno($t_ds);
  $ret->error = ldap_err2str($ret->errno);

  // Check for negative after have done test configuring UNREACHEABLE LDAP server
  // and check value returned by ldap_errno($t_ds);
  if($ret->errno < 0)  
  {
    $ret->status = ERROR_LDAP_SERVER_CONNECT_FAILED;
    $ret->info = 'ERROR_LDAP_SERVER_CONNECT_FAILED';
  } 

  return $ret;
}


// ----------------------------------------------------------------------------
// Attempt to authenticate the user against the LDAP directory
function ldap_authenticate( $p_login_name, $p_password ) 
{
  # if password is empty and ldap allows anonymous login, then
  # the user will be able to login, hence, we need to check
  # for this special case.
  if ( is_blank( $p_password ) ) 
  {
    return false;
  }

  $t_authenticated = new stdClass();
  $t_authenticated->status_ok = TRUE;
  $t_authenticated->status_code = null;
  $t_authenticated->status_verbose = '';

  $authCfg = config_get('authentication');

  $t_ldap_organization = $authCfg['ldap_organization'];
  $t_ldap_root_dn = $authCfg['ldap_root_dn'];
  $t_ldap_uid_field = $authCfg['ldap_uid_field']; // 'uid' by default

  $t_username = $p_login_name;

  $t_search_filter = "(&$t_ldap_organization($t_ldap_uid_field=$t_username))";
  $t_search_attrs = array( $t_ldap_uid_field, 'dn' );
  $t_connect = ldap_connect_bind();

  if( $t_connect->status == 0 )
  {
    $t_ds = $t_connect->handler;
        
    # Search for the user id
    $t_sr = ldap_search( $t_ds, $t_ldap_root_dn, $t_search_filter, $t_search_attrs );
    $t_info = ldap_get_entries( $t_ds, $t_sr );
    
    $t_authenticated->status_ok = false;
    $t_authenticated->status_code = ERROR_LDAP_AUTH_FAILED;
    $t_authenticated->status_verbose = 'ERROR_LDAP_AUTH_FAILED';
        
        
    if ( $t_info ) 
    {
      # Try to authenticate to each until we get a match
      for ( $idx = 0 ; $idx < $t_info['count'] ; $idx++ ) 
      {
        $t_dn = $t_info[$idx]['dn'];
    
        # Attempt to bind with the DN and password
        if ( @ldap_bind( $t_ds, $t_dn, $p_password ) ) 
        {
          $t_authenticated->status_ok = true;
          break; # Don't need to go any further
        }
      }
    }
    
    ldap_free_result( $t_sr );
    ldap_unbind( $t_ds );
  }
  else
  {
    $t_authenticated->status_ok = false;
    $t_authenticated->status_code = $t_connect->status;
    $t_authenticated->status_verbose = 'LDAP CONNECT FAILED';
  }
    
  return $t_authenticated;
}


/**
 * Escapes the LDAP string to disallow injection.
 *
 * @param string $p_string The string to escape.
 * @return string The escaped string.
 */
function ldap_escape_string( $p_string ) 
{
  $t_find = array( '\\', '*', '(', ')', '/', "\x00" );
  $t_replace = array( '\5c', '\2a', '\28', '\29', '\2f', '\00' );

  $t_string = str_replace( $t_find, $t_replace, $p_string );

  return $t_string;
}

?>
