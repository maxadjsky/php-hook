<?php
/**
 * Created by PhpStorm.
 * @author: mofeng
 * @since : 2017/5/20 12:54
 */

require_once __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Dotenv\Dotenv;

$env = new Dotenv(__DIR__);
$env->load();
$request = Request::createFromGlobals();

$fs     = new Filesystem();
$target = getenv('TARGET');
$user   = getenv('USER');
$group  = getenv("GROUP");
$json   = json_decode(strval($request->getContent(false)));
if (empty($json->token) || $json->token !== getenv('TOKEN')) {
    return (new Response("token error"))->send();
}
if ($fs->exists($target)) {
    chdir(getenv('TARGET'));
    $pull = new Process('git pull');
    $pull->run();

    $process = new Process('php /usr/local/bin/composer install');
    $process->start();
    $fs->chown($target, $user);
    $fs->chgrp($target, $group);
    $response = new Response("Hello");
    $response->send();
    $process->wait();
} else {
    return response("Directory is not found or permission denied");
}



