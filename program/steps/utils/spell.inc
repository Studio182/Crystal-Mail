<?php
/*
+----------------------------------------------------------------+
| ./program/steps/utils/spell.inc                                |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2010, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Invoke the configured or default spell checking engine       |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Kris Steinhoff <steinhof@umich.edu>                            |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


// max. number of suggestions for one word
define('MAX_SUGGESTIONS', 10);

$tiny = !empty($_GET['tiny']) ? 'html_' : '';

if ($spell_engine = $cmail->config->get('spellcheck_engine', 'googie')) {
    include('spell_'.$tiny.$spell_engine.'.inc');
}

header('HTTP/1.1 404 Not Found');
exit;

