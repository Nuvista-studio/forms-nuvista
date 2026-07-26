<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'html_content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActive(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    public function getStoragePath(): string
    {
        return storage_path("app/templates/{$this->slug}.blade.php");
    }

    public function syncToFile(): void
    {
        $dir = dirname($this->getStoragePath());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->getStoragePath(), $this->html_content);
    }
}
