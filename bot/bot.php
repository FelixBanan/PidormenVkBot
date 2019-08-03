<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/vk_api.php';

function bot_processMessage($data) {

	$user_id = $data['from_id'];
	$peer_id = $data['peer_id'];
	$message = $data['text'];

	chat_command($data);

}

function chat_command($data)
{
	if(!in_array(
    	substr($data['text'], 0, 1),
		 array('[')
	)){

		if (!in_array(
			substr($data['text'], 0, 1),
			array('/', '!')
		)){ return true; } else { $num = 0; }
	} else { $num = 1; }

   	$data['text'] = preg_replace('| +|', ' ', $data['text']);
   	$data['text'] = trim(substr($data['text'], 1));
   	$message = $data['text'];

   	if (strlen($message) < 2)
      	return;

   	$user = [
      	'id' => $data['from_id'],
      	'peer_id' => $data['peer_id'],
      	'message' => $data['text'],
      	'command' => explode(
         	" ",
         	mb_strtolower($message)
	  	),
	  	'group_id' => $data['group_id'],
      	'original_command' => explode(" ", $message)
   	];

   	unset($message);

   	switchCommands($user, $num);

}

function switchCommands($user, $num){
	$user['num'] = $num;
	switch ($user['command'][$num]) {

		case 'пидор':
			pidor($user, "https://pidor.men/lic.php?name=");
		break;

		case 'пидорас':
			pidor($user, "https://pidor.men/img.php?name=");
		break;

		default:
			$command = $user['command'][0];
			send($user, "$command<br>"."❓ Такой команды нет. <br> 📒 Список команд: <br> !пидор 'имя' <br> !пидорас 'имя'");
   }
}

function send($user, $msg)
{
	vkApi_messagesSend($user['peer_id'], $msg);
}

function pidor($user, $link)
{

	$str = getLongCommand($user['command'], $user['num']+1);
	$name = str_replace(' ', '%20', $str);
	$file = file_get_contents("$link$name");
   	file_put_contents($_SERVER['DOCUMENT_ROOT']."/temp/$name.jpeg", $file);
   	$photo = _vkApi_uploadPhoto($user['group_id'], $_SERVER['DOCUMENT_ROOT']."/temp/$name.jpeg");
   	vkApi_messagesSend($user['peer_id'], 'Опа', array('photo'.$photo['owner_id'].'_'.$photo['id']));
   	unlink($_SERVER['DOCUMENT_ROOT']."/temp/$name.jpeg");
}

function getLongCommand($commands, $start)
{
   $command = "";
   for ($x = $start; $x <= count($commands); $x++) {
      $command .= $commands[$x] . " ";
   }
   return trim($command);
}