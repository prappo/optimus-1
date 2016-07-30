<?php

namespace App\Http\Controllers;

use App\Allpost;
use App\FacebookPages;
use App\Fb;
use App\Setting;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FacebookController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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

        $defPage = Data::get('fbDefPage');
        $fbPages = FacebookPages::all();
        $likes = 0;
        $love = 0;
        $sad = 0;
        $haha = 0;
        $wow = 0;
        $angry = 0;
        $totalReactions = 0;

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);
        $getPage = FacebookPages::where('pageId', $defPage)->first();
//        $pageToken = $getPage->pageToken;
        try {

            $response = $fb->get('me/?fields=accounts{access_token,id,name,picture,fan_count,feed.limit(10){id,created_time,message,with_tags,from{id,name,picture},link,comments{id,message,comments,from{id,name,picture},created_time},reactions{type}}}', Data::get('fbAppToken'));
            $body = $response->getBody();
            $data = json_decode($body, true);
            $responseForGroup = $fb->get('me/groups?fields=id,name,owner,picture,privacy', Data::get('fbAppToken'));
            $bodyForGroup = $responseForGroup->getBody();
            $fbGroups = json_decode($bodyForGroup, true);

        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        return view('Facebook', compact('totalReactions', 'sad', 'likes', 'love', 'haha', 'wow', 'angry', 'data', 'fbPages', 'fbGroups'));

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function fbGroupIndex()
    {

        if (Setting::where('field', 'fbAppSec')->exists()) {
            foreach (Setting::where('field', 'fbAppSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }

        $likes = 0;
        $love = 0;
        $sad = 0;
        $haha = 0;
        $wow = 0;
        $angry = 0;
        $totalReactions = 0;
        $token = Data::get('fbAppToken');
        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);
        try {

            $responseForGroup = $fb->get('me/?fields=groups{access_token,id,name,picture,fan_count,feed.limit(10){id,created_time,message,with_tags,from{id,name,picture},link,comments{id,message,comments,from{id,name,picture},created_time},reactions{type}}}', Data::get('fbAppToken'));
            $bodyForGroup = $responseForGroup->getBody();
            $data = json_decode($bodyForGroup, true);


        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        return view('fbgroups', compact('totalReactions', 'sad', 'likes', 'love', 'haha', 'wow', 'angry', 'data', 'token'));
    }

    /**
     * @param Request $re
     * @return string
     * delete post form facebook
     */

    public function fbDelete(Request $re)
    {

        $id = $re->id;
        $token = $re->pageToken;


        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);
        try {
            $msg = json_decode($fb->delete($id, [], $token)->getBody(), true);
            if ($msg['success'] == 1) {
                Fb::where('postId',$id)->delete();
                return "success";
            }

        } catch (FacebookSDKException $e) {
            return $e->getMessage() . "[ fsdk]";
        } catch (FacebookResponseException $r) {
            return $r->getMessage() . " [ fe ]";
        }

    }

    public static function fbDel($id)
    {
        if (Fb::where('postId', $id)->exists()) {
            $fbPostId = Fb::where('postId', $id)->value('fbId');
            $pageId = Fb::where('postId', $id)->value('pageId');

            if ($pageId == "") {
                $token = Data::get('fbAppToken');
            } else {
                $token = Data::getToken($pageId);
            }


            $fb = new \Facebook\Facebook([
                'app_id' => Data::get('fbAppId'),
                'app_secret' => Data::get('fbAppSec'),
                'default_graph_version' => 'v2.6',
            ]);
            try {
                $msg = json_decode($fb->delete($fbPostId, [], $token)->getBody(), true);
                if ($msg['success'] == 1) {
                    Fb::where('postId',$id)->delete();
                    return "Deleted form facebook : success";
                }

            } catch (FacebookSDKException $e) {
                return $e->getMessage() . "[ fsdk]";
            } catch (FacebookResponseException $r) {
                return $r->getMessage() . " [ fe ]";
            }
        }
        else{
//            return "Post couldn't found";
        }


    }

    /**
     * @param Request $re
     * make comment on facebook
     */
    public function fbComment(Request $re)
    {
        $id = $re->id;
        $token = $re->pageToken;
        $message = $re->comment;

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        try {
            $msg = json_decode($fb->post($id . '/comments', ['message' => $message], $token)->getBody(), true);
            if (isset($msg['id'])) {
                echo "Success";
            }
        } catch (FacebookSDKException $fsdk) {
            echo $fsdk->getMessage() . " [fbc fsdk]";
        } catch (FacebookResponseException $fbr) {
            echo $fbr->getMessage() . " [fbc fbr]";
        }

    }

    /**
     * @param Request $re
     * Edit post of facebook
     */
    public function fbEdit(Request $re)
    {
        $id = $re->id;
        $token = $re->pageToken;
        $message = $re->message;

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        try {
            $msg = json_decode($fb->post($id, ['message' => $message], $token), true);
            echo "success";

        } catch (FacebookSDKException $sdke) {
            echo $sdke->getMessage() . " [fbe sdk]";
        } catch (FacebookResponseException $fre) {
            echo $fre->getMessage() . " [fbe fre]";
        }

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function fbReport()
    {
        if (Setting::where('field', 'fbAppSec')->exists()) {
            foreach (Setting::where('field', 'fbAppSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }

        $countryData = array();
        $cityData = array();

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);


        try {
            $response = $fb->get("me/accounts?fields=insights,picture,name,fan_count,cover", Data::get('fbAppToken'));
            $body = $response->getBody();
            $data = json_decode($body, true);

        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        return view('facebookreport', compact('data', 'countryData', 'cityData'));


    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fbReportView()
    {
        $datas = FacebookPages::all();
        return view('facebookreport', compact('datas'));
    }

    /**
     * @param $pageId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function massSend($pageId)
    {
        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        try {
            $response = $fb->get($pageId . '?fields=id,name,picture,category', Data::get('fbAppToken'))->getDecodedBody();
            $name = $response['name'];
            $category = $response['category'];
            $picture = $response['picture']['data']['url'];
        } catch (FacebookResponseException $rs) {
            return $rs->getMessage();
        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();
        }
        return view('masssendform', compact('pageId', 'name', 'category', 'picture'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function massSendIndex()
    {

        if (Setting::where('field', 'fbAppSec')->exists()) {
            foreach (Setting::where('field', 'fbAppSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }


        $fb = new \Facebook\Facebook([
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

        return view('masssend', compact('data'));
    }

    /**
     * @param $pageId
     * @return string
     */
    public function massReplay(Request $re)
    {
        $pageId = $re->pageId;
        $message = $re->message;

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        $pages = FacebookPages::where('pageId', $pageId)->get();
        foreach ($pages as $page) {
            $token = $page->pageToken;
        }
        $conCount = 0;
        $msgCount = 0;

        try {
            $response = $fb->get($pageId . '?fields=conversations', $token)->getDecodedBody();
            foreach ($response['conversations']['data'] as $conNo => $conversation){
                $conId = $conversation['id'];
                try{
                    $fb->post($conId."/messages",['message'=>$message],$token);
                    $msgCount++;
                }
                catch (\Exception $e){

                }
                $conCount++;
            }
        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();
        } catch (FacebookResponseException $rs) {
            return $rs->getMessage();
        }
        echo "Total conversations = " . $conCount . "<br>";
        echo "Total successful sent message = ".$msgCount;
    }

    /**
     * @param Request $re
     * @return string
     */
    public function scraper(Request $re)
    {
//        here echo used for ajax request

        $query = $re->data;
        $type = $re->type;
        $limit = $re->limit;


        $token = Data::get('fbAppToken');

        $fb = new \Facebook\Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);


        try {
            if ($type == 'page') {
                $response = $fb->get('search?q=' . $query . '&type=' . $type . '&fields=id,name,picture,link,phone,website,location,fan_count,about,emails' . '&limit=' . $limit, $token)->getDecodedBody();
                echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>                          
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Website</th>
                                <th>Location</th>
                                <th>Emails</th>
                                <th>Likes</th>
                                <th>About</th>
                            </tr>
                            </thead>
                            <tbody>';
                foreach ($response['data'] as $data) {
                    $id = "";
                    $link = "";
                    $picture = "";
                    $name = "";
                    $phone = "";
                    $website = "";
                    $location = "";
                    $emails = "";
                    $about = "";
                    $likes = "";
                    $lo = "";
                    $em = "";
                    echo '<tr>';
//                    check if all fields are available
                    foreach ($data as $field => $value) {
                        if (isset($data['id'])) {
                            $id = $data['id'];
                        }
                        if (isset($data['picture'])) {
                            $picture = $data['picture'];
                        }

                        if (isset($data['name'])) {
                            $name = $data['name'];
                        }

                        if (isset($data['phone'])) {
                            $phone = $data['phone'];
                        }
                        if (isset($data['website'])) {
                            $website = $data['website'];
                        }

                        if (isset($data['location'])) {
                            $location = $data['location'];
                        }

                        if (isset($data['emails'])) {
                            $emails = $data['emails'];
                        }

                        if (isset($data['about'])) {
                            $about = $data['about'];
                        }
                        if (isset($data['fan_count'])) {
                            $likes = $data['fan_count'];
                        }
                        if (isset($data['link'])) {
                            $link = $data['link'];
                        }
                    }
//                  check data if all are vailable

//                    echo '<td>' . $id . '</td>';
                    echo '<td>' . $picture = isset($picture['data']['url']) ? "<img class='img-thumbnail' src='{$picture['data']['url']}'>" : 'Not found' . '</td>';
                    echo '<td><a target="_blank" href="' . $link . '">' . $name . '</a></td>';
                    echo '<td>' . $phone = ($phone == "") ? "<span class='label label-danger'><i class='fa fa-times badge-danger'></i></span>" : $phone . '</td>';
                    echo '<td>' . $website = (isset($website)) ? $website : 'Not found' . '</td>';
                    if (isset($location['country'])) {
                        foreach ($location as $field => $value) {
                            if ($field == 'latitude' || $field == 'longitude') {

                            } else {
                                $lo .= $value . "<br>";
                            }

                        }
                        if (isset($location['latitude'])) {
                            $lo .= '<a class="btn btn-primary btn-xs" target="_blank" href="http://maps.google.com/?q=' . $location['latitude'] . ',' . $location['longitude'] . '">Show Map</a>';
                        }
                        echo '<td>' . $lo . '</td>';
                    } else {
                        echo '<td>' . "<span class='label label-danger'><i class='fa fa-times badge-danger'></i></span>" . '</td>';
                    }
                    if (is_array($emails)) {
                        foreach ($emails as $email) {
                            $em .= $email;
                        }
                        echo '<td>' . $em . '</td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }
                    echo '<td>' . $likes . '</td>';
                    echo '<td>' . $about . '</td>';

                    echo '</tr>';
                }
                echo '</tbody><tfoot>
                            <tr> 
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Website</th>
                                <th>Location</th>
                                <th>Emails</th>
                                <th>Likes</th>
                                <th>About</th>
                            </tr>
                            </tfoot>
                        </table>';
//                print_r($response);
            } elseif ($type == 'user') {
                $response = $fb->get('search?q=' . $query . '&type=' . $type . '&fields=id,name,picture,link,age_range,gender' . '&limit=' . $limit, $token)->getDecodedBody();
                echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Age range</th>
                                <th>Gender</th>
                                <th>Profile</th>
                            </tr>
                            </thead>

                            <tbody>';

                foreach ($response['data'] as $data) {
                    $id = "";
                    $link = "";
                    $picture = "";
                    $name = "";
                    $age_range = "";
                    $gender = "";

                    echo '<tr>';
//                    check if all fields are available
                    foreach ($data as $field => $value) {
                        if (isset($data['id'])) {
                            $id = $data['id'];
                        }
                        if (isset($data['picture'])) {
                            $picture = $data['picture'];
                        }

                        if (isset($data['name'])) {
                            $name = $data['name'];
                        }
                        if (isset($data['link'])) {
                            $link = $data['link'];
                        }
                        if (isset($data['age_range'])) {
                            $age_range = $data['age_range'];
                        }
                        if (isset($data['gender'])) {
                            $gender = $data['gender'];
                        }
                    }
//                  check data if all are vailable
                    echo '<td>' . $id . '</td>';
                    echo '<td>' . $picture = isset($picture['data']['url']) ? "<img src='{$picture['data']['url']}'>" : 'Not found' . '</td>';
                    echo '<td>' . $name . '</td>';
                    echo '<td>' . $age_range . '</td>';
                    echo '<td>' . $gender . '</td>';
                    echo '<td><a target="_blank" href="' . $link . '">Profile</a></td>';
//                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    echo '</tr>';
                }
                echo '</tbody>
                            <tfoot>
                            <tr>   
                                <th>ID</th>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Age range</th>
                                <th>Gender</th>
                                <th>Profile</th>
                            </tr>
                            </tfoot>
                        </table>';
            } elseif ($type == "event") {
                $response = $fb->get('search?q=' . $query . '&type=' . $type . '&fields=id,picture,name,place,attending_count,interested_count,noreply_count,declined_count,start_time,end_time,description,link,owner{name,link,picture}' . '&limit=' . $limit, $token)->getDecodedBody();
                echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                
                                <th>Name</th>
                                <th>Place</th>
                                <th>End time</th>
                                <th>Description</th>
                                <th>Owner</th>
                                <th>More info</th>
                                
                            </tr>
                            </thead>

                            <tbody>';

                foreach ($response['data'] as $data) {
                    $id = "";
                    $link = "";
                    $name = "";
                    $location = "";
                    $end_time = "";
                    $description = "";
                    $lo = "";

                    echo '<tr>';
//                    check if all fields are available
                    foreach ($data as $field => $value) {
                        if (isset($data['id'])) {
                            $id = $data['id'];
                        }
                        if (isset($data['name'])) {
                            $name = $data['name'];
                        }

                        if (isset($data['place'])) {
                            $location = $data['place'];
                        }
                        if (isset($data['end_time'])) {
                            $end_time = $data['end_time'];
                        }

                        if (isset($data['description'])) {
                            $description = $data['description'];
                        }
                        if (isset($data['id'])) {
                            $link = $data['id'];
                        }


                    }
//                  check data if all are vailable

                    echo '<td><img class="img-thumbnail" src="' . $data['picture']['data']['url'] . '"><br><a target="_blank" href="https://facebook.com/' . $link . '">' . $name . '</a></td>';
                    if (isset($location['location']['country'])) {
                        foreach ($location['location'] as $field => $value) {
                            if ($field == 'latitude' || $field == 'longitude') {

                            } else {
                                $lo .= $value . "<br>";
                            }

                        }
                        if (isset($location['location']['latitude'])) {
                            $lo .= '<a class="btn btn-primary btn-xs" target="_blank" href="http://maps.google.com/?q=' . $location['location']['latitude'] . ',' . $location['location']['longitude'] . '">Show Map</a>';
                        }
                        echo '<td>' . $lo . '</td>';
                    } else {
                        echo '<td>' . "<span class='label label-danger'><i class='fa fa-times badge-danger'></i></span>" . '</td>';
                    }
                    echo '<td>' . Prappo::date($end_time) . '</td>';
                    echo '<td>' . $description . '</td>';
                    echo '<td><img class="img-circle" src="' . $data['owner']['picture']['data']['url'] . '"><br><a target="_blank" href="' . $data['owner']['link'] . '">' . $data['owner']['name'] . '</a></td>';
                    echo '<td>' .
                        '<span class="text-green">Attending ' . $data['attending_count'] . '</span><br>' .
                        '<span class="text-blue">Interested ' . $data['interested_count'] . '</span><br>' .
                        '<span class="text-yellow">Noreply ' . $data['noreply_count'] . '</span><br>' .
                        '<span class="text-red">Declined ' . $data['declined_count'] . '</span><br>' .
                        '</td>';
//                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    echo '</tr>';
                }
                echo '</tbody>
                            <tfoot>
                            <tr>   
                                
                                <th>Name</th>
                                <th>Place</th>
                                <th>End time</th>
                                <th>Description</th>
                                <th>Owner</th>
                                <th>More info</th>
                            </tr>
                            </tfoot>
                        </table>';
            } elseif ($type == 'group') {
                $response = $fb->get('search?q=' . $query . '&type=' . $type . '&fields=id,name,privacy,link,picture,description,owner{id,name,picture,link}' . '&limit=' . $limit, $token)->getDecodedBody();
                echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Privacy</th>
                                <th>Description</th>
                                <th>Owner</th>
                                <th>Link</th>
                            </tr>
                            </thead>

                            <tbody>';

                foreach ($response['data'] as $data) {
                    $id = "";
                    $link = "";
                    $name = "";
                    $privacy = "";

                    echo '<tr>';
//                    check if all fields are available
                    foreach ($data as $field => $value) {
                        if (isset($data['id'])) {
                            $id = $data['id'];
                        }
                        if (isset($data['name'])) {
                            $name = $data['name'];
                        }

                        if (isset($data['id'])) {
                            $link = $data['id'];
                        }
                        if (isset($data['privacy'])) {
                            $privacy = $data['privacy'];
                        }
                        if (isset($data['owner'])) {
                            $owner_id = $data['owner']['id'];
                            $owner_name = $data['owner']['name'];
                            $owner_picture = $data['owner']['picture']['data']['url'];
                        }


                    }
//                  check data if all are vailable
                    echo '<td>' . $id . '</td>';
                    echo '<td><img class="img-thumbnail" src="' . $data['picture']['data']['url'] . '"><br><a target="_blank" href="https://facebook.com/' . $link . '">' . $name . '</a></td>';
                    echo '<td>' . $privacy . '</td>';
                    if (isset($data['description'])) {
                        echo '<td>' . $data['description'] . '</td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }
                    if (isset($data['owner'])) {
                        echo '<td><img src="' . $data['owner']['picture']['data']['url'] . '"><br>' . '<a target="_blank" href="' . $data['owner']['link'] . '">' . $data['owner']['name'] . '</a></td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }
                    echo '<td><a target="_blank" href="https://facebook.com/' . $data['id'] . '">Link</a>';

                    echo '</tr>';
                }
                echo '</tbody>
                            <tfoot>
                            <tr>   
                                <th>ID</th>
                                <th>Name</th>
                                <th>Privacy</th>
                                <th>Description</th>
                                <th>Owner</th>
                                <th>Link</th>
                            </tr>
                            </tfoot>
                        </table>';
            } elseif ($type == 'place') {

                $response = $fb->get("search?q=" . $query . "&type=" . $type . "&fields=id,name,category,picture,location,link,website,phone,description,about" . "&limit=" . $limit, $token)->getDecodedBody();
                echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>                          
                                <th>Name</th>
                                <th>Category</th>
                                <th>Phone</th>
                                <th>Website</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>About</th>
                            </tr>
                            </thead>
                            <tbody>';
                foreach ($response['data'] as $data) {
                    $id = "";
                    $link = "";
                    $picture = "";
                    $name = "";
                    $phone = "";
                    $website = "";
                    $location = "";
                    $about = "";
                    $lo = "";
                    $em = "";
                    $description = "";
                    echo '<tr>';
//                    check if all fields are available
                    foreach ($data as $field => $value) {
                        if (isset($data['id'])) {
                            $id = $data['id'];
                        }
                        if (isset($data['picture'])) {
                            $picture = $data['picture']['data']['url'];
                        }

                        if (isset($data['name'])) {
                            $name = $data['name'];
                        }

                        if (isset($data['phone'])) {
                            $phone = $data['phone'];
                        }
                        if (isset($data['website'])) {
                            $website = $data['website'];
                        }

                        if (isset($data['location'])) {
                            $location = $data['location'];
                        }
                        if (isset($data['link'])) {
                            $link = $data['link'];
                        }

                    }
//                  check data if all are vailable

                    echo '<td><img class="img-thumbnail" src="' . $picture . '"><br>' . '<a target="_blank" href="' . $link . '">' . $name . '</a></td>';
                    echo '<td>' . $data['category'] . '</td>';
                    if ($phone != "") {
                        echo '<td>' . $phone . '</td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }
                    echo '<td>' . $website . '</td>';
                    if (isset($location['country'])) {
                        foreach ($location as $field => $value) {
                            if ($field == 'latitude' || $field == 'longitude') {

                            } else {
                                $lo .= $value . "<br>";
                            }

                        }
                        if (isset($location['latitude'])) {
                            $lo .= '<a class="btn btn-primary btn-xs" target="_blank" href="http://maps.google.com/?q=' . $location['latitude'] . ',' . $location['longitude'] . '">Show Map</a>';
                        }
                        echo '<td>' . $lo . '</td>';
                    } else {
                        echo '<td>' . "<span class='label label-danger'><i class='fa fa-times badge-danger'></i></span>" . '</td>';
                    }
                    if (isset($data['description'])) {
                        echo '<td>' . $data['description'] . '</td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }

                    if (isset($data['about'])) {
                        echo '<td>' . $data['about'] . '</td>';
                    } else {
                        echo '<td> <span class=\'label label-danger\'><i class=\'fa fa-times badge-danger\'></i></span> </td>';
                    }


//
                    echo '</tr>';
                }
                echo '</tbody><tfoot>
                            <tr> 
                                <th>Name</th>
                                <th>Category</th>
                                <th>Phone</th>
                                <th>Website</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>About</th>
                            </tr>
                            </tfoot>
                        </table>';
            }


        } catch (FacebookSDKException $sdk) {
            return $sdk->getMessage();

        } catch
        (FacebookResponseException $rs) {
            return $rs->getMessage();
        }
    }


}
