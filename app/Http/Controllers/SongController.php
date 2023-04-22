<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    //ユーザの曲を全て取得
    public function getAll($user_id) {
        try {
            $user_songs = Song::where('user_id', $user_id)->latest()->get();

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
            //既に曲名が存在するかの確認
            if(Song::where("name", $req->name)->first()) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "既に曲が存在します.",
                    ]
                ], 401);
            }

            //トランザクション開始
            DB::beginTransaction();

            $song = new Song();
            $song->name = $req->name;
            $song->key = $req->key;
            $song->user_id = $user_id;

            //新たにアーティストを作成する場合，artistテーブルにデータを追加する
            if($req->new_artistName) {
                $artist = new Artist();
                $artist->name = $req->new_artistName;
                $artist->user_id = $user_id;
                $artist->save();
                $song->artist_id = $artist->id;
            } else {
                $song->artist_id = $req->artist_id;
            }

            //新たにカテゴリを作成する場合，categoryテーブルにデータを追加する
            if($req->new_categoryName) {
                $category = new Category();
                $category->name = $req->new_categoryName;
                $category->user_id = $user_id;
                $category->save();
                $song->category_id = $category->id;
            } else {
                $song->category_id = $req->category_id;
            }

            $song->save();

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
    public function edit($id, Request $req) {
        try {
            //既に自身以外に同じ曲名が存在するかの確認
            if(Song::where([["id", '!=', $id],["name", $req->name]])->first()) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "既に曲が存在します.",
                    ]
                ], 401);
            }

            //トランザクション開始
            DB::beginTransaction();

            $song = Song::find($id);
            $song->name = $req->name;
            $song->key = $req->key;

            //新たにアーティストを作成する場合，artistテーブルにデータを追加する
            if($req->new_artistName) {
                $artist = new Artist();
                $artist->name = $req->new_artistName;
                $artist->user_id = $song->user_id;
                $artist->save();
                $song->artist_id = $artist->id;
            } else {
                $song->artist_id = $req->artist_id;
            }

            //新たにカテゴリを作成する場合，categoryテーブルにデータを追加する
            if($req->new_categoryName) {
                $category = new Category();
                $category->name = $req->new_categoryName;
                $category->user_id = $song->user_id;
                $category->save();
                $song->category_id = $category->id;
            } else {
                $song->category_id = $req->category_id;
            }

            $song->update();

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
    public function destroy($id) {
        try {
            $song = Song::find($id);
            $song->delete();

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
