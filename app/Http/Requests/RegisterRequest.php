<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // autoriser toutes les requêtes ici
    }

    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:public,organisateur',
            'nom_organis' => 'required_if:role,organisateur',
            'interet' => 'array|required_if:role,public',
            'interet.*' => 'exists:interets,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Le nom complet est requis.',
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'L’adresse e-mail doit être valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.required' => 'Le mot de passe est requis.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit comporter au moins 6 caractères.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné est invalide.',
            'nom_organis.required_if' => 'Le nom de l’organisateur est requis pour les organisateurs.',
            'interet.required_if' => 'Les centres d’intérêt sont requis pour les utilisateurs publics.',
            'interet.*.exists' => 'Un ou plusieurs centres d’intérêt sélectionnés sont invalides.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}