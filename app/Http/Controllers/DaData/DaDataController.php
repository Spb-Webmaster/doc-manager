<?php

declare(strict_types=1);

namespace App\Http\Controllers\DaData;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Прокси к API DaData для автозаполнения реквизитов на формах кабинета.
 */
class DaDataController extends Controller
{
    /**
     * Запрос к findById-справочнику DaData (party / bank) с токенами из конфига.
     */
    private function request(string $endpoint, string $query): \Illuminate\Http\Client\Response
    {
        return Http::withHeaders([
            'Authorization' => 'Token ' . config('services.dadata.token'),
            'X-Secret'      => config('services.dadata.secret'),
        ])->post("https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/{$endpoint}", [
            'query' => $query,
        ]);
    }

    /**
     * POST /dadata/party — поиск организации/ИП по ИНН (AJAX).
     *
     * Возвращает JSON с наименованием (полным и кратким), ОГРН, КПП, адресом,
     * телефоном и email; 404 — если ИНН не найден, 422 — если сервис недоступен.
     */
    public function party(Request $request): JsonResponse
    {
        $inn = trim((string) $request->input('inn'));

        try {
            $response = $this->request('party', $inn);
        } catch (\Throwable) {
            return response()->json(['error' => 'Сервис ФНС недоступен'], 422);
        }

        $suggestions = $response->json('suggestions');

        if ($response->failed() || empty($suggestions)) {
            return response()->json(['error' => 'Организация с таким ИНН не найдена'], 404);
        }

        $s    = $suggestions[0];
        $data = $s['data'];

        $phone = '';
        if (!empty($data['phones'])) {
            $phone = preg_replace('/\D/', '', $data['phones'][0]['value'] ?? '');
        }

        return response()->json([
            'name'    => $data['name']['full_with_opf'] ?? $s['value'] ?? '',
            'short'   => $data['name']['short_with_opf'] ?? $s['value'] ?? '',
            'ogrn'    => $data['ogrn'] ?? '',
            'kpp'     => $data['kpp'] ?? '',
            'address' => $data['address']['value'] ?? '',
            'phone'   => $phone,
            'email'   => $data['emails'][0]['value'] ?? '',
        ]);
    }

    /**
     * POST /dadata/bank — поиск банка по БИК (AJAX).
     *
     * Возвращает JSON с названием банка, корсчётом и городом;
     * 404 — если банк не найден, 422 — если сервис недоступен.
     */
    public function bank(Request $request): JsonResponse
    {
        $bik = trim((string) $request->input('bik'));

        try {
            $response = $this->request('bank', $bik);
        } catch (\Throwable) {
            return response()->json(['error' => 'Сервис банковских реквизитов недоступен'], 422);
        }

        $suggestions = $response->json('suggestions');

        if ($response->failed() || empty($suggestions)) {
            return response()->json(['error' => 'Банк не найден'], 404);
        }

        $data = $suggestions[0]['data'];

        return response()->json([
            'name'                  => $data['name']['payment'] ?? $suggestions[0]['value'],
            'correspondent_account' => $data['correspondent_account'] ?? '',
            'city'                  => $data['address']['data']['city'] ?? '',
        ]);
    }
}
