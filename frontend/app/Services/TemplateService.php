<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Str;

class TemplateService
{
    /**
     * Process template content by replacing variables.
     *
     * @param Template $template
     * @param array $variables Key-value pairs for variable replacement
     * @return array Processed template data
     */
    public function processTemplate(Template $template, array $variables = []): array
    {
        $processed = [
            'content' => $template->content,
            'message_type' => $template->message_type,
            'metadata' => $template->metadata ?? [],
        ];

        // Replace variables in content
        $processed['content'] = $this->replaceVariables($template->content, $variables);

        // Replace variables in metadata if needed
        if (!empty($processed['metadata'])) {
            $processed['metadata'] = $this->replaceVariablesInArray($processed['metadata'], $variables);
        }

        return $processed;
    }

    /**
     * Replace variables in a string.
     *
     * @param string $content
     * @param array $variables
     * @return string
     */
    public function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Support both {{variable}} and {variable} formats
            $content = str_replace(['{{' . $key . '}}', '{' . $key . '}'], $value, $content);
        }

        return $content;
    }

    /**
     * Replace variables in an array recursively.
     *
     * @param array $data
     * @param array $variables
     * @return array
     */
    public function replaceVariablesInArray(array $data, array $variables): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $result[$key] = $this->replaceVariables($value, $variables);
            } elseif (is_array($value)) {
                $result[$key] = $this->replaceVariablesInArray($value, $variables);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Extract variable names from content.
     *
     * @param string $content
     * @return array
     */
    public function extractVariables(string $content): array
    {
        $variables = [];
        // Match {{variable}} format
        preg_match_all('/\{\{(\w+)\}\}/', $content, $matches);
        
        if (!empty($matches[1])) {
            $variables = array_unique($matches[1]);
        }
        
        return $variables;
    }

    /**
     * Validate that all required variables are provided.
     *
     * @param Template $template
     * @param array $variables
     * @return array ['valid' => bool, 'missing' => array]
     */
    public function validateVariables(Template $template, array $variables): array
    {
        $required = $template->variables ?? $template->extractVariables();
        $provided = array_keys($variables);
        $missing = array_diff($required, $provided);

        return [
            'valid' => empty($missing),
            'missing' => array_values($missing),
        ];
    }
}

