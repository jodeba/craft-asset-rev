<?php

namespace Club\AssetRev\Utilities;

use craft\base\Model;

abstract class Strategy implements StrategyContract
{
    protected $config = [];
    protected $basePath;

    public function __construct(Model $config, $basePath = null)
    {
        $this->config = $config;
        $this->basePath = $basePath;
    }

    protected function getAbsolutePath($path)
    {
        if (strpos($path, DIRECTORY_SEPARATOR) === 0) {
            return $path;
        }

        return $this->basePath . $path;
    }

    protected function normalisePath($path)
    {
        return rtrim($path, '/');
    }
}