<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';
    protected $primaryKey = 'id_branch';
    public $timestamps = true;

    protected $fillable = [
        'name_branch',
        'city_branch',
        'address_branch',
        'type_branch',
        'is_active_branch'
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

    public function users()
    {
        return $this->hasMany(User::class, 'id_branch_user', 'id_branch');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'id_branch_client', 'id_branch');
    }

    public function laundryItems()
    {
        return $this->hasMany(Laundry_Item::class, 'id_branch_laundry_item', 'id_branch');
    }

    public function rentalItems()
    {
        return $this->hasMany(Rental_Item::class, 'id_branch_rental_item', 'id_branch');
    }

    public function transactionsLaundry()
    {
        return $this->hasMany(Transaction_Laundry::class, 'id_branch_transaction_laundry', 'id_branch');
    }

    public function transactionsRental()
    {
        return $this->hasMany(Transaction_Rental::class, 'id_branch_transaction_rental', 'id_branch');
    }

    public function invoicesRental()
    {
        return $this->hasMany(Invoice_Rental::class, 'id_branch_invoice', 'id_branch');
    }
}
