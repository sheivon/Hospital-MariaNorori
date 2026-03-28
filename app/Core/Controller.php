<?php

namespace App\Core;

/**
 * Base controller with helper methods for rendering views.
 */
class Controller
{
    /**
     * Render a template from the templates/ directory.
     *
     * @param string $template Path relative to templates/ (no leading slash, can include subfolders)
     * @param array<string,mixed> $data
     * @return void
     */
    protected function render(string $template, array $data = []): void
    {
        $templateFile = dirname(__DIR__, 2) . '/templates/' . $template;
        if (!is_file($templateFile)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        // Expose data as variables in the template scope
        extract($data, EXTR_SKIP);
        include $templateFile;
    }

    /**
     * Redirect to another URL (relative or absolute).
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
