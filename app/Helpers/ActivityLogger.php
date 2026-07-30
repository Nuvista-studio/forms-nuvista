<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $type,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $properties = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'properties' => $properties ? json_encode($properties) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
