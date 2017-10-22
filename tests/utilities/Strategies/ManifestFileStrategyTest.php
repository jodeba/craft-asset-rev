<?php

use Club\AssetRev\models\Settings;
use Club\AssetRev\exceptions\ContinueException;
use Club\AssetRev\utilities\Strategies\ManifestFileStrategy;

class ManifestFileStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_throws_a_continue_exception_if_the_manifest_file_does_not_exist()
    {
        $this->expectException(ContinueException::class);

        $assetPath = stream_resolve_include_path('files/asset.css');
        $assetsPath = str_replace('files/asset.css', '', $assetPath);

        $settingsModel = new Settings(['manifestPath' => 'files/missing.json']);

        (new ManifestFileStrategy($settingsModel, $assetsPath))->rev('css/asset.css');
    }

    /**
     * @test
     */
    public function it_throws_a_continue_exception_if_the_asset_is_not_found_in_the_manifest_file()
    {
        $this->expectException(ContinueException::class);

        $assetPath = stream_resolve_include_path('files/asset.css');
        $assetsPath = str_replace('files/asset.css', '', $assetPath);

        $settingsModel = new Settings(['manifestPath' => 'files/manifest.json']);

        (new ManifestFileStrategy($settingsModel, $assetsPath))->rev('css/missing.css');
    }

    /**
     * @test
     */
    public function it_finds_the_manifest_file_when_the_path_is_relative()
    {
        $assetPath = stream_resolve_include_path('files/asset.css');
        $assetsPath = str_replace('files/asset.css', '', $assetPath);
        $config = new Settings(['manifestPath' => './tests/files/manifest.json']);
        $revver = new ManifestFileStrategy($config, $assetsPath);

        $this->assertEquals('css/asset.a9961d38.css', $revver->rev('css/asset.css'));
    }

    /**
     * @test
     */
    public function it_finds_the_manifest_file_when_the_path_is_absolute()
    {
        $assetPath = stream_resolve_include_path('tests/files/asset.css');
        $assetsPath = str_replace('tests/files/asset.css', '', $assetPath);

        $config = new Settings(['manifestPath' => $assetsPath . '/tests/files/manifest.json']);

        $revver = new ManifestFileStrategy($config);

        $this->assertEquals('css/asset.a9961d38.css', $revver->rev('css/asset.css'));
    }

    /**
     * @test
     */
    public function it_replaces_the_file_name_with_the_revved_version_in_the_manifest_file()
    {
        $assetPath = stream_resolve_include_path('tests/files/asset.css');
        $assetsPath = str_replace('tests/files/asset.css', '', $assetPath);
        $config = new Settings(['manifestPath' => 'tests/files/manifest.json']);
        $revver = new ManifestFileStrategy($config, $assetsPath);

        $this->assertEquals(
            'css/asset.a9961d38.css',
            $revver->rev('css/asset.css')
        );
    }
}
