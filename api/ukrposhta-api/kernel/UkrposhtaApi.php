<?php
/**
 * Created by Eugene.
 * User: eugene
 * Date: 19/03/18
 * Time: 09:58
 */

/**
 * Ukrposhta API wrapper class
 *
 * @author mrdonald245
 *
 * @method array addresses(...$arguments)
 * @method array clients(...$arguments)
 * @method array shipments(...$arguments)
 * @method array shipmentGroups(...$arguments)
 * @method string printForm(...$arguments)
 */
class UkrposhtaApi
{
    /**
     * @var string URL url where all of the requests go
     */
    const URL = 'https://www.ukrposhta.ua/';

    /**
     * @var string APP_NAME version of the API,
     * goes just after URL
     */
    const APP_NAME = 'ecom/0.0.1/';

    /**
     * @var array ROUTES where key is a UkrposhtaApi method name
     * and values are its requested method URLs.
     */
    const ROUTES = [
        'addresses' => [
            'post' => 'addresses',
            'get' => 'addresses/{id}'
        ],
        'clients' => [
            'post' => 'clients?token={token}',
            'get' => [
                'getById' => 'clients/{client_uuid}?token={token}',
                'getByPhone' => 'clients/phone?token={token}&countryISO3166=UA&phoneNumber={phoneNumber}',
                'getByExternalId' => 'clients/external-id/{externalId}?token={token}',
                'getAllPhones' => 'client-phones?token={token}&clientUuid={clientUuid}',
                'getAllAddresses' => 'client-addresses?token={token}&clientUuid={clientUuid}',
                'getAllEmails' => 'client-emails?token={token}&clientUuid={clientUuid}',
            ],
            'put' => 'clients/{client_uuid}?token={token}',
            'delete' => [
                'deletePhone' => 'client-phones/{phoneNumberUuid}?token={token}',
                'deleteAddress' => 'client-addresses/{addressUuid}?token={token}',
            ],
        ],
        'shipments' => [
            'post' => 'shipments?token={token}',
            'get' => 'shipments/{shipment_uuid}?token={token}',
            'put' => 'shipments/{shipment_uuid}?token={token}',
            'delete' => 'shipments/{shipment_uuid}?token={token}',
        ],
        'shipmentGroups' => [
            'post' => [
                'create' => 'shipment-groups?token={token}',
                'addShipment' => 'shipment-groups/{shipmentGroupUuid}/shipments/{shipmentUuid}?token={token}',
            ],
            'get' => [
                'get' => 'shipment-groups/{shipment_group_uuid}?token={token}',
                'getByClientUuid' => 'shipment-groups/clients/{clientUuid}?token={token}',
            ],
            'put' => 'shipment-groups/{shipment_uuid}?token={token}',
            'delete' => 'shipments/{shipmentUuid}/shipment-group?token={token}',
        ],
        'printForm' => [
            'get' => [
                'shipmentLabel' => 'shipments/{shipment_uuid}/label?token={token}',
                'shipmentSticker' => 'shipments/{shipment_uuid}/sticker?token={token}',
                'shipmentGroupLabel' => 'shipment-groups/{shipment_group_uuid}/label?token={token}',
                'shipmentGroupSticker' => 'shipment-groups/{shipment_group_uuid}/sticker?token={token}',
                'shipmentGroup103a' => 'shipment-groups/{shipment_group_uuid}/form103a?token={token}',
             ]
        ],
    ];

    /**
     * @var string $token API token
     */
    private $token;

    /**
     * @var string $bearer authorization bearer
     */
    private $bearer;

    /**
     * @var string $method may be either POST, GET, PUT or DELETE
     */
    private $method = 'POST';

    /**
     * @var string $route API request URL
     */
    private $route;

    /**
     * @var array $params params of current method
     */
    private $params;

    /**
     * @var string $action
     */
    private $action;

    /**
     * UkrposhtaApi constructor.
     *
     * @param string $bearer
     * @param string $token
     */
    public function __construct($bearer, $token)
    {
        $this->bearer = $bearer;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getBearer()
    {
        return $this->bearer;
    }

    /**
     * Set method and empties params properties
     *
     * @param string $method by default it is POST
     * @return $this
     */
    public function method($method = 'POST')
    {
        $this->method = $method;
        $this->route = null;
        $this->params = null;
        $this->action = null;

        return $this;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Set route of current method and clear params;
     *
     * @param string $route request url to API
     * @return $this
     */
    public function route($route)
    {
        $this->route = $route;
        $this->params = null;
        $this->action = null;

        return $this;
    }

    /**
     * Set params of current route
     *
     * @param array $params
     * @return $this
     */
    public function params($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Call API methods
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     * @throws BadMethodCallException if there is no such method or request is invalid
     * @throws Exception with curl error message
     *
     * @return array|string
     */
    public function __call($name, $arguments)
    {
        $routes = self::ROUTES;

        // Checks if there is such route
        if (isset($routes[$name])) {
            $route = $routes[$name];
            $method = strtolower($this->method);

            if (isset($route[$method])) {
                $action = $route[$method];
                $url = '';

                if (is_array($action)) {
                    $action_size = sizeof($action);
                    if ($action_size > 1) {
                        if (empty($this->action)) {
                            $actions_in_string = '';

                            foreach ($action as $key => $value) {
                                $actions_in_string .= '"' . $key . '"' . '; ';
                            }

                            throw new UkrposhtaApiException(
                                "You should choose one of $action_size actions: $actions_in_string");
                        } else {
                            if (isset($action[$this->action])) {
                                $url = $action[$this->action];
                            } else {
                                throw new UkrposhtaApiException(
                                    "There is no such action as \"$this->action\"");
                            }
                        }
                    }
                } else if (is_string($action)) {
                    $url = $action;
                }

                $this->route = $this->substituteUrlWithData($url, $arguments);

                $result = $this->execute();

                if (isset($result['code']) && isset($result['message'])) {
                    throw new Exception($result['message']);
                }

                return $result;

            } else {
                throw new BadMethodCallException(
                    "Requested method $this->method is unavailable in Nova Poshta API");
            }
        } else {
            throw new BadMethodCallException("There is no such method as $name");
        }
    }

    /**
     * Execute request to UkrPoshta API
     *
     * @return array|string
     * @throws Exception with curl error message
     */
    public function execute()
    {
        $result = $this->request($this->method, $this->route, $this->params);
        if ($this->isJson($result)) {
            $result = json_decode($result, true);
        }

        return $result;
    }

    /**
     * Make a request to the API.
     *
     * @param string $method
     * @param string $route
     * @param array|null $params required params
     *
     * @return mixed
     * @throws Exception with curl error message
     */
    private function request($method, $route, $params = null)
    {
        $full_url = $this->getFullUrl($route);
        $ch = curl_init($full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer $this->bearer",
        ]);

        if ($params != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] != 200) {
            if (empty($result)) {
                throw new UkrposhtaApiException('Unexpected error has occurred, may be api token is incorrect');
            }

            if ($this->isHTML($result)) {
                $api_error['code'] = 500;
                $api_error['message'] = 'Ukrposhta API returned HTML with error 500. Probably Ukroposhta is down';
            } else if ($this->isJson($result)) {
                $api_error = json_decode($result, true);
            } else {
                $api_error = $this->xmlErr2Array($result);
            }

            $error_message = isset($api_error['message'])
                ? $api_error['message']
                : 'UkrPoshtaApi unexpected error has occurred';

            $error_code = isset($api_error['code'])
                ? $this->extractNumberFromStr($api_error['code'])
                : null;

            throw new UkrposhtaApiException($error_message, $error_code);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * Exchange request API URL with data
     *
     * @example  /addresses/{id} id - will be replaced with address
     * @param string $template_url
     * @param array $params
     * @throws InvalidArgumentException if user passed wrong quantity of arguments
     *
     * @return string url with all substituted params
     */
    private function substituteUrlWithData($template_url, $params)
    {
        $pattern = '/(?!{token}){[\w]+}/'; // {id}, {uu_id} ...
        $params_count = count($params);

        // Checks if params quantity are the same as in template
        $tpl_params_count = preg_match_all($pattern, $template_url);
        if ($tpl_params_count != $params_count) {
            throw new InvalidArgumentException(
                "A method needs $tpl_params_count parameter(s), you passed $params_count parameter(s)");
        }

        // Replace {token} with current token if necessary
        $url = str_replace('{token}', $this->token, $template_url);

        // Replace {blocks} with $params
        if ($params_count) {
            $url = preg_replace_callback($pattern, function () use (&$params) {
                return array_shift($params);
            }, $url);
        }

        return $url;
    }

    /**
     * Concatenates URL with APP_NAME
     *
     * @param string $route_url 'addresses' for example
     * @return string
     */
    private function getFullUrl($route_url) { return self::URL . self::APP_NAME . $route_url; }

    /**
     * @param string $xml
     * @return array $arr
     */
    private function xmlErr2Array($xml)
    {
        $parser = xml_parser_create();
        $values = [];
        $index = [];
        $result = [];

        xml_parse_into_struct($parser, $xml, $values, $index);

        $keys = [];
        foreach ($index as $key => $item) {
            $matches = [];
            preg_match('/\w+:(\w+)/i', $key, $matches);
            $keys[strtolower($matches[1])] = $matches[0];
        }

        foreach ($keys as $i => $key) {
            $index_for_ams = $index[$key][0];
            if (isset($values[$index_for_ams]['value'])) {
                $result[$i] = $values[$index_for_ams]['value'];
            }
        }

        if (isset($result['message'])) {
            isset($result['description'])
                ? $result['message'] = $result['message'] . '. ' . $result['description']
                : null;
        }

        return $result;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isHTML($string)
    {
        return preg_match('/html/i', $string);
    }

    /**
     * @param string $str
     * @return int $number
     */
    private function extractNumberFromStr($str)
    {
        $matches = [];
        preg_match('/\d+/', $str, $matches);

        return (int)$matches[0];
    }
}

class UkrposhtaApiException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}