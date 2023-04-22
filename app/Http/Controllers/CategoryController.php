<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //ユーザの作成したカテゴリを全て取得
    public function getAll($user_id) {
        try {
            $user_categories = Category::where('user_id', $user_id)->get();

            return response()->json([
                $user_categories,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //カテゴリを1つ取得
    public function getOne($id) {
        try {
            $category = Category::find($id);

            return response()->json([
                $category,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //カテゴリを作成
    public function create($user_id, Request $req) {
        try {
            //既にカテゴリが存在するかの確認
            if(Category::where("name", $req->name)->first()) {
                return response()->json([
                    "error" => [
                        "param" => "name",
                        "msg" => "既にカテゴリが存在します.",
                    ]
                ], 401);
            }

            $category = new Category();
            $category->name = $req->name;
            $category->user_id = $user_id;
            $category->save();

            return response()->json([
                "success" => [
                    "param" => "create",
                    "msg" => " カテゴリを作成しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //カテゴリを削除
    public function destroy($id) {
        try {
            $category = Category::find($id);
            $category->delete();

            return response()->json([
                "success" => [
                    "param" => "delete",
                    "msg" => "カテゴリを削除しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }
}
