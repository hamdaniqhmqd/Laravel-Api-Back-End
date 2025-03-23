<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_Item extends Model
{
  use HasFactory;

  protected $table = 'rental_items';
  protected $primaryKey = 'id_rental_item';
  public $timestamps = true;

  protected $fillable = [
    'id_branch_rental_item',
    'number_rental_item',
    'name_rental_item',
    'price_rental_item',
    'status_rental_item',
    'condition_rental_item',
    'description_rental_item',
    'is_active_rental_item',
  ];

  public function branch()
  {
    return $this->belongsTo(Branch::class, 'id_branch_rental_item', 'id_branch');
  }

  public function listTransactionRental()
  {
    return $this->hasMany(List_Transaction_Rental::class, 'id_item_rental', 'id_rental_item');
  }
}