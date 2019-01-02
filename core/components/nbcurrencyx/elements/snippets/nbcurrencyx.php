<?php
$path_to_file = $modx->getOption('nbcurrencyx.assets_path', null,
	$modx->getOption('assets_path') . 'components/nbcurrencyx/').'currencies.json';
$cur_val_usd = file_get_contents($path_to_file);
$to = empty($dir)?'byn-to-curr':$dir;
$precision = empty($deg)?0:$deg;

if ($api_res = json_decode($cur_val_usd,true))
{
    $price = $api_res[$curr]['Cur_OfficialRate'];
}

$value = str_replace(array(' ',','),array('','.'),$value); //режем пробельчики, запятые меняем на точки

if (empty($price) || empty($value)) {
	return '';
}

if (!empty($price)) {
    switch ($to) {
        case 'byn-to-curr':
            $value = $value / $price;
            break;
        case 'curr-to-byn':
            $value = $value * $price;
    }
}

if (!empty($number_format))
	return number_format($value, floor($value) == $value ? 0 : $precision, '.', ' ');
else
	return round($value / $price, $precision);