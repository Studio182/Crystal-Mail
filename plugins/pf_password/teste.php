<?php

$passwd="senha1";
$basedir = getcwd();


//$dovecotpw = "/var/www/webmail-new/plugins/password/drivers/dovecotpw";// caminho completo até o executável
//$dovecotpw = $basedir . "/plugins/pf_password/drivers/dovecotpw";
//$dovecotpw="/var/www/webmail2/plugins/pf_password/drivers/dovecotpw";
$dovecotpw=$basedir."/drivers/dovecotpw";
// uso de temporário previne que a senha, em texto plano, apareça na tabela de processos
echo "<hr><br>basedir<br>".print_r($basedir)."<br><hr>";
$method = "CRAM-MD5";
$prefix = "postfixadmin-";
$tmpfile = tempnam('/tmp', $prefix); // cria temporário na pasta tmp, com um prefixo postfixadmin, e um nome aleatório
$pipe = popen("'$dovecotpw' -s '$method' > '$tmpfile'", 'w'); // chamada do programa encriptador


if (!$pipe) {
    unlink($tmpfile);
    echo "error with program calling";
    return 0; // error with program calling
} else {
    // use dovecot's stdin, it uses getpass() twice
    fwrite($pipe, $passwd . "\n", 1 + strlen($passwd));
    usleep(1000);
    fwrite($pipe, $passwd . "\n", 1 + strlen($passwd));
    pclose($pipe);

    $hash_passwd = file_get_contents($tmpfile); // gets the temporary content
    unlink($tmpfile);

    if (!preg_match('/^\{' . $method . '\}/', $hash_passwd)) {
        echo "problems with dovecotpw execution";
        return 0; // problems with dovecotpw execution
    }

    $hash_passwd = trim(str_replace('{' . $method . '}', '', $hash_passwd));



    var_dump($hash_passwd);

}


?>