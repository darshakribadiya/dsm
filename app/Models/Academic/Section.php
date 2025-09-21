<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = ['standard_id', 'name', 'capacity'];

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }
}
