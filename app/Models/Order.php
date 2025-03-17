<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_price', 'status','payment_status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'order_medicine')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(OrderStatus::class)->latestOfMany();
    }
}
