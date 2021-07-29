<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

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
}
