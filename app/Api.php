<?php

namespace App;

use GuzzleHttp\Client;

/**
 * Class Api
 * @package App
 */
class Api
{
    /**
     * Request data from the API
     *
     * @param string $url
     * @param string $queryString
     * @return array
     *
     */
    public function request($url, $queryString = '')
    {
        $client = new Client([
            'base_uri' => config('app.datastore_url')
        ]);

        if (!empty($queryString)) {
            $queryString = '?' . $queryString;
        }

        try {
            $response = $client->request('GET', $url . $queryString);
            $status = $response->getStatusCode();
            if ($status == 200) {
                $data = json_decode($response->getBody(), true);
                if (!is_null($data)) {
                    return [
                        'success' => true,
                        'data' => $data['data'],
                        'pages' => isset($data['pages']) ? $data['pages'] : [],
                        'aggregations' => isset($data['aggregations']) ? $data['aggregations'] : []
                    ];
                }
                return [
                    //@todo Implement more descriptive/friendly error messages
                    'success' => false,
                    'message' => isset($data['message']) ? $data['message'] : 'An error occurred',
                    'error' => true,
                    'pages' => [],
                    'data' => [],
                    'aggregations' => []
                ];
            }
        } catch (\Exception $e) {
            //@todo Implement more descriptive/friendly error messages
            return [
                'success' => false,
                'message' => 'An error occurred.',
                'error' => true,
                'data' => [],
                'pages' => [],
                'aggregations' => []
            ];
        }
    }
}
