<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait BulkDeletesDocuments
{
    /**
     * Удаляет записи по списку id в рамках переданной relation (уже
     * ограниченной текущим пользователем), по одной через delete(),
     * чтобы сработали модельные события (генерация/чистка PDF и т.д.).
     */
    protected function bulkDeleteOwned(Request $request, Relation $ownedRelation): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $models = $ownedRelation->whereIn('id', $validated['ids'])->get();

        foreach ($models as $model) {
            $model->delete();
        }

        return response()->json(['ok' => true, 'deleted' => $models->count()]);
    }
}
