<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class List_Transaction_Laundry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'list_transaction_laundries';
    protected $primaryKey = 'id_list_transaction_laundry';
    public $timestamps = true;

    protected $fillable = [
        'id_transaction_laundry',
        'id_item_laundry',
        'price_list_transaction_laundry',
        'weight_list_transaction_laundry',
        'total_price_list_transaction_laundry',
        'status_list_transaction_laundry',
        'note_list_transaction_laundry',
        'is_active_list_transaction_laundry'
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

    public function transactionLaundry()
    {
        return $this->belongsTo(Transaction_Laundry::class, 'id_transaction_laundry', 'id_transaction_laundry');
    }

    public function laundryItem()
    {
        return $this->belongsTo(Laundry_Item::class, 'id_item_laundry', 'id_laundry_item');
    }
}
