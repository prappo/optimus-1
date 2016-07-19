<?php

namespace App\Http\Controllers;

use App\OptSchedul;
use Illuminate\Http\Request;

use App\Http\Requests;

class ScheduleController extends Controller
{
    public function index()
    {
        $data = OptSchedul::all();
        return view('schedule', compact('data'));
    }

    public function addSchedule(Request $re)
    {

        $title = $re->title;
        $caption = $re->caption;
        $link = $re->link;
        $image = $re->image;
        $description = $re->description;
        $status = $re->status;
        $postId = $re->postId;
        $type = $re->type;
        $fb = $re->fb;
        $fbg = $re->fbg;
        $tw = $re->tw;
        $tu = $re->tu;
        $pageId = $re->pageId;
        $pageToken = $re->pageToken;
        $blogName = $re->blogName;
        $groupId = $re->groupId;
        $wp = $re->wp;
        $imagetype = $re->imagetype;
        $sharetype = $re->sharetype;

        if ($status == "") {
            return "Please write something";
        }
        $schedule = new OptSchedul();
        try {
            $schedule->title = $title;
            $schedule->caption = $caption;
            $schedule->link = $link;
            $schedule->image = $image;
            $schedule->description = $description;
            $schedule->content = $status;
            $schedule->postId = $postId;
            $schedule->type = $type;
            $schedule->fb = $fb;
            $schedule->tw = $tw;
            $schedule->tu = $tu;
            $schedule->fbg = $fbg;
            $schedule->wp = $wp;
            $schedule->groupId = $groupId;
            $schedule->pageId = $pageId;
            $schedule->pageToken = $pageToken;
            $schedule->blogName = $blogName;
            $schedule->imagetype = $imagetype;
            $schedule->sharetype = $sharetype;
            $schedule->save();
            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function sdel(Request $re)
    {

        $id = $re->id;

        try {
            OptSchedul::where('id', $id)->delete();
            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function sedit(Request $re){
        $id = $re->id;
        $title = $re->title;
        $content = $re->data;
        $type = $re->type;
        try{
            OptSchedul::where('id',$id)->update(['title'=>$title,'content'=>$content,'type'=>$type]);
            return "success";

        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
