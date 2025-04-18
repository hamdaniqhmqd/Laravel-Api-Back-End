<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice_Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_rentals';
    protected $primaryKey = 'id_invoice_rental';
    public $timestamps = true;

    protected $fillable = [
        'id_branch_invoice',
        'id_client_invoice',
        'notes_invoice_rental',
        'time_invoice_rental',
        'total_weight_invoice_rental',
        'price_invoice_rental',
        'promo_invoice_rental',
        'additional_cost_invoice_rental',
        'total_price_invoice_rental',
        'is_active_invoice_rental',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var list<string, string>
     */
    protected $dates = [
        'time_invoice_rental',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_invoice', 'id_branch');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client_invoice', 'id_client');
    }

    public function listInvoiceRentals()
    {
        return $this->hasMany(List_Invoice_Rental::class, 'id_rental_invoice', 'id_invoice_rental');
    }
}
