<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'amount','stripe_payment_id','method', 'status', 'transaction_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
