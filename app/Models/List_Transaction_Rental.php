<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class List_Transaction_Rental extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'list_transaction_rentals';
    protected $primaryKey = 'id_list_transaction_rental';
    public $timestamps = true;

    protected $fillable = [
        'id_rental_transaction',
        'id_item_rental',
        'status_list_transaction_rental',
        'condition_list_transaction_rental',
        'note_list_transaction_rental',
        'price_list_transaction_rental',
        'weight_list_transaction_rental',
        'is_active_list_transaction_rental',
    ];

    protected $casts = [
        'price_list_transaction_rental' => 'integer',
        'weight_list_transaction_rental' => 'float',
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

    public function transactionRental()
    {
        return $this->belongsTo(Transaction_Rental::class, 'id_rental_transaction', 'id_transaction_rental');
    }

    public function itemRental()
    {
        return $this->belongsTo(Rental_Item::class, 'id_item_rental', 'id_rental_item');
    }
}
