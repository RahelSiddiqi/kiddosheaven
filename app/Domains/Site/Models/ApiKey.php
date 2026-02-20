<?php

namespace App\Domains\Site\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id', 'name', 'key', 'secret', 'scopes',
        'last_used_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'scopes'       => 'array',
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    protected $hidden = ['secret'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public static function generate(int $siteId, string $name, array $scopes = []): array
    {
        $plainKey    = 'kh_' . Str::random(32);
        $plainSecret = Str::random(64);

        $model = static::create([
            'site_id' => $siteId,
            'name'    => $name,
            'key'     => hash('sha256', $plainKey),
            'secret'  => bcrypt($plainSecret),
            'scopes'  => $scopes,
        ]);

        return [
            'api_key'    => $plainKey,
            'api_secret' => $plainSecret,
            'id'         => $model->id,
        ];
    }
}
