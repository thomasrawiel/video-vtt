<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function (): void {
    $rendererRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::class);
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\YouTubeRenderer::class
    );
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\VimeoRenderer::class
    );
    $rendererRegistry->registerRendererClass(
        TRAW\VideoVtt\Resource\Rendering\VideoTagRenderer::class
    );
    if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'])) {
        $txt = array_filter(array_map('trim', explode(',', (string)$GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'])));
        $txt[] = 'vtt';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'] = implode(',', array_unique($txt));
    }
});
