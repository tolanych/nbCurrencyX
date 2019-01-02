<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/nbCurrencyX/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/nbcurrencyx')) {
            $cache->deleteTree(
                $dev . 'assets/components/nbcurrencyx/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/nbcurrencyx/', $dev . 'assets/components/nbcurrencyx');
        }
        if (!is_link($dev . 'core/components/nbcurrencyx')) {
            $cache->deleteTree(
                $dev . 'core/components/nbcurrencyx/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/nbcurrencyx/', $dev . 'core/components/nbcurrencyx');
        }
    }
}

return true;