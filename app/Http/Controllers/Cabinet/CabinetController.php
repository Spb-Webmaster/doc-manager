<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CabinetController extends Controller
{
    public function index(): View
    {
        $user  = auth()->user();
        $legal = $user->legalEntity;
        $ip    = $user->individualEntrepreneur;
        $req   = $legal ?? $ip;
        $bank  = $user->bankAccounts()->orderByDesc('is_primary')->orderBy('created_at')->first();

        $reqData = null;
        if ($req && $req->inn) {
            $reqData = [
                'inn'        => $req->inn,
                'name'       => $req->full_name ?? $req->name ?? null,
                'ogrn_label' => $legal ? 'ОГРН' : 'ОГРНИП',
                'ogrn'       => $legal ? ($legal->ogrn ?? null) : ($ip->ogrnip ?? null),
                'address'    => $legal ? ($legal->legal_address ?? null) : ($ip->register_address ?? null),
            ];
        }

        $contractors = $user->contractors()
            ->withCount(['invoices', 'acts'])
            ->orderByDesc('created_at')
            ->get();

        return view('cabinet.index', compact('reqData', 'bank', 'contractors'));
    }
}
