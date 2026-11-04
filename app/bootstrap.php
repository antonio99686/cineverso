<?php
declare(strict_types=1);
$configPath=dirname(__DIR__).'/config.php';
if(!is_file($configPath)){http_response_code(503);exit('Copie config.example.php para config.php e ajuste o banco.');}
$config=require $configPath;
if(session_status()!==PHP_SESSION_ACTIVE){session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','use_strict_mode'=>true]);}
header('X-Content-Type-Options: nosniff');header('Referrer-Policy: strict-origin-when-cross-origin');
require_once __DIR__.'/functions.php';
try {$db=new PDO('mysql:host='.$config['db']['host'].';port='.$config['db']['port'].';dbname='.$config['db']['name'].';charset=utf8mb4',$config['db']['user'],$config['db']['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}
catch(PDOException $e){http_response_code(503);exit('Banco de dados indisponível. Verifique config.php e a importação do SQL.');}
