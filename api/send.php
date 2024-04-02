<?php

use App\Bitrix24\Bitrix24API;
use App\Bitrix24\Bitrix24APIException;
use Symfony\Component\Dotenv\Dotenv;

include_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['comment'])) {
  exit('INVALID REQUEST');
}

try {
  
  $dotenv = new Dotenv();
  $dotenv->load('../.env');

  $webhookURL = $_ENV['BITRIX24_WEBHOOK_URL'];
  $bx24 = new Bitrix24API($webhookURL);

// Добавляем новый контакт
  $contactId = $bx24->addContact([
    'NAME' => $_POST['name'],
    'PHONE' => [['VALUE' => $_POST['phone'], 'VALUE_TYPE' => 'WORK']]
  ]);

  // Добавляем новую сделку
  $dealId = $bx24->addDeal([
    'TITLE' => 'Заявка с сайта ' . date("Y-m-d H:i:s"),
    'COMMENTS' => $_POST['comment'],
    'CONTACT_ID' => $contactId,    
    'SOURCE_ID' => 'WEB',
    'UTM_SOURCE' => 'САЙТ'        
  ]);

  echo "<pre>" . "Форма обратной связи отправлена". "</pre>";
  echo "<pre>" . "Сделка успешно добавлена. ID Сделки: " . $dealId . "</pre>";
  echo "<pre>" . "Контакт успешно добавлен. ID Контакта: " . $contactId . "</pre>";

} catch (Bitrix24APIException $e) {
  printf('Ошибка (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
} catch (Exception $e) {
  printf('Ошибка (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}