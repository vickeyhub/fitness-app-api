<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionCatalogItem extends Model
{
    public const TYPE_MUSCLE = 'muscle';

    public const TYPE_FITNESS_GOAL = 'fitness_goal';

    public const TYPE_SESSION_TYPE = 'session_type';

    public const TYPE_KEYWORD = 'keyword';

    protected $fillable = [
        'type',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_MUSCLE,
            self::TYPE_FITNESS_GOAL,
            self::TYPE_SESSION_TYPE,
            self::TYPE_KEYWORD,
        ];
    }
}
