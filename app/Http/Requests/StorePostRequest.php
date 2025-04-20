<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Implemente sua lógica de autorização aqui, se necessário.
        // Por exemplo, verificar se o usuário autenticado pode criar posts.
        // return $this->user()->can('create', Post::class);
        return true; // Permitir por enquanto
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'tags'    => 'sometimes|array',
            'tags.*'  => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O campo título é obrigatório.',
            'content.required' => 'O campo conteúdo é obrigatório.',
            'tags.array' => 'As tags devem ser um array.',
            'tags.*.string' => 'Cada tag deve ser um texto.',
            // Adicione outras mensagens personalizadas se necessário
        ];
    }
}