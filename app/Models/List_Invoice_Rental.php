<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class List_Invoice_Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'list__invoice__rentals';
    protected $primaryKey = 'id_list_invoice_rental';
    public $timestamps = true;

    protected $fillable = [
        'id_rental_invoice',
        'id_rental_transaction',
        'status_list_invoice_rental',
        'note_list_invoice_rental',
        'price_list_invoice_rental',
        'weight_list_invoice_rental',
        'is_active_list_invoice_rental',
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

    public function invoiceRental()
    {
        return $this->belongsTo(Invoice_Rental::class, 'id_rental_invoice', 'id_invoice_rental');
    }

    public function transactionRental()
    {
        return $this->belongsTo(Transaction_Rental::class, 'id_rental_transaction', 'id_transaction_rental');
    }
}
