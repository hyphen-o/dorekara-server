<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Authorization;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    //ユーザ登録
    public function register(Request $req) {
        try {
            //トランザクション開始
            DB::beginTransaction();

            $user = new User();
            $user->name = $req->name;
            $user->image_url = "default_url.png";

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

            //パスワードの暗号化
            $auth = new Authorization();
            $auth->password = Crypt::encryptString($req->password);
            $auth->user_id = $user->id;

            $auth->save();
            DB::commit();

            $token = auth('api')->login($user);

            return $this->respondWithToken($token, $user);

        } catch (Exception $error) {
            DB::rollBack();
            return response()->json([
                "error" => [
                    "param" => "error",
                    "msg" => "エラーが発生しました.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //ユーザログイン
    public function login(Request $req) {
        try {
            $user = User::where('name', $req->name)->first();

            //ユーザが存在するかの確認
            if(!$user) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "ユーザ名が無効です.",
                    ]
                ], 401);
            }

            //パスワードの復号
            $auth = Authorization::where('user_id', $user->id)->first();
            $decryptedPassword = Crypt::decryptString($auth->password);
            if($req->password != $decryptedPassword) {
                return response()->json([
                    "error" => [
                        "param" => "password",
                        "msg" => "パスワードが無効です.",
                    ]
                ], 401);
            }

            $token = auth('api')->login($user);

            return $this->respondWithToken($token, $user);

        } catch(Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "error",
                    "msg" => "エラーが発生しました.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //トークン,ユーザ情報を整形
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 201);
    }

    //ユーザ削除
    public function destroy($id) {
        try {
            $user = User::find($id);
            if($user) {
                $user->delete();
                return response()->json([
                    "success" => [
                        "param" => "delete",
                        "msg" => "曲を削除しました",
                    ]
                ]);
            }
        } catch(Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "error",
                    "msg" => "エラーが発生しました.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //ユーザイメージ取得
    public function getImage($id) {
        try {
            $imgPath = User::find($id)->image_url;
            return response()->file(Storage::path('public/user_image/' . $imgPath));
        } catch (Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "error",
                    "msg" => "エラーが発生しました.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //ユーザイメージアップロード
    public function uploadImage($id, Request $req) {
        try {
            $upload_file = $req->file('upload_image');
            if($upload_file) {
                $new_imgPath = $upload_file->getClientOriginalName();
            } else {
                return response()->json([
                    "error" => [
                        "param" => "image",
                        "msg" => "画像の形式が無効です.",
                    ]
                ], 401);
            }
            $upload_file->storeAs('public/user_image', $new_imgPath);

            $user = User::find($id);
            $user->image_url = $new_imgPath;
            $user->save();

            return response()->file(Storage::path('public/user_image/' . $new_imgPath));
        } catch (Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "error",
                    "msg" => "エラーが発生しました.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

}
