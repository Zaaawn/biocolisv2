<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Ce fichier remplace app/Http/Requests/ProfileUpdateRequest.php
 * La validation est désormais gérée directement dans ProfileController::update()
 * mais on garde ce fichier pour compatibilité si besoin.
 */
class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prenom'    => ['required', 'string', 'max:100'],
            'nom'       => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', Rule::unique('users')->ignore($this->user()->id)],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['required', 'string', 'max:255'],
        ];
    }
}
