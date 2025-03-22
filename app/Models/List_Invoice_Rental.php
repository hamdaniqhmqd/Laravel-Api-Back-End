<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class List_Invoice_Rental extends Model
{
    use HasFactory;

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

    public function invoice()
    {
        return $this->belongsTo(Invoice_Rental::class, 'id_rental_invoice', 'id_invoice_rental');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction_Rental::class, 'id_rental_transaction', 'id_transaction_rental');
    }
}
