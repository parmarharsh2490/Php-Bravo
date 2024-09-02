<?php
require_once __DIR__ . '/vendor/autoload.php';

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$input = json_decode(file_get_contents('php://input'), true);

$toName = isset($input['firstName']) ? $input['firstName'] : 'Recipient';
$fromName = isset($input['firstName']) ? $input['firstName'] : 'Unknown';
$fromEmail = isset($input['email']) ? $input['email'] : 'unknown@example.com';
$fromPhone = isset($input['phone']) ? $input['phone'] : 'Not provided';
$message = isset($input['message']) ? $input['message'] : 'No message provided';

$config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);

$apiInstance = new TransactionalEmailsApi(
    new GuzzleHttp\Client(),
    $config
);

$emailTemplate = "
    <p>Hello $toName,</p>
    <p>You have received a new message from <span style=\"font-weight: bold;\">$fromName</span>.</p>
    <p style=\"margin: 16px 0;\">
      <span style=\"font-weight: bold;\">Details:</span><br>
      <span style=\"font-weight: bold; color: #333;\">Name:</span> $fromName<br>
      <span style=\"font-weight: bold; color: #333;\">Email:</span> $fromEmail<br>
      <span style=\"font-weight: bold; color: #333;\">Phone Number:</span> $fromPhone
    </p>
    <p style=\"margin: 16px 0;\">
      <span style=\"font-weight: bold;\">Message:</span>
    </p>
    <p style=\"padding: 12px; border-left: 4px solid #d0d0d0; font-style: italic; margin: 0;\">
      $message
    </p>
";

$emailData = new SendSmtpEmail([
    'to' => [
        [
            'email' => '2101031000125@silveroakuni.ac.in',
            'name' => $toName
        ]
    ],
    'sender' => [
        'email' => '2101031000125@silveroakuni.ac.in',
        'name' => 'Dummy Form'
    ],
    'subject' => 'New Message from Dummy Form',
    'htmlContent' => $emailTemplate
]);

try {
    $result = $apiInstance->sendTransacEmail($emailData);
    echo json_encode(['message' => 'Email sent successfully!', 'data' => $result]);
} catch (Exception $e) {
    echo json_encode(['message' => 'Exception when calling TransactionalEmailsApi->sendTransacEmail', 'error' => $e->getMessage()]);
}
?>
