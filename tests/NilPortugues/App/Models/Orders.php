<?php

namespace NilPortugues\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public $timestamps = false;
    public $incrementing = true;
    protected $table = 'orders';
    protected $primaryKey = 'id';
    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'customer_id',
        'order_date',
        'shipped_date',
        'shipper_id',
        'ship_name',
        'ship_address',
        'ship_city',
        'ship_state_province',
        'ship_zip_postal_code',
        'ship_country_region',
        'shipping_fee',
        'taxes',
        'payment_type',
        'paid_date',
        'notes',
        'tax_rate',
        'tax_status_id',
        'status_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
}
