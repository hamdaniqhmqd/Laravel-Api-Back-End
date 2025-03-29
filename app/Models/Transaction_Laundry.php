<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction_Laundry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_laundries';
    protected $primaryKey = 'id_transaction_laundry';
    public $timestamps = true;

    protected $fillable = [
        'id_user_transaction_laundry',
        'id_branch_transaction_laundry',
        'name_client_transaction_laundry',
        'status_transaction_laundry',
        'notes_transaction_laundry',
        'total_weight_transaction_laundry',
        'total_price_transaction_laundry',
        'total_laundry_transaction_laundry',
        'promo_transaction_laundry',
        'additional_cost_transaction_laundry',
        'total_transaction_laundry',
        'cash_transaction_laundry',
        'change_money_transaction_laundry',
        'is_active_transaction_laundry',
        'first_date_transaction_laundry',
        'last_date_transaction_laundry',
    ];

    protected $casts = [
        'total_weight_transaction_laundry' => 'float',
        'total_price_transaction_laundry' => 'float',
        'total_laundry_transaction_laundry' => 'integer',
        'promo_transaction_laundry' => 'float',
        'additional_cost_transaction_laundry' => 'float',
        'total_transaction_laundry' => 'float',
        'cash_transaction_laundry' => 'float',
        'change_money_transaction_laundry' => 'float',
    ];

    protected $dates = [
        'first_date_transaction_laundry',
        'last_date_transaction_laundry',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    // Relasi ke User (Kurir)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_transaction_laundry', 'id_user');
    }

    // Relasi ke Branch (Cabang)
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_transaction_laundry', 'id_branch');
    }

    // Relasi ke List_Transaction_Laundry
    public function listTransactionLaundry()
    {
        return $this->hasMany(List_Transaction_Laundry::class, 'id_transaction_laundry', 'id_transaction_laundry');
    }
}
