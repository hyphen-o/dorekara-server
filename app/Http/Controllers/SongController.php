<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    //ユーザの曲を全て取得
    public function getAll($user_id) {
        try {
            $user = User::find($user_id);
            $user_songs = $user->songs()->latest()->get();

            return response()->json([
                $user_songs,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //曲を1つ取得
    public function getOne($id) {
        try {
            $song = Song::find($id);

            return response()->json([
                $song,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //曲を作成　
    public function create($user_id, Request $req) {
        try {
            //トランザクション開始
            DB::beginTransaction();

            if(Song::where([["name", $req->name], ["artist_id", $req->artist_id], ["key", $req->key]])->first()) {
                $song = Song::where([["name", $req->name], ["artist_id", $req->artist_id], ["key", $req->key]])->first();
            } else {
                $song = new Song();
                $song->name = $req->name;
                $song->artist_id = $req->artist_id;
                $song->key = $req->key;
                $song->save();
            }

            $user = User::find($user_id);
            $user->songs()->syncWithoutDetaching($song->id);

            DB::commit();

            return response()->json([
                "success" => [
                    "param" => "create",
                    "msg" => "曲を作成しました",
                ]
            ], 201);
        } catch(Exception $error) {
            DB::rollBack();
            return response($error, 500);
        }
    }

    //曲を編集
    public function edit($user_id, Request $req) {
        try {
            //トランザクション開始
            DB::beginTransaction();

            $user = User::find($req->user_id);
            $user->songs()->detach($req->song_id);

            //既に自身以外に同じ曲名が存在するかの確認
            if(Song::where([["name", $req->name], ["artist_id", $req->artist_id], ["key", $req->key]])->first()) {
                $song = Song::where([["name", $req->name], ["artist_id", $req->artist_id], ["key", $req->key]])->first();
            } else {
                $song = new Song();
                $song->name = $req->name;
                $song->artist_id = $req->artist_id;
                $song->key = $req->key;
                $song->save();
            }

            $user = User::find($user_id);
            $user->songs()->syncWithoutDetaching($song->id);

            DB::commit();

            return response()->json([
                "success" => [
                    "param" => "edit",
                    "msg" => "曲を編集しました",
                ]
            ], 201);
        } catch(Exception $error) {
            DB::rollBack();
            return response($error, 500);
        }
    }

    //曲を削除
    public function destroy(Request $req) {
        try {
            $user = User::find($req->user_id);
            $user->songs()->detach($req->song_id);

            return response()->json([
                "success" => [
                    "param" => "delete",
                    "msg" => "曲を削除しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }
}
