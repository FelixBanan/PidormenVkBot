<?php
//11
define('VK_API_VERSION', '5.80'); //Используемая версия API
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');
require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';
function vkApi_messagesSend($peer_id, $message, $attachments = array())
{
  return _vkApi_call('messages.send', array(
    'peer_id'    => $peer_id,
    'message'    => $message,
    'attachment' => implode(',', $attachments)
  ));
}

function vkApi_messagesSendKeyboard($peer_id, $message, $keyboard)
{
  return _vkApi_call('messages.send', array(
    'peer_id'    => $peer_id,
    'message'    => $message,
    'keyboard' => $keyboard,
  ));
}

function vkApi_usersGetP($user_id)
{
  return _vkApi_call('users.get', array(
    'user_id' => $user_id,
    'fields' => 'sex'
  ));
}

function vkApi_photosGetMessagesUploadServer($peer_id)
{
  return _vkApi_call('photos.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
  ));
}

function vkApi_photosSaveMessagesPhoto($photo, $server, $hash)
{
  return _vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}

function vkApi_docsGetMessagesUploadServer($peer_id, $type)
{
  return _vkApi_call('docs.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
    'type'    => $type,
  ));
}

function vkApi_docsSave($file, $title)
{
  return _vkApi_call('docs.save', array(
    'file'  => $file,
    'title' => $title,
  ));
}


function vkApi_getOwnerPhotoUploadServer($owner_id)
{
  return _vkApi_call('photos.getOwnerPhotoUploadServer', array(
    'owner_id' => $owner_id
  ));
}

function vkApi_messagesRemoveChatUser($chat_id, $user_id)
{
  return _vkApi_call('messages.removeChatUser', array(
    'chat_id'  => $chat_id,
    'user_id' => $user_id,
  ));
}

function vkApi_utilsResolveScreenName($screen_name)
{
  return _vkApi_call('utils.resolveScreenName', array(
    'screen_name' => $screen_name,
  ));
}

function vkApi_messagesGetInviteLink($peer_id, $reset)
{
  return _vkApi_call('messages.getInviteLink', array(
    'peer_id' => $peer_id,
    'reset' => $reset,
  ));
}

function vkApi_usersGet($id, $fields = array())
{
  return _vkApi_call('users.get', array(
    'user_ids' => $id,
    'fields' => implode(',', $fields)
  ));
}

function vkApi_messagesGetConversationMembers($peer_id, $fields = array())
{
  return _vkApi_call('messages.getConversationMembers', array(
    'peer_id' => $peer_id,
    'fields' => implode(',', $fields)
  ));
}

function vkApi_messagesGetConversationsById($peer_id, $fields = array())
{
  return _vkApi_call('messages.getConversationsById', array(
    'peer_ids' => $peer_id,
    'fields' => implode(',', $fields)
  ));
}

function _vkApi_call($method, $params = array())
{
  $params['access_token'] = VK_API_ACCESS_TOKEN;
  $params['v'] = VK_API_VERSION;

  $query = http_build_query($params);
  $url = VK_API_ENDPOINT . $method . '?' . $query;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$method} request");
  }

  curl_close($curl);

  $response = json_decode($json, true);
  if (!$response || !isset($response['response'])) {
    log_error($json);
    throw new Exception("Invalid response for {$method} request");
  }

  return $response['response'];
}

function vkApi_upload($url, $file_name) {
  if (!file_exists($file_name)) {
    throw new Exception('File not found: '.$file_name);
  }
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$url} request");
  }
  curl_close($curl);
  $response = json_decode($json, true);
  if (!$response) {
    throw new Exception("Invalid response for {$url} request");
  }
  return $response;
}

function _vkApi_uploadPhoto($user_id, $file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);
  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];
  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);
  return $photo;
}