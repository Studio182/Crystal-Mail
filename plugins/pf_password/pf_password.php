<?php

/*
  +-------------------------------------------------------------------------+
  | Password Plugin for Crystal Mail                                           |
  | Version 1.3.1                                                           |
  |                                                                         |
  | Copyright (C) 2009, RoundCube Dev.                                      |
  |                                                                         |
  | This program is free software; you can redistribute it and/or modify    |
  | it under the terms of the GNU General Public License version 2          |
  | as published by the Free Software Foundation.                           |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  |                                                                         |
  | You should have received a copy of the GNU General Public License along |
  | with this program; if not, write to the Free Software Foundation, Inc., |
  | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.             |
  |                                                                         |
  +-------------------------------------------------------------------------+
  | Author: Aleksander Machniak <alec@alec.pl>                              |
  +-------------------------------------------------------------------------+

  $Id: index.php 2645 2009-06-15 07:01:36Z alec $

 */

define('PASSWORD_CRYPT_ERROR', 1);
define('PASSWORD_ERROR', 2);
define('PASSWORD_CONNECT_ERROR', 3);
define('PASSWORD_NOT_DIFFERENT', 4);
define('PASSWORD_SUCCESS', 0);

/**
 * Change password plugin
 *
 * Plugin that adds functionality to change a users password.
 * It provides common functionality and user interface and supports
 * several backends to finally update the password.
 *
 * For installation and configuration instructions please read the README file.
 *
 * @version 1.3.1
 * @author Aleksander Machniak
 */
/*
 * 	Some changes made to add support to:
 *   -PostFixAdmin
 *   -CRAM-MD5 (dovecot's encryption)
 *   -MobileCube theme(the tab does not display in this theme)
 *
 * 	by Marcelo Salgado <msscelo@gmail.com>

 * 	edit /roundcube/program/localization/en_US/labels.inc, add $labels['changepasswd'] = 'Change Password';
 * 	you may also edit the following file, for pt_BR translation:
 * 	edit /roundcube/program/localization/pt_BR/labels.inc, add $labels['changepasswd'] = 'Alterar senha';

 * 	Some credits:
 * 	From postfixadmin 2.3, file functions.inc.php, created by christian_boltz, I used some code, where he access the dovecotpw safely.
 * 	http://postfixadmin.sourceforge.net/

 * 	Dovecotpw, created by Joshua Goodall.
 * 	http://www.dovecot.org/
 */
class pf_password extends crystal_plugin {

    public $task = 'settings';

    function init() {
        $cmail = cmail::get_instance();
        // add Tab label
        $this->add_texts('localization/');
        
        $this->load_config();        


        $this->add_hook('list_prefs_sections', array($this, 'list_prefs'));
        $this->add_hook('user_preferences', array($this, 'user_prefs'));
        $this->add_hook('save_preferences', array($this, 'save_prefs'));
    }

    function list_prefs($args) {
        $args['list']['password'] = array('id' => 'password', 'section' => $this->gettext('changepasswd'));
        return $args;
    }

    function user_prefs($args) {
        $this->add_texts('localization/');
        $cmail = cmail::get_instance();


        if ($args['section'] == 'password') {
            $this->include_script('force.js');
            $args['blocks']['password']['name'] = $this->gettext('password');

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


            if ($cmail->config->get('password_confirm_current')) {
                // show current password selection
                $field_id = 'curpasswd';
                $input_curpasswd = new html_passwordfield(array('name' => '_curpasswd', 'id' => $field_id,
                            'size' => 20, 'autocomplete' => 'off'));

                $args['blocks']['password']['options']['curpasswd_field'] = array
                    (
                    'title' => html::label($field_id, Q($this->gettext('curpasswd'))),
                    'content' => $input_curpasswd->show(),
                );
            }

            // show new password selection
            $field_id = 'newpasswd';
            //$input_newpasswd = new html_passwordfield(array('name' => '_newpasswd', 'id' => $field_id,      'size' => 20, 'autocomplete' => 'off'));
            $input_newpasswd = "<input type='password' autocomplete='off' size='20' id='newpasswd' name='_newpasswd' onkeyup = 'runPassword(document.forms.form._newpasswd.value)' > ";
            $args['blocks']['password']['options']['newpasswd_field'] = array
                (
                'title' => html::label($field_id, Q($this->gettext('newpasswd'))),
                'content' => $input_newpasswd,
            );

            //show the password strenght
            $field_id = 'strenght';
            $forca_senha = "
				<div style='width: 100px;'> 
					<div id='newpassword_text' style='font-size: 12px;'></div>
					<div id='newpassword_bar' style='font-size: 1px; height: 6px; width: 0px; border: 1px solid white;'></div> 
				</div>
				";

            $args['blocks']['password']['options']['strength'] = array
                (
                'title' => html::label($field_id, Q($this->gettext('pwdstrength'))),
                'content' => $forca_senha,
            );


            // show confirm password selection
            $field_id = 'confpasswd';
            $input_confpasswd = new html_passwordfield(array('name' => '_confpasswd', 'id' => $field_id,
                        'size' => 20, 'autocomplete' => 'off'));

            $args['blocks']['password']['options']['confpasswd_field'] = array
                (
                'title' => html::label($field_id, Q($this->gettext('confpasswd'))),
                'content' => $input_confpasswd->show(),
            );

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        return $args;
    }

    function save_prefs($args) {
        $cmail = cmail::get_instance();
        $this->load_config();


        if ($args['section'] == 'password') {
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $this->add_texts('localization/');
            $confirm = $cmail->config->get('password_confirm_current');
            $required_length = intval($cmail->config->get('password_minimum_length'));
            $check_strength = $cmail->config->get('password_require_nonalpha');
            $strength_level = $cmail->config->get('password_require_strength_level');
            if (($confirm && !isset($_POST['_curpasswd'])) || !isset($_POST['_newpasswd'])) {
                $cmail->output->command('display_message', $this->gettext('nopassword'), 'error');
                cmail_overwrite_action('plugin.pf_password');
                $cmail->output->send('plugin');
                return;
            } else {

                $curpwd = get_input_value('_curpasswd', RCUBE_INPUT_POST);
                $newpwd = get_input_value('_newpasswd', RCUBE_INPUT_POST);
                $conpwd = get_input_value('_confpasswd', RCUBE_INPUT_POST);

                if ($conpwd != $newpwd) {
                    $cmail->output->command('display_message', $this->gettext('passwordinconsistency'), 'error');
                    //cmail_overwrite_action('plugin.pf_password');
                    //$cmail->output->send('plugin');
                    return;
                } else if ($confirm && $cmail->decrypt($_SESSION['password']) != $curpwd) {
                    $cmail->output->command('display_message', $this->gettext('passwordincorrect'), 'error');
                    return;
                } else if ($required_length && strlen($newpwd) < $required_length) {
                    $cmail->output->command('display_message', $this->gettext(
                                    array('name' => 'passwordshort', 'vars' => array('length' => $required_length))), 'error');
                    return;
                } else if ($check_strength) {

                    /*
                      if(!preg_match("/[0-9]/", $newpwd) || !preg_match("/[^A-Za-z0-9]/", $newpwd))
                      {// TODO definir se este if é necessário
                      $cmail->output->command('display_message', $this->gettext('passwordweak'), 'error');
                      return ;
                      } */


                    $score = 0;
                    if (strlen($newpwd) < 5) {
                        $score += 5;
                    } else if (strlen($newpwd) > 4 && strlen($newpwd) < 8) {
                        $score += 10;
                    } else if (strlen($newpwd) > 7) {
                        $score += 25;
                    }

                    $tempmatch = preg_match_all("/[A-Z]/", $newpwd, $tempmatch);
                    //$UpperCount = count($tempmatch [0]);
                    $UpperCount = $tempmatch;
                    $tempmatch = preg_match_all("/[a-z]/", $newpwd, $tempmatch);
                    $LowerCount = $tempmatch;

                    $LowerUpperCount = $UpperCount + $LowerCount;

                    if ($UpperCount == 0 && $LowerCount != 0) {
                        $score += 10;
                    } else if ($UpperCount != 0 && $LowerCount != 0) {
                        $score += 20;
                    }

                    $tempmatch = preg_match_all("/[0-9]/", $newpwd, $tempmatch);
                    $NumberCount = $tempmatch;

                    if ($NumberCount == 1) {
                        $score += 10;
                    }
                    if ($numberCount >= 3) {
                        $score += 20;
                    }

                    $tempmatch = preg_match_all('/[!@#$%^&*?_~;:]/', $newpwd, $tempmatch);
                    $characterCount = $tempmatch;
                    //echo':';print_r($characterCount);echo '<br>';
                    // -- 1 character
                    if ($CharacterCount == 1) {
                        $score += 10;
                    }
                    // -- More than 1 character
                    if ($CharacterCount > 1) {
                        $score += 25;
                    }


                    if ($NumberCount != 0 && $LowerUpperCount != 0) {
                        $score += 2;
                    }
                    // -- Letters, numbers, and characters
                    if ($NumberCount != 0 && $LowerUpperCount != 0 && $CharacterCount != 0) {
                        $score += 3;
                    }
                    // -- Mixed case letters, numbers, and characters
                    if ($NumberCount != 0 && $UpperCount != 0 && $LowerCount != 0 && $CharacterCount != 0) {
                        $score += 5;
                    }
                    //echo '<br><br>:'.$score;
                    if ($score < $strength_level) {
                        $cmail->output->command('display_message', $this->gettext('passwordweak'), 'error');
                        return;
                    }
                }



                if (!($res = $this->_save($curpwd, $newpwd))) {
                    echo "<br>";
                    print_r($res);
                    $cmail->output->command('display_message', $this->gettext('successfullysaved'), 'confirmation');
                    $_SESSION['password'] = $cmail->encrypt($newpwd);
                    return;
                } else {
                    $cmail->output->command('display_message', $res, 'error');
                    return;
                }
            }

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        return $args;
    }

    private function _save($curpass, $passwd) {
        $config = cmail::get_instance()->config;
        $driver = $this->home . '/drivers/' . $config->get('password_driver', 'sql') . '.php';

        if (!is_readable($driver)) {
            raise_error(array(
                'code' => 600,
                'type' => 'php',
                'file' => __FILE__,
                'message' => "Password plugin: Unable to open driver file $driver"
                    ), true, false);
            return $this->gettext('internalerror');
        }

        include($driver);

        if (!function_exists('password_save')) {
            raise_error(array(
                'code' => 600,
                'type' => 'php',
                'file' => __FILE__,
                'message' => "Password plugin: Broken driver: $driver"
                    ), true, false);
            return $this->gettext('internalerror');
        }

        $result = password_save($curpass, $passwd);

        switch ($result) {
            case PASSWORD_SUCCESS:
                return;
            case PASSWORD_CRYPT_ERROR:
                return $this->gettext('crypterror');
            case PASSWORD_CONNECT_ERROR:
                return $this->gettext('connecterror');
            case PASSWORD_ERROR:
                return $this->gettext('internalerror');
            case PASSWORD_NOT_DIFFERENT:
                return $this->gettext('passwordnotdifferent');
            default:
                return $this->gettext('internalerror');
        }
    }

}

?>
