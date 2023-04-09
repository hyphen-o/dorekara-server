<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    //ユーザの作成したアーティストを全て取得
    public function getAll($user_id) {
        try {
            $user_artists = Artist::where('user_id', $user_id)->get();

            return response()->json([
                $user_artists,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //アーティストを1つ取得
    public function getOne($id) {
        try {
            $artist = Artist::find($id);

            return response()->json([
                $artist,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //アーティストを作成
    public function create($user_id, Request $req) {
        try {
            //既にアーティストが存在するかの確認
            if(Artist::where("name", $req->name)->first()) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "既にアーティストが存在します.",
                    ]
                ], 401);
            }

            $artist = new Artist();
            $artist->name = $req->name;
            $artist->user_id = $user_id;
            $artist->save();

            return response()->json([
                "success" => [
                    "param" => "create",
                    "msg" => " アーティストを作成しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //アーティストを削除
    public function destroy($id) {
        try {
            $artist = Artist::find($id);
            $artist->delete();

            return response()->json([
                "success" => [
                    "param" => "delete",
                    "msg" => "アーティストを削除しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }
}
