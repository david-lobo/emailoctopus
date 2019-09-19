<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/Form/ContactForm.php';
require_once __DIR__ . '/../src/EmailOctopusApi.php';

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
 
autoload();

$apiKey = getenv('API_KEY');
$listId = getenv('LIST_ID');

// GET request to /contacts
route('GET', '^/contacts$', function() use ($apiKey, $listId) 
{
    $response = ['success' => 'true'];
    $api = new EmailOctopusApi($apiKey);
    $list = $api->getListContacts($listId);

    $response = array_merge($response, $list);
    // Send a json response
    header('Content-Type: application/json');
    http_response_code(200);

    echo json_encode($response);
});

// POST request to /contacts
route('POST', '^/contacts$', function() use ($apiKey, $listId) 
{
    $post = $_POST;
    $response = ['success' => 'false'];
    $form = new ContactForm($post);
    $form->validate();

    if ($form->isValid()) {
        $api = new EmailOctopusApi('6d05a3b6-da6b-11e9-be00-06b4694bee2a');
        $inputs = $form->getInputs();
        $payload = $api->postListContact($listId, $inputs);
        $statusCode = $api->getStatusCode();

        if ($statusCode === 200) {
            $response['success'] = true;
            $response = array_merge($response, $payload);
        } else {
            if ($payload['error']['code'] === 'MEMBER_EXISTS_WITH_EMAIL_ADDRESS') {
                $response['error'] = [
                    'code' => 'MEMBER_EXISTS_WITH_EMAIL_ADDRESS',
                    'message' => 'A member already exists with the supplied email address.',
                    'fields' => [
                        'email_address' => 'A member already exists with the supplied email address.'
                    ]
                ];
            } else {
                $response['error'] = [
                    'code' => 'GENERAL_ERROR',
                    'message' => 'The was a problem with that request'
                ];            
            }
        }
    } else {
        $response['success'] = false;
        $response['error'] = [
            'code' => 'INVALID_INPUT_FIELDS',
            'message' => 'The inputs are invalid',
            'fields' => $form->getErrors()
        ];
    }

    header("Access-Control-Allow-Origin: *");

    // Send a json response
    header('Content-Type: application/json');
    http_response_code(200);

    echo json_encode($response);
});

header('HTTP/1.0 404 Not Found');
echo '404 Not Found';