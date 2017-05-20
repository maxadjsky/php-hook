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
$fs      = new Filesystem();
$target  = getenv('TARGET');
$user    = getenv('USER');
$group   = getenv("GROUP");
$json    = json_decode(file_get_contents('php://input'), true);
if (empty($json['token']) || $json['token'] !== getenv('TOKEN')) {
    return (new Response("token error"))->send();
}
if ($fs->exists($target)) {
    chdir(getenv('TARGET'));
    $pull = new Process('git pull');
    $pull->run();
    $chown = new Process("chown -R {$user}:{$group} $target");
    $chown->run();
    $process = new Process('composer install');
    $process->start();

    $response = new Response("Hello");
    $response->send();
    $process->wait();
} else {
    echo "Directory is not found or permission denied";
}

