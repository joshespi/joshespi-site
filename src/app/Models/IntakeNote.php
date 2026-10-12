<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntakeNote extends Model
{
    protected $fillable = ['body'];

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }
}
