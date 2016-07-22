<?php

namespace App\Http\Controllers;

use App\FacebookPages;
use App\Setting;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Request;

use App\Http\Requests;

class Conversation extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function index()
    {
//        check if fbAppSec exists

        if (Setting::where('field', 'fbAppSec')->exists()) {
            foreach (Setting::where('field', 'fbAppSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }
        /** @var object $fb */
        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        try {
            $data = $fb->get('me/accounts?fields=id,name,picture,fan_count,category,cover', Data::get('fbAppToken'))->getDecodedBody();

        } catch (FacebookResponseException $r) {
            return $r->getMessage();
        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();
        }

        return view('conversation', compact('data'));
    }

    /**
     * @param $pageId
     * @param $cId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function inbox($pageId, $cId)
    {

        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);
        $me = $pageId;
        $pageToekn = FacebookPages::where('pageId', $pageId)->get();
        foreach ($pageToekn as $t) {
            $token = $t->pageToken;
        }


        try {
            $response = $fb->get($cId . '?fields=participants{id,name,email,picture},messages{message,from,created_time},message_count', $token)->getDecodedBody();

        } catch (FacebookResponseException $rs) {
            return $rs->getMessage();
        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();
        }

        return view('chat', compact('response', 'me'));
    }

    /**
     * @param $pageId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function getConversations($pageId)
    {
        /** @var object $fb */
        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        $pageToekn = FacebookPages::where('pageId', $pageId)->get();
        foreach ($pageToekn as $t) {
            $token = $t->pageToken;
        }


        try {
            $data = $fb->get($pageId . '?fields=id,picture,name,conversations{participants{id,name,picture},message_count,snippet,unread_count,senders}', $token)->getDecodedBody();

        } catch (FacebookSDKException $fsdk) {
            return $fsdk->getMessage() . " [fbc fsdk]";
        } catch (FacebookResponseException $fbr) {
            return $fbr->getMessage() . " [fbc fbr]";
        }

        return view('conpage', compact('data'));


    }

    /**
     * @param $pageId
     * @param $cId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajaxGetConversations($pageId, $cId)
    {
        /** @var object $fb */
        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);
        $me = $pageId;
        $pageToekn = FacebookPages::where('pageId', $pageId)->get();
        foreach ($pageToekn as $t) {
            $token = $t->pageToken;
        }


        try {
            $response = $fb->get($cId . '?fields=participants{id,name,email,picture},messages{message,from,created_time},message_count', $token)->getDecodedBody();
        } catch (FacebookResponseException $rs) {
            return $rs->getMessage();
        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();
        }

        return view('ajaxchat', compact('response', 'me'));

    }

    /**
     * @param Request $re
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function chat(Request $re)
    {
        $conId = $re->conId;
        $pageId = $re->pageId;
        $message = $re->message;
        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        $pageToekn = FacebookPages::where('pageId', $pageId)->get();
        foreach ($pageToekn as $t) {
            $token = $t->pageToken;
        }


        try {

            $fb->post($conId . '/messages', ['message' => $message], $token);
            return "success";

        } catch (FacebookSDKException $fsdk) {
            return $fsdk->getMessage() . " [fbc fsdk]";
        } catch (FacebookResponseException $fbr) {
            return $fbr->getMessage() . " [fbc fbr]";
        }

        return view('conpage', compact('data'));


    }
}
