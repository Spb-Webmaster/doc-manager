<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Contractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractsController extends Controller
{
    public function index(Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        return response()->json(
            $contractor->contracts()->orderBy('sort_order')->orderByDesc('id')->get()
        );
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ])['ids'];

        $userIds = Contract::where('user_id', auth()->id())->pluck('id')->flip();

        foreach ($ids as $order => $id) {
            if ($userIds->has($id)) {
                Contract::where('id', $id)->update(['sort_order' => $order]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request, Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'number' => 'required|string|max:100',
            'date'   => 'required|date',
        ]);

        $contract = $contractor->contracts()->create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['contract' => $contract], 201);
    }

    public function update(Request $request, Contract $contract): JsonResponse
    {
        abort_unless($contract->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'number' => 'required|string|max:100',
            'date'   => 'required|date',
        ]);

        $contract->update($data);

        return response()->json(['contract' => $contract->fresh()]);
    }

    public function destroy(Contract $contract): JsonResponse
    {
        abort_unless($contract->user_id === auth()->id(), 403);

        $contract->delete();

        return response()->json(['message' => 'Договор удалён']);
    }
}
