<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class Notify extends Controller
{
    //
    public function insert(Request $re){
        try{
            $notify = new \App\Notify();
            $notify->img = $re->img;
            $notify->title = $re->title;
            $notify->body = $re->body;
            $notify->time = $re->time;
            $notify->url = $re->url;
            $notify->type = $re->type;
            $notify->save();
            return "success";

        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function show(){
        $data = \App\Notify::all();
        return view('notify',compact('data'));
    }

    public function delAll(){
        try {
            \App\Notify::truncate();
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
