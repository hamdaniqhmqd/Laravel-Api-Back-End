<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction_Laundry extends Model
{
    use HasFactory;

    protected $table = 'transaction_laundries';
    protected $primaryKey = 'id_transaction_laundry';
    public $timestamps = true;

    protected $fillable = [
        'id_user_transaction_laundry',
        'id_branch_transaction_laundry',
        'time_transaction_laundry',
        'name_client_transaction_laundry',
        'status_transaction_laundry',
        'notes_transaction_laundry',
        'total_weight_transaction_laundry',
        'total_price_transaction_laundry',
        'cash_transaction_laundry',
        'is_active_transaction_laundry',
        'first_date_transaction_laundry',
        'last_date_transaction_laundry',
    ];

    protected $casts = [
        'time_transaction_laundry' => 'datetime:H:i',
        'total_weight_transaction_laundry' => 'float',
        'total_price_transaction_laundry' => 'float',
        'cash_transaction_laundry' => 'float',
        'first_date_transaction_laundry' => 'date',
        'last_date_transaction_laundry' => 'date',
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
        return $this->hasMany(List_Transaction_Laundry::class, 'id_list_transaction_laundry', 'id_transaction_laundry');
    }
}