<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id),],
            'steam_id' => ['nullable', 'string', 'max:32', 'regex:/^\d{17}$/'], // 17 számjegyű Steam ID
            'steam_name' => ['required', 'string', 'max:255'], // steam név
            'discord_tag' => ['nullable', 'string', 'max:32', 'regex:/^.*#\d{4}$/'], // pl. Teszt#1234
            'avatar' => ['nullable', 'image', 'max:2048'], // max 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'steam_id.regex' => 'A Steam ID 17 számjegyből álló számsor kell legyen.',
            'steam_name' => 'A steam név minden esetben kötelező, hogy a statisztikák a versenyek alatt megjelenjenek.',
            'discord_tag.regex' => 'A Discord tag a következő formátumban legyen: Név#1234',
            'avatar.image' => 'A feltöltött fájl nem kép.',
            'avatar.max' => 'A kép nem lehet nagyobb 2MB-nál.',
        ];
    }
}
