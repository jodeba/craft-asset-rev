<?php

namespace Club\AssetRev\utilities;

use Craft;
use ErrorException;
use Club\AssetRev\AssetRev;
use InvalidArgumentException;
use Club\AssetRev\exceptions\ContinueException;

class FilenameRev
{
    protected $config;
    protected $basePath;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function setBasePath($path)
    {
        return $this->basePath = $path;
    }

    public function rev($file)
    {
        $strategies = array_filter(explode('|', $this->config->pipeline));

        return $this->prependAssetPrefix(
            $this->executeStrategies($file, $strategies, $this->basePath)
        );
    }

    protected function executeStrategies($file, array $strategies, $basePath)
    {
        if (empty($strategies)) {
            throw new InvalidArgumentException('No revving strategies have been configured.');
        }

        foreach ($strategies as $strategy) {
            if (!array_key_exists($strategy, $this->config->strategies)) {
                throw new InvalidArgumentException("The strategy `$strategy` has not been configured.");
            }

            try {
                return $this->revFilenameUsingStrategy($file, $this->config->strategies[$strategy], $basePath);
            } catch (ContinueException $e) {
                Craft::info($e->getMessage() . '. Continuing to next strategy...');
                continue;
            }
        }

        throw new ErrorException('None of the configured strategies `' . $this->config->pipeline . '` returned a value.');
    }

    protected function revFilenameUsingStrategy($file, $strategy, $basePath)
    {
        if (is_callable($strategy)) {
            return $strategy($file, $this->config, $basePath);
        }

        if (!class_exists($strategy)) {
            throw new InvalidArgumentException('class does not exist');
        }

        $class = new $strategy($this->config, $basePath);

        if (!$class instanceof Strategy) {
            throw new InvalidArgumentException(
                "Strategy class `$strategy` must be an instance of `AssetRev\Utilities\Strategy`"
            );
        }

        return $class->rev($file);
    }

    protected function prependAssetPrefix($file)
    {
        if (!empty($this->config->assetUrlPrefix)) {
            return $this->config->assetUrlPrefix . $file;
        }

        return $file;
    }
}
