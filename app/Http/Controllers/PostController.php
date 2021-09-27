<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\PostLike;
use App\PostComment;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");

        $this->loggedUser = auth()->user();
    }

    public function like($id)
    {
        $array = ['error' => ''];

        // 1. Verificar se o post existe
        if (Post::find($id)) {
            // 2. Verificar se eu já dei like no post
            $isLiked = PostLike::where('id_post', $id)
            ->where('id_user', $this->loggedUser['id'])
            ->count();

            if ($isLiked > 0) {
                // 2.1 Se sim, remover
                $pl = PostLike::where('id_post', $id)
                ->where('id_user', $this->loggedUser['id'])
                ->first();

                $pl->delete();

                $array['isLiked'] = false;
            } else {
                // 2.2 Se não, adicionar
                $newPostLike = new PostLike();
                $newPostLike->id_post = $id;
                $newPostLike->id_user = $this->loggedUser['id'];
                $newPostLike->created_at = date('Y-m-d H:i:s');
                $newPostLike->save();

                $array['isLiked'] = true;
            }

            $array['likeCount'] = PostLike::where('id_post', $id)->count();


        } else {
            $array['error'] = 'Post não existe!';
            return $array;
        }

        return $array;
    }

    public function comment(Request $request, $id)
    {
        $array = ['error' => ''];

        $txt = $request->input('txt');

        if (Post::find($id)) {
            if ($txt) {

                $newComment = new PostComment();
                $newComment->id_post = $id;
                $newComment->id_user = $this->loggedUser['id'];
                $newComment->created_at = date('Y-m-d H:i:s');
                $newComment->body = $txt;
                $newComment->save();

            } else {
                $array['error'] = 'Não enviou mensagem';
                return $array;
            }
        } else {
            $array['error'] = 'Post não existe!';
            return $array;
        }

        return $array;
    }


}
