<?php

namespace NilPortugues\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'employees';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * @var array
     */
    protected $fillable = [
        'company',
        'last_name',
        'first_name',
        'email_address',
        'job_title',
        'business_phone',
        'home_phone',
        'mobile_phone',
        'fax_number',
        'address',
        'city',
        'state_province',
        'zip_postal_code',
        'country_region',
        'web_page',
        'notes',
        'attachments',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOrders()
    {
        return $this->hasMany(Orders::class, 'employee_id')->limit(10);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
