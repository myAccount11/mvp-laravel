<?php

namespace App\Traits;

trait SnakeToCamelCase
{
    /**
     * Convert snake_case keys to camelCase recursively
     */
    public function toCamelCaseArray(): array
    {
        return $this->convertKeysToCamelCase($this->toArray(), 0, []);
    }

    /**
     * Recursively convert array keys from snake_case to camelCase with depth limiting
     */
    protected function convertKeysToCamelCase($data, $depth = 0, $visited = [])
    {
        // Prevent infinite recursion - limit depth to 5 levels
        if ($depth > 5) {
            return $data;
        }

        if (!is_array($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $value) {
            $camelKey = $this->snakeToCamel($key);
            
            // Skip if we've already processed this key at this depth (prevent circular refs)
            $keyHash = md5($camelKey . $depth);
            if (in_array($keyHash, $visited)) {
                continue;
            }
            
            $newVisited = $visited;
            $newVisited[] = $keyHash;
            
            if (is_array($value)) {
                $result[$camelKey] = $this->convertKeysToCamelCase($value, $depth + 1, $newVisited);
            } else {
                $result[$camelKey] = $value;
            }
        }
        return $result;
    }

    /**
     * Convert snake_case to camelCase
     */
    protected function snakeToCamel(string $string): string
    {
        // Handle special cases
        $specialCases = [
            '24sec_required' => '24secRequired',
            'tl_edit_enabled' => 'TLEditEnabled',
            'cm_time_set_until' => 'CMTimeSetUntil',
        ];

        if (isset($specialCases[$string])) {
            return $specialCases[$string];
        }

        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }
}

