<?php

/**
 * SQL Password Driver
 *
 * Driver for passwords stored in SQL database
 *
 * @version 1.3
 * @author Aleksander 'A.L.E.C' Machniak <alec@alec.pl>
 *
 */

function password_save($curpass, $passwd)
{
    $cmail = cmail::get_instance();
	
	
	
	$check_last=$cmail->config->get('password_check_last');
	/////////////////////////// pegando senhas antigas
	// conectando ao  banco de dados
	$current_password=$last_password=$second_last_password=0;
	if($check_last)
	{
		if ($dsn = $cmail->config->get('password_db_dsn')) 
		{
			// #1486067: enable new_link option
			if (is_array($dsn) && empty($dsn['new_link']))
				$dsn['new_link'] = true;
			else if (!is_array($dsn) && !preg_match('/\?new_link=true/', $dsn))
			  $dsn .= '?new_link=true';
	
			$db = new crystal_mdb2($dsn, '', FALSE);
			$db->set_debug((bool)$cmail->config->get('sql_debug'));
			$db->db_connect('r');
		} 
		else 
		{
			$db = $cmail->get_dbh();
	    }
		
		
	    if ($db->is_error())
		{
			$cmail->output->command('display_message',$this->gettext('connecterror'), 'error');
	        return ;
		}
		
		
		$user=$db->quote($_SESSION['username'],'text');
		
		$sql = "SELECT `password`,`last_password`,`second_last_password` FROM `mailbox` WHERE(`username` = %u ) LIMIT 1";
		//$sql = $cmail->config->get('pf_alias_sql_select');
		$sql = str_replace('%u', $user, $sql);
		
	
		$res = $db->query($sql);
		
	    if (!$db->is_error()) 
		{
			if ($result = $db->fetch_array($res))
			{
				//sucesso
			}
			else
			{
				$cmail->output->command('display_message',$this->gettext('connecterror'), 'error'); // TODO arrumar mensagens de erro
				return ;
			}
	    }
		else
		{
			$cmail->output->command('display_message',$this->gettext('connecterror'), 'error');
			return ;
		}
		if($result!=0)
		{
			$current_password=$result[0];
			$last_password=$result[1];
			$second_last_password=$result[2];
		}
	
	}
	/////////////////////////

	
    if (!($sql = $cmail->config->get('password_query')))
        $sql = 'SELECT update_passwd(%c, %u)';

    if ($dsn = $cmail->config->get('password_db_dsn')) {
	// #1486067: enable new_link option
	if (is_array($dsn) && empty($dsn['new_link']))
	    $dsn['new_link'] = true;
	else if (!is_array($dsn) && !preg_match('/\?new_link=true/', $dsn))
	  $dsn .= '?new_link=true';

        $db = new crystal_mdb2($dsn, '', FALSE);
        $db->set_debug((bool)$cmail->config->get('sql_debug'));
        $db->db_connect('w');
    } else {
        $db = $cmail->get_dbh();
    }

    if ($err = $db->is_error())
        return PASSWORD_ERROR;
    
    // crypted password
    if (strpos($sql, '%c') !== FALSE) {
        $salt = '';
        if (CRYPT_MD5) { 
    	    $len = rand(3, CRYPT_SALT_LENGTH);
        } else if (CRYPT_STD_DES) {
    	    $len = 2;
        } else {
    	    return PASSWORD_CRYPT_ERROR;
        }
        for ($i = 0; $i < $len ; $i++) {
    	    $salt .= chr(rand(ord('.'), ord('z')));
        }
        $sql = str_replace('%c',  $db->quote(crypt($passwd, CRYPT_MD5 ? '$1$'.$salt.'$' : $salt)), $sql);
    }
    
    // hashed passwords
    if (preg_match('/%[n|q]/', $sql)) 
	{

		if (!extension_loaded('hash')) {
			raise_error(array(
				'code' => 600,
			'type' => 'php',
			'file' => __FILE__,
			'message' => "Password plugin: 'hash' extension not loaded!"
			), true, false);
			return PASSWORD_ERROR;			    
		}

		if (!($hash_algo = strtolower($cmail->config->get('password_hash_algorithm'))))
				$hash_algo = 'sha1';
				
		/*
		*  This was edited to add suport to cram-md5, the encryption method used by dovecot
		*  For credits, see password.php
		*/
		if($hash_algo=='cram-md5')
		{	
			$basedir=getcwd();
			
			
			//$dovecotpw = "/var/www/webmail-new/plugins/password/drivers/dovecotpw";// caminho completo até o executável
			$dovecotpw=$basedir."/plugins/pf_password/drivers/dovecotpw";
			//echo "<hr><br>basedir<br>".print_r($basedir)."<br><hr>";
                        //echo "<hr><br>dovecotpw<br>".print_r($dovecotpw)."<br><hr>";

                        // uso de temporário previne que a senha, em texto plano, apareça na tabela de processos

			$method = "CRAM-MD5"; 
			$prefix = "postfixadmin-";
			$tmpfile = tempnam('/tmp', $prefix); // cria temporário na pasta tmp, com um prefixo postfixadmin, e um nome aleatório
			$pipe = popen("'$dovecotpw' -s '$method' > '$tmpfile'", 'w'); // chamada do programa encriptador
		
			
			if (!$pipe) {
				unlink($tmpfile);
				return PASSWORD_ERROR; // error with program calling
			} 
			else {
				// use dovecot's stdin, it uses getpass() twice
				fwrite($pipe, $passwd . "\n", 1+strlen($passwd)); usleep(1000);
				fwrite($pipe, $passwd . "\n", 1+strlen($passwd));
				pclose($pipe);
		
				$hash_passwd = file_get_contents($tmpfile); // gets the temporary content
			   unlink($tmpfile);

				if ( !preg_match('/^\{' . $method . '\}/', $hash_passwd)) {
				return PASSWORD_ERROR; // problems with dovecotpw execution
				}

				$hash_passwd = trim(str_replace('{' . $method . '}', '', $hash_passwd));
				
				
				// impedindo a senha de ser igual a alguma das ultimas duas
				if($hash_passwd == $current_password || $hash_passwd == $last_password || $hash_passwd == $second_last_password)
					return PASSWORD_NOT_DIFFERENT;

				//if($hash_passwd == $current_password || $hash_passwd == $last_password || $hash_passwd == $second_last_password)
				//	return PASSWORD_ERROR;
				
			}

		}
		else
		{       
			$hash_passwd = hash($hash_algo, $passwd);
				$hash_curpass = hash($hash_algo, $curpass);
			}
		if ($cmail->config->get('password_hash_base64')) {
				$hash_passwd = base64_encode(pack('H*', $hash_passwd));
				$hash_curpass = base64_encode(pack('H*', $hash_curpass));
			}
		
		$sql = str_replace('%n', $db->quote($hash_passwd, 'text'), $sql);
		$sql = str_replace('%q', $db->quote($hash_curpass, 'text'), $sql);
    }

    $user_info = explode('@', $_SESSION['username']);
    if (count($user_info) >= 2) {
	$sql = str_replace('%l', $db->quote($user_info[0], 'text'), $sql);
	$sql = str_replace('%d', $db->quote($user_info[1], 'text'), $sql);
    }
    
    $sql = str_replace('%u', $db->quote($_SESSION['username'],'text'), $sql);
    $sql = str_replace('%h', $db->quote($_SESSION['imap_host'],'text'), $sql);
    $sql = str_replace('%p', $db->quote($passwd,'text'), $sql);
    $sql = str_replace('%o', $db->quote($curpass,'text'), $sql);

	//guardando as ultimas senhas..
	 $sql = str_replace('%y', $db->quote($current_password,'text'), $sql);
	 $sql = str_replace('%z', $db->quote($last_password,'text'), $sql); 

    $res = $db->query($sql);

    if (!$db->is_error()) {
	if (strtolower(substr(trim($query),0,6))=='select') {
    	    if ($result = $db->fetch_array($res))
		return PASSWORD_SUCCESS;
	} else { 
    	    if ($db->affected_rows($res) == 1)
		return PASSWORD_SUCCESS; // This is the good case: 1 row updated
	}
    }

    return PASSWORD_ERROR;
}

?>
