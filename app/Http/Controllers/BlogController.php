<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Post;
use App\Models\Tag;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Parâmetros de paginação
            $perPage = $request->input('per_page', 10);
            
            // Consulta paginada dos posts com eager loading das tags e do usuário
            $posts = Post::with(['tags', 'user']) // Carrega tanto as tags quanto o usuário
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            // Verifica se existem posts
            if ($posts->isEmpty()) {
                return view('home', [
                    'posts' => $posts,
                    'message' => 'Nenhum post encontrado.'
                ]);
            }

            $tags = Tag::all();

            return view('home', compact('posts', 'tags'));
        } catch (\Exception $e) {
            // Log do erro para debug
            \Log::error('Erro ao buscar posts: ' . $e->getMessage());
            
            // Retorna a view com mensagem de erro
            return view('home', [
                'posts' => collect(),
                'error' => 'Ocorreu um erro ao carregar os posts. Por favor, tente novamente mais tarde.'
            ]);
        }
    }
}