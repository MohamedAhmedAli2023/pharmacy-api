<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'image_path', 'ocr_result', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
