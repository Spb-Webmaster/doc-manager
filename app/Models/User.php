<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'phone', 'email', 'password', 'account', 'notify_invoice_from_template'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_invoice_from_template' => 'boolean',
        ];
    }

    public function legalEntity(): HasOne
    {
        return $this->hasOne(LegalEntity::class);
    }

    public function individualEntrepreneur(): HasOne
    {
        return $this->hasOne(IndividualEntrepreneur::class);
    }

    public function selfEmployed(): HasOne
    {
        return $this->hasOne(SelfEmployed::class);
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function smartInvoices(): HasMany
    {
        return $this->hasMany(SmartInvoice::class);
    }
}
