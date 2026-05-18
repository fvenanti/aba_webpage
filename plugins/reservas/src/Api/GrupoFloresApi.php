<?php

namespace Upbrands\Obituarios\Api;

use GuzzleHttp\Client;

class GrupoFloresApi
{
  const BASE_URI = 'https://api.asispre.com';

  /** @var Client */
  protected $http;

  /** @var string */
  protected $api_key;

  public function __construct(?Client $http = null)
  {
    $this->api_key = $_ENV['API_KEY'];

    $this->http = $http ?: new Client([
      'base_uri' => self::BASE_URI,
      'timeout' => 10,
    ]);
  }

  /**
   * GET /getServiciosActuales
   *
   * @return array lista de servicios (arrays asociativos)
   */
  public function getServiciosActuales(): array
  {
    $response = $this->http->request('GET', '/getServiciosActuales', [
      'headers' => [
        'Authorization' => $this->api_key,
        'Accept' => 'application/json',
      ],
    ]);

    $body = (string) $response->getBody();
    $data = json_decode($body, true);

    return is_array($data) ? $data : [];
  }
}
