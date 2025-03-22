<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_Item extends Model
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
        'last_date_transaction_laundry'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_transaction_laundry', 'id_user');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_transaction_laundry', 'id_branch');
    }

    public function listTransactions()
    {
        return $this->hasMany(List_Transaction_Rental::class, 'id_transaction_laundry', 'id_transaction_laundry');
    }
}