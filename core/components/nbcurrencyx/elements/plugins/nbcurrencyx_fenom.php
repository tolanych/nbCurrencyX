<?php
/** @var modX $modx */
switch ($modx->event->name) {
    case 'pdoToolsOnFenomInit':
		$fenom->addModifier('nbcurr', function ($input, $options = []) use ($modx) {
			$path = MODX_CORE_PATH . 'components/nbcurrencyx/model/';
			$modx->loadClass('nbcurrencyx', $path, true, true);
			$n = new nbCurrencyX($modx);
			switch ($options) {
				case 'USD-BYN':
					$config = ['curr' => 'USD', 'dir' => 'curr-to-byn'];
					break;
				case 'BYN-USD':
					$config = ['curr' => 'USD', 'dir' => 'byn-to-curr'];
					break;
				default:
					$config = $options;
			}

			return $n->convert($input, $config);
		});
        break;
}