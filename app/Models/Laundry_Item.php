<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laundry_Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laundry_items';
    protected $primaryKey = 'id_laundry_item';
    public $timestamps = true;

    protected $fillable = [
        'id_branch_laundry_item',
        'name_laundry_item',
        'price_laundry_item',
        'time_laundry_item',
        'description_laundry_item',
        'is_active_laundry_item'
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
        return $this->belongsTo(Branch::class, 'id_branch_laundry_item', 'id_branch');
    }

    public function listTransactionLaundry()
    {
        return $this->hasMany(List_Transaction_Laundry::class, 'id_item_laundry', 'id_laundry_item');
    }
}
