<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logging extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loggings';

    protected $fillable = [
        'user_id',
        'ip_address',
        'message',
        'action',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var list<string, string>
     */
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function record($user = null, $message)
    {
        return static::create([
            'user_id' => $user->id_user,
            'ip_address' => request()->ip(),
            'message' => $message,
            'action' => request()->fullUrl()
        ]);
    }
}
