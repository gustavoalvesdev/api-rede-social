<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserRelation;
use App\Post;
use Image;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");

        $this->loggedUser = auth()->user();
    }

    /**
     * Updates user information
     *
     * @param Request $request
     * @return array
     */
    public function update(Request $request) : array
    {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $passwordConfirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        // name
        if ($name) {
            $user->name = $name;
        }

        // email
        if ($email) {
            if ($email != $user->email) {
                $emailExists = User::where('email', $email)->count();
                if ($emailExists === 0) {
                    $user->email = $email;
                } else {
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        // birthdate
        if ($birthdate) {
            if (strtotime($birthdate) === false) {
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }

            $user->birthdate = $birthdate;
        }

        // city
        if ($city) {
            $user->city = $city;
        }

        // work
        if ($work) {
            $user->work = $work;
        }

        // password
        if ($password && $passwordConfirm) {
            if ($password === $passwordConfirm) {

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;

            } else {
                $array['error'] = 'As senhas não são iguais';
                return $array;
            }
        }


        $user->save();

        return $array;
    }

    /**
     * Updates user avatar image
     *
     * @param Request $request
     * @return array
     */
    public function updateAvatar(Request $request) : array
    {
        $array = ['error' => ''];

        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('avatar');

        if ($image) {

            if (in_array($image->getClientMimeType(), $allowedTypes)) {

                $fileName = md5(time() . rand(0, 9999)) . '.jpg';

                $destPath = public_path('/media/avatars');

                $img = Image::make($image->path())
                    ->fit(200, 200)
                    ->save($destPath . '/' . $fileName);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $fileName;
                $user->save();

                $array['url'] = url('/media/avatars/' . $fileName);

            } else {
                $array['error'] = 'Arquivo não suportado!';
            }

        } else {
            $array['error'] = 'Arquivo não enviado!';
        }

        return $array;
    }

    /**
     * Updates user cover image
     *
     * @return array
     */
    public function updateCover(Request $request) : array
    {
        $array = ['error' => ''];

        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('cover');

        if ($image) {

            if (in_array($image->getClientMimeType(), $allowedTypes)) {

                $fileName = md5(time() . rand(0, 9999)) . '.jpg';

                $destPath = public_path('/media/covers');

                $img = Image::make($image->path())
                    ->fit(850, 310)
                    ->save($destPath . '/' . $fileName);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $fileName;
                $user->save();

                $array['url'] = url('/media/covers/' . $fileName);

            } else {
                $array['error'] = 'Arquivo não suportado!';
            }

        } else {
            $array['error'] = 'Arquivo não enviado!';
        }

        return $array;
    }

    public function read($id = false) {
        $array = ['error' => ''];

        if ($id) {
            $info = User::find($id);
            if (!$info) {
                $array['error'] = 'Usuário inexistente';
                return $array;
            }
        } else {
            $info = $this->loggedUser;
        }

        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $info['cover'] = url('media/covers/' . $info['cover']);

        $info['me'] = $info['id'] == $this->loggedUser['id'];

        $dateFrom = new \DateTime($info['birthdate']);
        $dateTo = new \DateTime('today');
        $info['age'] = $dateFrom->diff($dateTo)->y;

        $info['followers'] = UserRelation::where('user_to', $info['id'])->count();
        $info['following'] = UserRelation::where('user_from', $info['id'])->count();

        $info['photoCount'] = Post::where('id_user', $info['id'])
        ->where('type', 'photo')
        ->count();

        $hasRelation = UserRelation::where('user_from', $this->loggedUser['id'])
        ->where('user_to', $info['id'])
        ->count();
        $info['isFollowing'] = $hasRelation > 0;

        $array['data'] = $info;

        return $array;
    }

    public function follow($id)
    {
        // POST api/user/123/follow
        $array = ['error' => ''];

        if ($id == $this->loggedUser['id']) {
            $array['error'] = 'Você não pode seguir a si mesmo!';
            return $array;
        }

        $userExists = User::find($id);

        if ($userExists) {

            $relation = UserRelation::where('user_from', $this->loggedUser['id'])
            ->where('user_to', $id)
            ->first();

            if ($relation) {
                // parar de seguir
                $relation->delete();
            } else {
                // Seguir
                $newRelation = new UserRelation();
                $newRelation->user_from = $this->loggedUser['id'];
                $newRelation->user_to = $id;
                $newRelation->save();
            }

        } else {
            $array['error'] = 'Usuário inexistente!';
            return $array;
        }

        return $array;
    }
}
