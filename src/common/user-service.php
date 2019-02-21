<?php

session_start();

function is_authorised()
{
    return !empty($_SESSION['username']) && !empty($_SESSION['password']);
}

// todo dont't need it with ajax
function is_authorized_or_redirect()
{
    $authorized = is_authorised();
    if (!$authorized) {
        header('Location: /login.php');
    }
    return $authorized;
}

function authorize($username, $password)
{
// todo return state of authorization
// todo: retrieve and check info from db

    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
}

function logout()
{
    unset($_SESSION['username']);
    unset($_SESSION['password']);
}

function register()
{

}

?>