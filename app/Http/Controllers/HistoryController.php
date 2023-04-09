<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Song;

class HistoryController extends Controller
{
    public function getAll() {
        try {

        } catch(Exception $error) {

        }
    }

    public function getOne($session_id, Request $req) {
        try {
            // $histories = History::where('session_id', $session_id)->latest()->get();
            // foreach($histories as $history) {

            // }
        } catch(Exception $error) {

        }
    }

    public function create($user_id, Request $req) {
        try {
            $history = new History();
            $history->user_id = $user_id;
            $history->song_id = $req->song_id;
            $history->session_id = $req->session_id;
            $history->save();

            return response()->json([
                "success" => [
                    "param" => "create",
                    "msg" => "履歴を作成しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    public function destroy($session_id, Request $req) {
        try {
            $histories = History::where('session_id', $session_id)->get();
            $histories->delete();

            return response()->json([
                "success" => [
                    "param" => "delete",
                    "msg" => "履歴を削除しました",
                ]
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }
}
