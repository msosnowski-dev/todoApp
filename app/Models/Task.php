<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public static function priorities(): array
    {
        return [
            'low' => 'Niski',
            'medium' => 'Średni',
            'high' => 'Wysoki',
        ];
    }

    public static function statuses(): array
    {
        return [
            'to-do' => 'Do zrobienia',
            'in-progress' => 'W trakcie',
            'done' => 'Zrobione',
        ];
    }

    /**
     * Relacja: Zadanie należy do użytkownika.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d.m.Y') : null;
    }
}
