<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function register(Request $req) {
        try {
            $user = new User();
            $user->name = $req->name;
            //パスワードの暗号化
            $user->password = Crypt::encryptString($req->password);
            $user->image_url = "default_url";

            //既にユーザがいるかの確認
            if(User::where("name", $req->name)->first()) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "既にユーザが存在します.",
                    ]
                ], 401);
            }

            $user->save();

            return response()->json([
                "name" => $user->name,
                "id" => $user->id,
                "image_url" => $user->image_url,
            ], 201);

        } catch (Exception $error) {
            return response($error, 500);
        }
    }

    public function login(Request $req) {
        try {
            $user = User::where('name', $req->name)->first();

            if(!$user) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "ユーザ名が無効です.",
                    ]
                ], 401);
            }

            //パスワードの復号
            $decryptedPassword = Crypt::decryptString($user->password);
            if($req->password != $decryptedPassword) {
                return response()->json([
                    "error" => [
                        "param" => "password",
                        "msg" => "パスワードが無効です.",
                    ]
                ], 401);
            }

            return response()->json([
                "name" => $user->name,
                "id" => $user->id,
                "image_url" => $user->image_url,
            ], 201);

        } catch(Exception $error) {
            return response($error, 500);
        }
    }

}
