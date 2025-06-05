<?php

namespace App\Models;

class Proxy extends Model
{
    public static array $attrs = [
        "id",
        "type",
        "proxy",
        "enable",
        "reason",
        "account_id",
        "created_at",
        "updated_at",
    ];

    protected $fillable = [
        "type",
        "proxy",
        "enable",
        "reason",
        "account_id",
        "created_at",
        "updated_at",
    ];

    public function account()
    {
        return $this->hasOne(Account::class, "id", "account_id");
    }

    protected function casts(): array
    {
        return [
            'enable' => "boolean"
        ];
    }
}
