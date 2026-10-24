<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- IMPORTA O TRAIT

class Card extends Model
{
    use HasFactory, SoftDeletes; // <-- ATIVA O TRAIT AQUI

    protected $fillable = [
        'card_list_id',
        'title',
        'description',
        'position',
        'due_date',
    ];

    public function list()
    {
        return $this->belongsTo(CardList::class, 'card_list_id');
    }
}