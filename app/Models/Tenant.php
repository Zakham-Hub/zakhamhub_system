<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $table = 'tenants';
    protected $fillable = [
        'name',
        'domain',
        'subdomain',
        'database',
        'username',
        'password',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
