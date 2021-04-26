<?php
defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function () {
    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\YouTubeRenderer::class
    );
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\VimeoRenderer::class
    );
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\VideoTagRenderer::class
    );
});