<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Tumblr\API\Client;

class Tumblr extends Controller
{
    public function index(){
        if(Setting::where('field','tuTokenSec')->exists()){
            foreach (Setting::where('field','tuTokenSec')->get() as $d){
                if($d->value == ""){
                    return redirect('/settings');
                }
            }
        }
        else{
            return redirect('/settings');
        }

        $blogName = self::get_value('tuDefBlog');
        $consumerKey = self::get_value('tuConKey');
        $consumerSecret = self::get_value('tuConSec');
        $token = self::get_value('tuToken');
        $tokenSecret = self::get_value('tuTokenSec');

        $client = new Client($consumerKey, $consumerSecret, $token, $tokenSecret);
        try {

            $post = $client->getBlogPosts($blogName);

            $avatar = $client->getBlogAvatar($blogName);
            $blog = $post->blog->title;
            $description = $post->blog->description;
            $followers = $post->blog->followers;
            $totalPosts = $post->blog->posts;
            $subscribed = $post->blog->subscribed;
            $admin = $post->blog->admin;
            $type = $post->blog->type;
            $message = $post->blog->messages;
            $ask = $post->blog->ask;
            $dashboard = $client->getDashboardPosts();

            
//            print_r($dashboard);
//            exit;
//            print_r($post);
//            exit;


        } catch (\Exception $e) {
            echo "error";
        }

        return view('Tumblr',compact('avatar','post','blog','followers','description','totalPosts','subscribed','admin','type','message','ask','dashboard'));
    }

    public static function get_value($field)
    {
        return DB::table('settings')->where('field', $field)->value('value');
    }
}
