<?php

require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use framework\CommandProcessorFactory;

//$test = new dal\managers\ConquestRepository();
//$test->GetCurrentConquest();
//
//$userRepository = new \dal\managers\UserRepository();
//$user = $userRepository->GetUserByName('christopher');
//$test->SetCommander($user);

$app = new Silex\Application();
$app['debug'] = true;

$app->post('', function(Request $request){
    $data = json_decode($request->getContent(), true);
    error_log(print_r($data, true));
    $commandProcessor = new CommandProcessorFactory();
    $commandProcessor->CreateProcessor($request);
    return new Response('', 200);
});

$app->post('/slack/verify', function(Request $request){
    $data = json_decode($request->getContent(), true);
    if ($data['type'] != 'url_verification')
    {
        return new Response('Wrong type of request', 400);
    }
    if ($data['token'] != Config::$JarvisToken)
    {
        return new Response('Invalid token', 400);
    }    
    return new Response($data['challenge'], 200);
});

$app->run();