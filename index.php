<?php
require('vendor/autoload.php');
use RingCentral\SDK\SDK;

$credentials = require (__DIR__ . '/credentials.php');
$rcsdk = new SDK($credentials['clientId'], $credentials['clientSecret'], $credentials['server'], 'Demo', '1.0.0');
$platform = $rcsdk->platform();
$platform->login(["jwt" => $credentials['jwt']]);

$phoneNumbers = $platform->get('/account/~/extension/~/phone-number', array('perPage' => 'max'))->json()->records;
$smsNumber = null;
foreach ($phoneNumbers as $phoneNumber) {
    if (in_array('SmsSender', $phoneNumber->features)) {
        $smsNumber = $phoneNumber->phoneNumber;
        break;
    }
}
print 'SMS Phone Number: ' . $smsNumber . PHP_EOL;

if ($smsNumber) {
    $response = $platform
        ->post('/account/~/extension/~/sms', array(
              'from' => array('phoneNumber' => $smsNumber),
              'to' => array(
                  array('phoneNumber' => $credentials['smsRecipient']),
              ),
              'text' => 'Test from PHP',
          )
        );
    print 'Sent SMS ' . $response->json()->uri . PHP_EOL;
} else {
    print 'SMS cannot be sent: no SMS-enabled phone number found...' . PHP_EOL;
}
