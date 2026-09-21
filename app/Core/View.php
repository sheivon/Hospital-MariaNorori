<?php

namespace App\Core;

/**
 * Simple view rendering helper.
 */
class View
{
    /**
     * Render a template file from the templates directory.
     *
     * @param string $template
     * @param array<string,mixed> $data
     */
    public static function render(string $template, array $data = []): void
    {
        $templateFile = dirname(__DIR__, 2) . '/templates/' . $template;
        if (!is_file($templateFile)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        extract($data, EXTR_SKIP);
        include $templateFile;
    }
}
