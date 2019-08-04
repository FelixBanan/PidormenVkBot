<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/vk_api.php';

function bot_processMessage($data) {

	$user_id = $data['from_id'];
	$peer_id = $data['peer_id'];
	$message = $data['text'];


	if($peer_id > 2000000000){
		conversation_command($data);
	} else {
		chat_command($data);
	}

}

function conversation_command($data)
{

	if(!in_array(
    	substr($data['text'], 0, 1),
		 array('[')
	)){
		if(COMMAND_ACTIVE == true){
			if (!in_array(
				substr($data['text'], 0, 1),
				array('/', '!')
			)){ return true; } else { $num = 0; }
		} else { return true; }
	} else { $num = 1; }

	$data['text'] = preg_replace('| +|', ' ', $data['text']);
	$data['text'] = trim(substr($data['text'], 1));

	$message = $data['text'];

	$user = [
	   'id' => $data['from_id'],
	   'peer_id' => $data['peer_id'],
	   'command' => explode(
		  " ",
		  mb_strtolower($message)
	   ),
	 'original_command' => explode(" ", $message),
	 'group_id' => $data['group_id'],
	 'num' => $num + 1
	];

	switch($user['command'][$num]){

		case 'пидор':
			pidor($user, "https://pidor.men/lic.php?name=");
		break;

		case 'пидорас':
			pidor($user, "https://pidor.men/img.php?name=");
		break;

		default:
			send($user, $user['command'][1]."<br>".MSG_HELP);

	}

}

function chat_command($data)
{

   	$message = $data['text'];

   	$user = [
      	'id' => $data['from_id'],
      	'peer_id' => $data['peer_id'],
      	'command' => explode(
         	" ",
         	mb_strtolower($message)
		  ),
		'original_command' => explode(" ", $message),
		'group_id' => $data['group_id'],
		'num' => 1
	   ];

	switch($user['command'][0]){

		case 'пидор':
			pidor($user, "https://pidor.men/lic.php?name=");
	 	break;

		case 'пидорас':
			pidor($user, "https://pidor.men/img.php?name=");
	 	break;

		case 'начать':
		case 'start':
			send($user, MSG_WELCOME);
		break;

		default:
			send($user, MSG_HELP);

	}
	   
}

function commands($user){

	switch($user['command'][0]){

		case 'пидор':
			pidor($user, "https://pidor.men/lic.php?name=");
	 	break;

		case 'пидорас':
			pidor($user, "https://pidor.men/img.php?name=");
	 	break;

		case 'начать':
		case 'start':
			send($user, MSG_WELCOME);
		break;

		default:
			send($user, MSG_HELP);

	}

}

function send($user, $msg)
{
	vkApi_messagesSend($user['peer_id'], $msg);
}

function pidor($user, $link)
{
   $str = getLongCommand($user['original_command'], $user['num']);
   $name = str_replace(' ', '%20', $str);
   $file = file_get_contents("$link$name");
   file_put_contents($_SERVER['DOCUMENT_ROOT']."/temp/$name.jpeg", $file);
   $photo = _vkApi_uploadPhoto($user['group_id'], $_SERVER['DOCUMENT_ROOT']."/temp/$name.jpeg");
   vkApi_messagesSend($user['peer_id'], MSG_PIDOR, array('photo'.$photo['owner_id'].'_'.$photo['id']));
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