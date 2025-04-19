<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Implemente sua lógica de autorização aqui, se necessário.
        // Por exemplo, verificar se o usuário autenticado pode atualizar este post específico.
        // $post = $this->route('post'); // Supondo que o nome do parâmetro da rota seja 'post'
        // return $this->user()->can('update', $post);
        return true; // Permitir por enquanto
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Usamos 'sometimes' para permitir atualizações parciais,
        // mas neste caso, a API original parece exigir todos os campos.
        // Ajuste conforme necessário para sua API (PUT vs PATCH).
        // Se for PUT (substituição completa), use 'required'. Se for PATCH, 'sometimes' é mais adequado.
        // O exemplo original usa PUT, então vamos manter 'required' para consistência com ele.
        return [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'required|integer|exists:users,id', // Considere não permitir a alteração do user_id
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
            'user_id.required' => 'O ID do usuário é obrigatório.',
            'user_id.exists' => 'O usuário especificado não existe.',
            'tags.array' => 'As tags devem ser um array.',
            'tags.*.string' => 'Cada tag deve ser um texto.',
            // Adicione outras mensagens personalizadas se necessário
        ];
    }
}