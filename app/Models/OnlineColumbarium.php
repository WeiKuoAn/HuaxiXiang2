<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineColumbarium extends Model
{
    use HasFactory;

    /**
     * Get the pet that owns the columbarium.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}