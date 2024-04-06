<?php

namespace Abavo\SitePackage\ViewHelpers;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to render the page title
 *
 * # Example: Basic Example
 * # Description: Render the content of the VH as page title
 * <code>
 *    <vh:pageTitle>Foo</vh:pageTitle>
 * </code>
 *
 */
class PageTitleViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    use CompileWithRenderStatic;

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = trim($renderChildrenClosure());
        if (!empty($content)) {
            $GLOBALS['TSFE']->altPageTitle = $content;
            $GLOBALS['TSFE']->indexedDocTitle = $content;
            $GLOBALS['TSFE']->page['title'] = $content;
        }
    }
}
