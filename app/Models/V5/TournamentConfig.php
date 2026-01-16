<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentConfig extends Model
{
    use HasFactory;

    protected $table = 'tournament_configs';

    protected $fillable = [
        'tournament_id',
        'settings',
    ];

    protected $casts = [
        'tournament_id' => 'integer',
        'settings' => 'array', // Automatically cast JSON to array
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a specific setting value
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
    }
}
