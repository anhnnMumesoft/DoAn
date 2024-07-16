<?php

namespace App\Helpers;

class ValidationHelper
{
    public static function validateRequiredFields($data, $requiredFields)
    {
        $missingFields = [];

        foreach ($requiredFields as $field => $displayName) {
            if (empty($data[$field])) {
                $missingFields[] = $displayName;
            }
        }

        if (!empty($missingFields)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Thiếu trường bắt buộc: ' . implode(', ', $missingFields)
            ];
        }

        return null;
    }
}