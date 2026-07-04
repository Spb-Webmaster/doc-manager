<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\IndividualEntrepreneur;
use App\Models\LegalEntity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user  = auth()->user();
        $legal = $user->legalEntity;
        $ip    = $user->individualEntrepreneur;
        $req   = $legal ?? $ip;

        $requisites = $req ? [
            'inn'     => $req->inn,
            'name'    => $req->full_name ?? '',
            'short'   => $req->name ?? '',
            'ogrn'    => $legal ? ($legal->ogrn ?? '') : ($ip->ogrnip ?? ''),
            'kpp'     => $legal ? ($legal->kpp ?? '') : '',
            'address' => $legal ? ($legal->legal_address ?? '') : ($ip->register_address ?? ''),
            'phone'   => $req->phone ?? '',
            'email'   => $req->email ?? '',
        ] : [];

        $bankAccounts = $user->bankAccounts()->orderBy('sort_order')->orderBy('created_at')->get();

        $notifyInvoiceFromTemplate = $user->notify_invoice_from_template;

        return view('cabinet.settings', compact('requisites', 'bankAccounts', 'notifyInvoiceFromTemplate'));
    }

    public function saveRequisites(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inn'     => ['required', 'regex:/^\d{10}$|^\d{12}$/'],
            'name'    => ['nullable', 'string', 'max:500'],
            'short'   => ['nullable', 'string', 'max:255'],
            'ogrn'    => ['nullable', 'string', 'max:15'],
            'kpp'     => ['nullable', 'string', 'max:9'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:255'],
        ], [
            'inn.regex' => 'ИНН должен содержать 10 цифр (юр. лицо) или 12 цифр (ИП).',
        ]);

        $userId = auth()->id();

        if (strlen($validated['inn']) === 10) {
            LegalEntity::updateOrCreate(
                ['user_id' => $userId],
                [
                    'inn'           => $validated['inn'],
                    'full_name'     => $validated['name'] ?: null,
                    'name'          => $validated['short'] ?: null,
                    'ogrn'          => $validated['ogrn'] ?: null,
                    'kpp'           => $validated['kpp'] ?: null,
                    'legal_address' => $validated['address'] ?: null,
                    'phone'         => $validated['phone'] ?: null,
                    'email'         => $validated['email'] ?: null,
                ]
            );
        } else {
            IndividualEntrepreneur::updateOrCreate(
                ['user_id' => $userId],
                [
                    'inn'              => $validated['inn'],
                    'full_name'        => $validated['name'] ?: null,
                    'name'             => $validated['short'] ?: null,
                    'ogrnip'           => $validated['ogrn'] ?: null,
                    'register_address' => $validated['address'] ?: null,
                    'phone'            => $validated['phone'] ?: null,
                    'email'            => $validated['email'] ?: null,
                ]
            );
        }

        return response()->json(['message' => 'Реквизиты сохранены']);
    }

    public function storeBankAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bik'                   => ['required', 'digits:9'],
            'bank'                  => ['required', 'string', 'max:255'],
            'payment_account'       => ['required', 'digits:20', Rule::unique('bank_accounts', 'payment_account')],
            'correspondent_account' => ['required', 'digits:20'],
            'city'                  => ['required', 'string', 'max:255'],
        ], [
            'payment_account.unique' => 'Этот расчётный счёт уже зарегистрирован в системе.',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . config('services.dadata.token'),
                'X-Secret'      => config('services.dadata.secret'),
            ])->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/bank', [
                'query' => $validated['bik'],
            ]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Не удалось проверить БИК: сервис недоступен', 'field' => 'bik'], 422);
        }

        if ($response->failed() || empty($response->json('suggestions'))) {
            return response()->json(['error' => 'Банк с указанным БИК не найден', 'field' => 'bik'], 422);
        }

        $user = auth()->user();

        $account = $user->bankAccounts()->create([
            'bik'                   => $validated['bik'],
            'bank'                  => $validated['bank'],
            'payment_account'       => $validated['payment_account'],
            'correspondent_account' => $validated['correspondent_account'],
            'city'                  => $validated['city'],
            'is_primary'            => $user->bankAccounts()->doesntExist(),
            'sort_order'            => (int) $user->bankAccounts()->max('sort_order') + 1,
        ]);

        return response()->json([
            'message' => 'Банковский счёт добавлен',
            'account' => [
                'id'                    => $account->id,
                'bank'                  => $account->bank,
                'bik'                   => $account->bik,
                'payment_account'       => $account->payment_account,
                'correspondent_account' => $account->correspondent_account,
                'city'                  => $account->city,
                'is_primary'            => $account->is_primary,
            ],
        ]);
    }

    public function reorderBankAccounts(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ])['ids'];

        $userIds = auth()->user()->bankAccounts()->pluck('id')->flip();

        foreach ($ids as $order => $id) {
            if ($userIds->has($id)) {
                BankAccount::where('id', $id)->update([
                    'sort_order' => $order,
                    'is_primary' => $order === 0,
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function destroyBankAccount(BankAccount $bankAccount): JsonResponse
    {
        abort_if($bankAccount->user_id !== auth()->id(), 403);
        $bankAccount->delete();
        return response()->json(['message' => 'Счёт удалён']);
    }

    public function saveProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required'  => 'Введите имя.',
            'email.required' => 'Введите email.',
            'email.unique'   => 'Этот email уже занят другим пользователем.',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'] ? preg_replace('/\D/', '', $validated['phone']) : null,
            'email' => $validated['email'],
        ]);

        return response()->json(['message' => 'Личные данные сохранены']);
    }

    public function saveNotifications(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notify_invoice_from_template' => ['required', 'boolean'],
        ]);

        $user = auth()->user();

        if ($validated['notify_invoice_from_template'] && !$user->email) {
            return response()->json(['error' => 'Сначала укажите email в профиле — иначе отправлять уведомление некуда'], 422);
        }

        $user->update([
            'notify_invoice_from_template' => $validated['notify_invoice_from_template'],
        ]);

        return response()->json(['message' => 'Настройки уведомлений сохранены']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'password'              => ['required', 'string', 'min:5', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'password.min'       => 'Пароль должен содержать не менее 5 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Пароль успешно изменён']);
    }

    public function markAccountDelete(): JsonResponse
    {
        auth()->user()->update(['account' => 'delete']);

        return response()->json(['message' => 'Аккаунт помечен на удаление']);
    }
}
