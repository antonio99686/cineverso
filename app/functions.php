<?php
declare(strict_types=1);
function cfg(string $key): mixed {global $config;return $config[$key]??null;}
function base(string $path=''):string{return rtrim((string)cfg('base_url'),'/').'/'.ltrim($path,'/');}
function e(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function go(string $to=''):never{header('Location: '.base($to));exit;}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verify_csrf():void{if(!hash_equals(csrf(),(string)($_POST['_csrf']??''))){http_response_code(419);exit('Sessão expirada. Atualize a página e tente novamente.');}}
function user():?array{return $_SESSION['user']??null;}
function require_login():array{$u=user();if(!$u)go('index.php?page=login');return $u;}
function require_admin():array{$u=require_login();if(($u['role']??'')!=='admin'){http_response_code(403);exit('Acesso restrito.');}return $u;}
function flash(string $message,string $type='ok'):void{$_SESSION['flash']=['message'=>$message,'type'=>$type];}
function flash_show():void{if(!empty($_SESSION['flash'])){$f=$_SESSION['flash'];unset($_SESSION['flash']);echo '<div class="flash '.e($f['type']).'">'.e($f['message']).'</div>';}}
function db():PDO{global $db;return $db;}
function query(string $sql,array $params=[]):PDOStatement{$s=db()->prepare($sql);$s->execute($params);return $s;}
function one(string $sql,array $params=[]):?array{return query($sql,$params)->fetch()?:null;}
function all(string $sql,array $params=[]):array{return query($sql,$params)->fetchAll();}
function money(float|int|string $v):string{return 'R$ '.number_format((float)$v,2,',','.');}
function poster(?string $path):string{if(!$path)return base('assets/placeholder.svg');if(preg_match('~^https?://~i',$path))return $path;return base(ltrim($path,'/'));}
function upload_image(string $field,?string $current=null):?string{
 if(empty($_FILES[$field])||($_FILES[$field]['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return $current;
 $f=$_FILES[$field];if($f['error']!==UPLOAD_ERR_OK)throw new RuntimeException('Erro no envio de imagem.');
 if($f['size']>5*1024*1024)throw new RuntimeException('Imagem maior que 5 MB.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
 if(!isset($types[$mime]))throw new RuntimeException('Use JPG, PNG, WEBP ou GIF.');
 $name=bin2hex(random_bytes(16)).'.'.$types[$mime];$dir=dirname(__DIR__).'/public/uploads/posters';if(!is_dir($dir))mkdir($dir,0755,true);
 if(!move_uploaded_file($f['tmp_name'],$dir.'/'.$name))throw new RuntimeException('Falha ao guardar imagem.');return 'uploads/posters/'.$name;
}
function render_header(string $title=''):void{$u=user();$app=e((string)cfg('app_name'));$page=e($title);$home=base('index.php');$admin=base('index.php?page=admin');$login=base('index.php?page=login');$logout=base('index.php?page=logout');$mine=base('index.php?page=my-tickets');$style=base('assets/style.css');echo <<<HTML
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0d0d15"><title>{$page} | {$app}</title><link rel="stylesheet" href="{$style}"></head><body><header class="topbar"><a class="brand" href="{$home}"><span class="brand-icon">✦</span> CINE<span>VERSO</span></a><nav><a href="{$home}">Explorar</a><a href="{$home}&amp;mode=streaming">Streaming</a><a href="{$home}&amp;mode=cinema">Cinema</a><a href="{$mine}">Ingressos</a></nav><div class="account">
HTML;
 if($u){echo '<span class="hello">Olá, '.e($u['name']).'</span>';if($u['role']==='admin')echo '<a class="btn outline small" href="'.$admin.'">Admin</a>';echo '<a class="btn small" href="'.$logout.'">Sair</a>';}
 else echo '<a class="btn small" href="'.$login.'">Entrar</a>';
 echo '</div></header><main class="container">';flash_show();}
function render_footer():void{echo '</main><footer>© '.date('Y').' CINEVERSO · Cinema & streaming · Demonstração sem cobrança real</footer></body></html>';}
