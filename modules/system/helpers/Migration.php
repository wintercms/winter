<?php

namespace System\Helpers;

/**
 * This helper class is used in migration scaffolding console scripts
 *
 */
class Migration
{

    /**
     * Maps model fields config to DB Schema column types.
     */
    public static function mapFieldType(string $fieldName, array $fieldConfig) : array
    {
        switch ($fieldConfig['type'] ?? 'text') {
            case 'checkbox':
            case 'switch':
                $dbType = 'boolean';
                break;
            case 'number':
                if (isset($fieldConfig['step']) && is_int($fieldConfig['step'])) {
                    $dbType = 'integer';
                } else {
                    $dbType = 'double';
                }
                if ($dbType === 'integer' && isset($fieldConfig['min']) && $fieldConfig['min'] >= 0) {
                    $dbType = 'unsignedInteger';
                }
                break;
            case 'range':
                $dbType = 'unsignedInteger';
                break;
            case 'datepicker':
                $dbType = 'datetime';
                break;
            case 'markdown':
            case 'textarea':
                $dbType = 'mediumText';
                break;
            default:
                $dbType = 'string';
        }
        $required = $fieldConfig['required'] ?? false;

        return [
            'type' => $dbType,
            'required' => $required,
            'index' => in_array($fieldName, ["slug"]) or str_ends_with($fieldName, "_id"),
        ];
    }
}
