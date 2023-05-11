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
        $user = new User();
        $user->name = $req->name;
        $user->image_url = "default_url.png";

        //既にユーザがいるかの確認
        if($this->isExistUser($req->name)) {
            return response()->json([
                "error" => [
                    "param" => "name",
                    "msg" => "既にユーザが存在します.",
                ]
            ], 401);
        }

        //トランザクション開始
        DB::beginTransaction();

        try {

            $user->save();

            //パスワードの暗号化
            $auth = new Authorization();
            $auth->password = $this->encryptPassword($req->password);
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
            if(!$this->isRegisteredUser($req)) {
                return response()->json([
                    "error" => [
                        "param" => "login",
                        "msg" => "ユーザ名，またはパスワードが無効です．",
                    ]
                ], 401);
            }

            $user = $this->getUser($req->name);
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

    //ユーザログアウト
    public function logout() {
        try {
            //JWTの無効化
            $user = JWTAuth::parseToken()->invalidate();
            return response()->json([
                "success" => [
                    "param" => "logout",
                    "msg" => "ログアウトしました",
                ]
            ]);
        } catch (Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "logout-error",
                    "msg" => "ログアウト中にエラーが発生しました．システム管理者にお問い合わせください．",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    public function me() {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json(compact('user'));
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

    //ユーザ削除
    public function destroy(Request $req) {
        try {
            $user = User::find($req->user_id);
            if($user) {
                $user->delete();
                return response()->json([
                    "success" => [
                        "param" => "delete",
                        "msg" => "ユーザを削除しました",
                    ]
                ]);
            }
        } catch(Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "delete-user-error",
                    "msg" => "ユーザを削除することができませんでした.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //ユーザイメージ取得
    public function getImage($id) {
        try {
            $path = User::find($id)->image_url;

            return $path;
        } catch (Exception $error) {
            return response()->json([
                "error" => [
                    "param" => "get-userImage-error",
                    "msg" => "ユーザのイメージを取得できませんでした.",
                    "body" => $error,
                ]
            ], 500);
        }
    }

    //ユーザイメージアップロード
    public function uploadImage($id, Request $req) {
        try {
            $upload_file = $req->file('upload_image');
            $path = Storage::disk('s3')->putFile('user-image', $upload_file);

            $user = User::find($id);
            $user->image_url = Storage::disk('s3')->url($path);
            $user->save();

            return Storage::disk('s3')->url($path);
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

    //トークン,ユーザ情報を整形
    protected function respondWithToken($token, $user) {
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 201);
    }

    //ユーザがいるかどうかの確認
    protected function isExistUser($name) {
        $user = User::where("name", $name)->first();
        if($user) return true;
        else return false;
    }

    //パスワードの暗号化
    protected function encryptPassword($password) {
        return Crypt::encryptString($password.env('SECRET_SOLT', false));
    }

    //パスワードの復号
    protected function decryptPassword($password) {
        return Crypt::decryptString($password);
    }

    //ユーザを取得
    protected function getUser($name) {
        return User::where("name", $name)->first();
    }

    //登録されたユーザかどうかを確認
    protected function isRegisteredUser($req) {
        //ユーザが存在するかの確認
        if(!$this->isExistUser($req->name)) return false;

        $user = $this->getUser($req->name);

        //パスワードの復号
        $auth = Authorization::where('user_id', $user->id)->first();
        $decryptedPassword = $this->decryptPassword($auth->password);

        if($req->password.env('SECRET_SOLT', false) != $decryptedPassword) return false;

        return true;
    }

}
