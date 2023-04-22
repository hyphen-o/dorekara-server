<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
    //ユーザの作成したアーティストを全て取得
    public function getAll($user_id) {
        try {

            $user = User::find($user_id);
            $user_artists = $user->artists()->latest()->get();

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
            //トランザクション開始
            DB::beginTransaction();

            //既にアーティストが存在するかの確認
            if(Artist::where("name", $req->name)->first()) {
                $artist = Artist::where("name", $req->name)->first();
            } else {
                $artist = new Artist();
                $artist->name = $req->name;
                $artist->save();
            }

            $user = User::find($user_id);
            $user->artists()->syncWithoutDetaching($artist->id);
            $user->save();

            DB::commit();

            return response()->json([
                "success" => [
                    "param" => "create",
                    "msg" => " アーティストを作成しました",
                ]
            ], 201);
        } catch(Exception $error) {
            DB::rollBack();
            return response($error, 500);
        }
    }

    //アーティストを削除
    public function destroy(Request $req) {
        try {
            $user = User::find($req->user_id);
            $user->artists()->detach($req->artist_id);

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
