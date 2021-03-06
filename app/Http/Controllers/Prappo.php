<?php

namespace App\Http\Controllers;

use App\Phones;
use App\Setting;
use App\Tu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;


use Pusher;
use Registration;
use Tumblr\API\Client;
use WhatsProt;


class Prappo extends Controller
{
    /**
     * insert settings information into database
     */
    public static function index()
    {

        if (!(DB::table('settings')->where('field', 'wpUser')->exists())) {
            DB::table('settings')->insert(['field' => 'wpUser']);
        }

        if (!(DB::table('settings')->where('field', 'wpPassword')->exists())) {
            DB::table('settings')->insert(['field' => 'wpPassword']);
        }

        if (!(DB::table('settings')->where('field', 'wpUrl')->exists())) {
            DB::table('settings')->insert(['field' => 'wpUrl']);
        }


        if (!(DB::table('settings')->where('field', 'tuConKey')->exists())) {
            DB::table('settings')->insert(['field' => 'tuConKey']);
        }


        if (!(DB::table('settings')->where('field', 'tuConSec')->exists())) {
            DB::table('settings')->insert(['field' => 'tuConSec']);
        }

        if (!(DB::table('settings')->where('field', 'tuToken')->exists())) {
            DB::table('settings')->insert(['field' => 'tuToken']);
        }

        if (!(DB::table('settings')->where('field', 'tuTokenSec')->exists())) {
            DB::table('settings')->insert(['field' => 'tuTokenSec']);
        }


        if (!(DB::table('settings')->where('field', 'twConKey')->exists())) {
            DB::table('settings')->insert(['field' => 'twConKey']);
        }


        if (!(DB::table('settings')->where('field', 'twConSec')->exists())) {
            DB::table('settings')->insert(['field' => 'twConSec']);
        }


        if (!(DB::table('settings')->where('field', 'twToken')->exists())) {
            DB::table('settings')->insert(['field' => 'twToken']);
        }


        if (!(DB::table('settings')->where('field', 'twTokenSec')->exists())) {
            DB::table('settings')->insert(['field' => 'twTokenSec']);
        }


        if (!(DB::table('settings')->where('field', 'fbAppId')->exists())) {
            DB::table('settings')->insert(['field' => 'fbAppId']);
        }


        if (!(DB::table('settings')->where('field', 'fbAppToken')->exists())) {
            DB::table('settings')->insert(['field' => 'fbAppToken']);
        }


        if (!(DB::table('settings')->where('field', 'fbAppSec')->exists())) {
            DB::table('settings')->insert(['field' => 'fbAppSec']);
        }


        if (!(DB::table('settings')->where('field', 'tuDefBlog')->exists())) {
            DB::table('settings')->insert(['field' => 'tuDefBlog']);
        }

        if (!(DB::table('settings')->where('field', 'twUser')->exists())) {
            DB::table('settings')->insert(['field' => 'twUser']);
        }


        if (!(DB::table('settings')->where('field', 'fbDefPage')->exists())) {
            DB::table('settings')->insert(['field' => 'fbDefPage']);
        }

        if (!(DB::table('settings')->where('field', 'lang')->exists())) {
            DB::table('settings')->insert(['field' => 'lang']);
        }

        if (!(DB::table('settings')->where('field', 'notifyAppId')->exists())) {
            DB::table('settings')->insert(['field' => 'notifyAppId']);
        }

        if (!(DB::table('settings')->where('field', 'notifyAppKey')->exists())) {
            DB::table('settings')->insert(['field' => 'notifyAppKey']);
        }

        if (!(DB::table('settings')->where('field', 'notifyAppSecret')->exists())) {
            DB::table('settings')->insert(['field' => 'notifyAppSecret']);
        }

        if (!(DB::table('settings')->where('field', 'skypeUser')->exists())) {
            DB::table('settings')->insert(['field' => 'skypeUser']);
        }

        if (!(DB::table('settings')->where('field', 'skypePass')->exists())) {
            DB::table('settings')->insert(['field' => 'skypePass']);
        }


    }


    /**
     * @param $string
     * @return array|string
     */
    function extract_email_address($string)
    {
        foreach (preg_split('/\s/', $string) as $token) {
            $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
            if ($email !== false) {
                $emails[] = $email;
            } else {
                $emails = "";
            }
        }
        return $emails;
    }

    /**
     * @param Request $re
     */
    function iup(Request $re)
    {
        $file = $re->file('file');
        $fileType = $file->getClientMimeType();
        if ($fileType == 'image/jpeg' || $fileType == 'image/png') {
            try {
                Input::file('file')->move(public_path() . '/uploads/', "coolboy.png");
                echo "success";
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo "invalid size";
        }


    }


    /**
     * @param $string
     * @return bool|string
     */
    public static function date($string)
    {
        $s = $string;
        $date = strtotime($s);
        return date('d/M/Y', $date);
    }

    /**
     * @param $title
     * @param $content
     */
    public static function notify($title, $content, $url, $type, $time)
    {
        $options = array(
            'encrypted' => true
        );
        $pusher = new Pusher(
            Data::get('notifyAppKey'),
            Data::get('notifyAppSecret'),
            Data::get('notifyAppId'),
            $options
        );

        $data = [
            'message' => $content,
            'title' => $title,
            'url' => $url,
            'type' => $type,
            'time' => $time
        ];
        $pusher->trigger('optimus', 'my_event', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public static function tuCheck()
    {
        if (Setting::where('field', 'tuTokenSec')->exists()) {
            foreach (Setting::where('field', 'tuTokenSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public static function twCheck()
    {
        if (Setting::where('field', 'twTokenSec')->exists()) {
            foreach (Setting::where('field', 'twTokenSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }

    }

    /**
     *
     */
    public static function writeCheck()
    {
        self::index();
    }

    /**
     * @param $link
     * @return mixed
     */
    public static function getSkypeName($link)
    {
        $content = $link;
        $username = str_replace("8:", "", strstr($content, "8:"));
        return $username;

    }

    /**
     * @param $user
     * @return string
     */
    public static function getSkypeImg($user){
        return 'https://api.skype.com/users/' . $user . '/profile/avatar';
    }

    public function test(){
        $blogName = "prappoprince";
        $title = "sometitle";
        $content = "gogogo";
        $pId = "23434";

    }


}
