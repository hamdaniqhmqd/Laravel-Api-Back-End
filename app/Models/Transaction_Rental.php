<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction_Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_rentals';
    protected $primaryKey = 'id_transaction_rental';
    public $timestamps = true;

    protected $fillable = [
        'id_kurir_transaction_rental',
        'id_branch_transaction_rental',
        'id_client_transaction_rental',
        'recipient_name_transaction_rental',
        'type_rental_transaction',
        'status_transaction_rental',
        'total_weight_transaction_rental',
        'total_pcs_transaction_rental',
        'promo_transaction_rental',
        'additional_cost_transaction_rental',
        'total_price_transaction_rental',
        'notes_transaction_rental',
        'is_active_transaction_rental',
        // 'first_date_transaction_rental',
        // 'last_date_transaction_rental',
    ];

    protected $casts = [
        'total_weight_transaction_rental' => 'float',
        'total_pcs_transaction_rental' => 'integer',
        'promo_transaction_rental' => 'float',
        'additional_cost_transaction_rental' => 'float',
        'total_price_transaction_rental' => 'float',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var list<string, string>
     */
    protected $dates = [
        // 'first_date_transaction_rental',
        // 'last_date_transaction_rental',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function kurir()
    {
        return $this->belongsTo(User::class, 'id_kurir_transaction_rental', 'id_user');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_transaction_rental', 'id_branch');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client_transaction_rental', 'id_client');
    }

    public function listTransactionRentals()
    {
        return $this->hasMany(List_Transaction_Rental::class, 'id_rental_transaction', 'id_transaction_rental');
    }

    public function ListInvoice()
    {
        return $this->hasMany(List_Invoice_Rental::class, 'id_list_invoice_rental', 'id_transaction_rental');
    }
}
