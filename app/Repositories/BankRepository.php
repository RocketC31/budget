<?php

namespace App\Repositories;

class BankRepository
{
    public function getValidationRules(): array
    {
        return [
            'bank.id' => 'required|max:255',
            'bank.logo' => 'required|max:255',
            'bank.name' => 'required|max:255'
        ];
    }
}
