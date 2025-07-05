<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskVersion;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use Carbon\Carbon;


class TaskService
{

    /**
     * Walidacja pól formularza zadania.
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => 'bail|required|max:255',
            'description' => 'nullable',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to-do,in-progress,done',
            'due_date' => 'required|date|date_format:d.m.Y',
        ]);
    }

    /**
     * Tworzy nową wersję zadania i aktualizuje wskaźnik current_version_id.
     */
    public function createVersion(Task $task): TaskVersion
    {

        $data = $this->sanitize($this->validateTask(request()));

        // Tworzenie wersji
        $version = $task->versions()->create($data);

        // Aktualizacja wskazania na aktualną wersję
        $task->update(['current_version_id' => $version->id]);

        return $version;
    }

    /**
     * Zwraca historię wszystkich wersji danego zadania (najnowsze pierwsze).
     */
    public function getHistory(Task $task): Collection
    {
        return $task->versions()->latest()->get();
    }

    /**
     * Oczyszcza dane zadania z potencjalnie niebezpiecznego kodu HTML.
     *
     * Usuwa wszystkie znaczniki HTML z pól 'title' i 'description',
     * aby zapobiec atakom XSS lub przypadkowemu wyświetlaniu formatowania.
     */
    private function sanitize(array $data): array
    {
        $data['title'] = strip_tags($data['title'] ?? '');
        $data['description'] = strip_tags($data['description'] ?? '');
        return $data;
    }


}
