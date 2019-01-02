<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$secret_code = $modx->getOption('nbcurrencyx_secret_code', null, '');

if ( $_GET['sc'] != $secret_code ) {
    echo 'permission denied';
    exit();
}

/* curl обертка */
function _NB_getRequest($url, $refer = "", $timeout = 10)
{
    $ssl = stripos($url,'https://') === 0 ? true : false;
    $curlObj = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_HTTPHEADER => ['Expect:'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ];
    if ($refer) {
        $options[CURLOPT_REFERER] = $refer;
    }
    if ($ssl) {
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    }
    curl_setopt_array($curlObj, $options);
    $returnData = curl_exec($curlObj);
    if (curl_errno($curlObj)) {
        $returnData = curl_error($curlObj);
    }
    curl_close($curlObj);
    return $returnData;
}

$default_codes = array('USD','EUR','RUB');	// если не заданы коды валют - по-умолчанию USD,EUR,RUB
$custom_codes = array();					// пользовательские коды валют, переданные в $_GET
$arr_json = array();						// результат
$nbrb_url = $modx->getOption('nbcurrencyx_api_url', null,
	'https://www.nbrb.by/API/ExRates/Rates/');
$path_to_file = $modx->getOption('nbcurrencyx_assets_path', null,
	$modx->getOption('assets_path') . 'components/nbcurrencyx/').'currencies.json';

if (!empty($_GET['val_codes']))
{
    $val_codes = explode(',',$_GET['val_codes']);

    foreach ($val_codes as $code) {
        $code = trim($code);
        if ( $code ) {
            $custom_codes[] = $code;
        }
    }
}

if (count($custom_codes) > 0)
    $list_currencies = $custom_codes;
else
    $list_currencies = $default_codes;

foreach($list_currencies as $currencie)
{
	try {
		$result = _NB_getRequest($nbrb_url.$currencie.'/?ParamMode=2');
		$cur_v = json_decode($result, true);
		if ( !is_array($cur_v) ) {
			$modx->log('nbcurrencyx api error: '.$cur_v);
		} else {
			$arr_json[$currencie]['Cur_OfficialRate'] = $cur_v['Cur_OfficialRate'] / $cur_v['Cur_Scale'];
		}
	}
	catch (Exception $e) {
		$modx->log('nbcurrencyx api error: '.$e->getMessage());
	}
}

if (!empty($arr_json) && count($arr_json) > 0 ) {
	file_put_contents($path_to_file, json_encode($arr_json));
}
else {
	$modx->log('nbcurrencyx error: empty result json');
}