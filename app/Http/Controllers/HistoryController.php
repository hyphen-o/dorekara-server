<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Song;
use Carbon\Carbon;

class HistoryController extends Controller
{
    //指定したユーザが持つ履歴の日時とsession_idを全て取得
    public function getDates($user_id) {
        try {
            $histories = History::where('user_id', $user_id)->latest()->get()->unique('date');
            $dates = $histories->map(function ($history) {
                return [
                    "date" => $history->date,
                ];
            });

            return response()->json([
                $dates,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //指定したsession_idの曲を全て取得
    public function getSongs(Request $req) {
        try {
            $histories = History::where('date', $req->date)->get();
            $songs = $histories->map(function ($history) {
                return Song::find($history->song_id);
            });

            return response()->json([
                $songs,
            ], 201);
        } catch(Exception $error) {
            return response($error, 500);
        }
    }

    //履歴を作成
    public function create($user_id, Request $req) {
        try {

            $history = new History();
            $history->user_id = $user_id;
            $history->song_id = $req->song_id;
            $cb = new Carbon();
            $history->date = $cb->year."年".$cb->month."月".$cb->day."日";
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

    //履歴を削除
    public function destroy(Request $req) {
        try {
            $histories = History::where('date', $req->date)->get();

            foreach($histories as $history) {
                $history->delete();
            }

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
