<?php

define('BOT_LOGS_DIRECTORY', 'logs');

define('CALLBACK_API_CONFIRMATION_TOKEN', 'd68a0850'); //Строка для подтверждения адреса сервера из настроек Callback API
define('VK_API_ACCESS_TOKEN', 'db04f9c2ba340a89339514eea64ab9dd1cecc6c39bb203007c0604d53c02a28925abb1c1b574410fa3e9b'); //Ключ доступа сообщества
define('GROUP_ID', '185136788'); // ID группы

define('COMMAND_LIST', "📝 Список команд: <br> пидор 'имя' <br> пидорас 'имя'");
define('COMMAND_ACTIVE', false); // Включить или выключить / и !

define('MSG_HELP', "❗ Такой команды нет. <br> ".COMMAND_LIST); //Сообщение если команда введена не правильно или не введена вообще
define('MSG_WELCOME', "Привет, добро пожаловать!<br>🌏 Сайт: pidor.men <br>".COMMAND_LIST);
define('MSG_PIDOR', "Опа");