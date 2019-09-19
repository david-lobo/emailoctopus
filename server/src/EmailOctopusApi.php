<?php
declare(strict_types=1);

class EmailOctopusApi {

    protected $apiKey;
    protected $isCurlLog = false;
    protected $ssl = false;
    protected $statusCode;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function curl(string $url, array $parameters, string $contenType, bool $post) : string
    {
        if ($this->isCurlLog) {
            $fp = fopen(APPLICATION_PATH . '/logs/curl.log', 'a');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($this->isCurlLog) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }
        
        if ($post === true) {
            curl_setopt($ch, CURLOPT_POST, true);
            $str = http_build_query($parameters);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURL_HTTP_VERSION_1_1, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        
        $headers = array();
        if (!empty($contentType)) {
            $headers[] = "Content-Type: {$contentType}";
        }

        $headers = array();
        if (!empty($this->accessToken)) {
            $headers = array(
                'authorization: Bearer ' . $this->accessToken,
                'Connection: Keep-Alive',
                'X-RestLi-Protocol-Version: 2.0.0'
            );
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result;
    }

    public function getLists() : array
    {
        $url = "https://emailoctopus.com/api/1.5/lists?api_key={$this->apiKey}";
        $response = $this->curl($url, array(), '', false);
        $lists = json_decode($response, true);
        return $lists;
    }

    public function getList(string $listId) : array
    {
        $url = "https://emailoctopus.com/api/1.5/lists/{$listId}?api_key={$this->apiKey}";
        $response = $this->curl($url, array(), '', false);
        $lists = json_decode($response, true);
        return $lists;
    }

    public function getListContacts(string $listId) : array
    {
        $url = "https://emailoctopus.com/api/1.5/lists/{$listId}/contacts?api_key={$this->apiKey}";
        $response = $this->curl($url, array(), '', false);
        $lists = json_decode($response, true);
        return $lists;
    }

    public function postListContact(string $listId, array $contact) : array
    {   
        $firstNameField = 'FirstName';
        $lastNameField = 'LastName';
        if (isset($contact[$firstNameField])) {
            $contact['fields'][$firstNameField] = $contact[$firstNameField];
            unset($contact[$firstNameField]);
        }
        if (isset($contact[$lastNameField])) {
            $contact['fields'][$lastNameField] = $contact[$lastNameField];
            unset($contact[$lastNameField]);
        }

        $url = "https://emailoctopus.com/api/1.5/lists/{$listId}/contacts?api_key={$this->apiKey}";
        $response = $this->curl($url, $contact, '', true);
        $lists = json_decode($response, true);
        return $lists;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }
}