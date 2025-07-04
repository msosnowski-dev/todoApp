<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_version_id'
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

    public function publicTokens()
    {
        return $this->hasMany(PublicTaskToken::class);
    }

    public function currentVersion()
    {
        return $this->belongsTo(TaskVersion::class, 'current_version_id');
    }

    public function versions()
    {
        return $this->hasMany(TaskVersion::class);
    }
}
