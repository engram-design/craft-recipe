<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial
 * conversion, portion calculation, and JSON-LD microdata support
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2020 nystudio107
 */

namespace nystudio107\recipe\helpers;

use Craft;
use craft\helpers\Template;
use craft\web\View;

use yii\base\Exception;

use Twig\Markup;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.1.0
 */
class PluginTemplate
{
    // Static Methods
    // =========================================================================

    public static function renderStringTemplate(string $templateString, array $params = []): string
    {
        try {
            $html = Craft::$app->getView()->renderString($templateString, $params);
        } catch (\Exception $e) {
            $html = Craft::t(
                'recipe',
                'Error rendering template string -> {error}',
                ['error' => $e->getMessage()]
            );
            Craft::error($html, __METHOD__);
        }

        return $html;
    }

    /**
     * Render a plugin template
     *
     * @param $templatePath
     * @param $params
     *
     * @return Markup
     */
    public static function renderPluginTemplate(string $templatePath, array $params = []): Markup
    {
        $htmlText = '';
        // Stash the old template mode, and set it Control Panel template mode
        $oldMode = Craft::$app->view->getTemplateMode();
        $templateRendered = false;
        // Look for a frontend template to render first
        if (!$templateRendered) {
            try {
                Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);
            } catch (Exception $e) {
                Craft::error($e->getMessage(), __METHOD__);
            }
            // Render the template with our vars passed in
            try {
                $htmlText = Craft::$app->view->renderTemplate('recipe/' .$templatePath, $params);
                $templateRendered = true;
            } catch (\Exception $e) {
                $htmlText = Craft::t(
                    'recipe',
                    'Error rendering `{template}` -> {error}',
                    ['template' => $templatePath, 'error' => $e->getMessage()]
                );
                Craft::error($htmlText, __METHOD__);
                $templateRendered = false;
            }
        }
        // If no frontend template was found, try our built-in template
        if (!$templateRendered) {
            try {
                Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
            } catch (Exception $e) {
                Craft::error($e->getMessage(), __METHOD__);
            }
            // Render the template with our vars passed in
            try {
                $htmlText = Craft::$app->view->renderTemplate('recipe/' . $templatePath, $params);
                $templateRendered = true;
            } catch (\Exception $e) {
                $htmlText = Craft::t(
                    'recipe',
                    'Error rendering `{template}` -> {error}',
                    ['template' => $templatePath, 'error' => $e->getMessage()]
                );
                Craft::error($htmlText, __METHOD__);
                $templateRendered = false;
            }
        }

        // Restore the old template mode
        try {
            Craft::$app->view->setTemplateMode($oldMode);
        } catch (Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }

        return Template::raw($htmlText);
    }
}
