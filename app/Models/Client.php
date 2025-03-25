<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';
    protected $primaryKey = 'id_client';
    public $timestamps = true;

    protected $fillable = [
        'id_branch_client',
        'name_client',
        'address_client',
        'phone_client',
        'is_active_client'
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_client', 'id_branch');
    }

    public function transactions_rental()
    {
        return $this->hasMany(Transaction_Rental::class, 'id_client_transaction_rental', 'id_client');
    }

    public function invoice_rental()
    {
        return $this->hasMany(Invoice_Rental::class, 'id_client_invoice', 'id_client');
    }
}
