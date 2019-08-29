<?php
/**
 * This file is an adapted verison of
 * https://github.com/agentejo/cockpit/blob/next/modules/Cockpit/cli/account/create.php
 * 
 * It allows user input if parameters are missing and it doesn't allow a password parameter
 * to prevent keeping the password in the bash history
 */

if (!COCKPIT_CLI) return;

if(!defined("STDIN")) define("STDIN", fopen('php://stdin','rb'));

$user = $app->param('user', null);

if (!$user) {
    echo "Type a user name and press Enter:\n";
    $user = fread(STDIN, 80); // allow 80 characters input
    $user = trim($user);
}

$email = $app->param('email', null);

if (!$email) {
    echo "Type a valid email address and press Enter:\n";
    $email = fread(STDIN, 80);
    $email = trim($email);
}

if (!$app->helper('utils')->isEmail($email)) {
    var_dump($app->helper('utils')->isEmail($email));
    return CLI::writeln('Valid email required', false);
}

echo "Type a password and press Enter:\n";
$password = fread(STDIN, 80);

$password = $app->hash(trim($password));


$created = time();

$account = [
    'user'     => $user,
    'name'     => $app->param('name', $user),
    'email'    => $email,
    'password' => $password,
    'active'   => true,
    'group'    => $app->param('group', 'admin'),
    'i18n'     => $app->param('i18n', 'en'),
    '_created' => $created,
    '_modified'=> $created,
];

// unique check
// --

$exist = $app->storage->findOne('cockpit/accounts', ['user'  => $user]);

    if ($exist) {
        return CLI::writeln('Username is already used!', false);
    }

$exist = $app->storage->findOne('cockpit/accounts', ['email'  => $email]);

    if ($exist) {
        return CLI::writeln('Email is already used!', false);
    }

// --

$app->storage->insert('cockpit/accounts', $account);

CLI::writeln('Account created', true);
