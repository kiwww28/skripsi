<?php

namespace App\Models;

use App\Observers\TransaksiObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TransaksiObserver::class])]
class Transaksi extends Model
{
    protected $guarded = ['id'];

    public function product () {
        return $this->belongsTo(Product::class);
    }
}
