<?php

namespace Club\AssetRev\utilities;

use craft\base\Model;

interface StrategyContract
{
    public function __construct(Model $config, $basePath = null);
    public function rev($file);
}
