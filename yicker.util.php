<?php
function http($url, $method, $postfields = NULL) {
    //echo $url;
    //$this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
    //curl_setopt($ci, CURLOPT_USERAGENT,);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ci, CURLOPT_TIMEOUT, 120);
    //curl_setopt ($ci, CURLOPT_PROXY, "http://157.54.27.21:80");
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    //curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    //curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
            }
            break;
        case 'DELETE':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($postfields)) {
                $url = "{$url}?{$postfields}";
            }
			break;
        default:
            break;
    }
    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    curl_close ($ci);

    return $response;
  }
?>