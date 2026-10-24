<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- IMPORTA O TRAIT
use Illuminate\Support\Carbon;

class Card extends Model
{
    use HasFactory, SoftDeletes; // <-- ATIVA O TRAIT AQUI

    protected $fillable = [
        'card_list_id',
        'title',
        'description',
        'position',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(CardList::class, 'card_list_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CardHistory::class)->latest('created_at');
    }

    public function status(?Carbon $listEndDate = null): string
    {
        if ($this->completed_at !== null) {
            return 'feito';
        }

        $deadline = $this->due_date ?? $listEndDate;

        if ($deadline !== null && $deadline->isBefore(today())) {
            return 'atraso';
        }

        return 'pendente';
    }
}