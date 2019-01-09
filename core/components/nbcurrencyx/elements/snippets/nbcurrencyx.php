<?php
$path = MODX_CORE_PATH . 'components/nbcurrencyx/model/';
$modx->loadClass('nbcurrencyx', $path, true, true);
$n = new nbCurrencyX($modx);

return $n->convert($value, $scriptProperties);