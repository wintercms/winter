<?php

namespace System\Helpers;

use Winter\Storm\Database\Model;

/**
 * This helper class is used in migration scaffolding console scripts
 *
 */
class Migration
{

    /**
     * Maps model fields config to DB Schema column types.
     */
    public static function mapFieldType(string $fieldName, array $fieldConfig, ?Model $model = null) : array
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
                $dbType = 'mediumText';
                break;
            case 'textarea':
                $dbType = 'text';
                break;
            default:
                $dbType = 'string';
        }

        if ($model) {
            $rule = array_get($model->rules ?? [], $name, '');
            $rule = is_array($rule) ? implode(',', $rule) : $rule;

            $required = str_contains($rule, 'required') ? true : $fieldConfig['required'] ?? false;
        } else {
            $required = $fieldConfig['required'] ?? false;
        }

        return [
            'type' => $dbType,
            'required' => $required,
            'index' => in_array($fieldName, ["slug"]) or str_ends_with($fieldName, "_id"),
        ];
    }
}
