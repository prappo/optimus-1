<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;

class Scraper extends Controller
{
    //
    public function index()
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

        return view('scraper');
    }

    public function twScraper()
    {
        if(Setting::where('field','twTokenSec')->exists()){
            foreach (Setting::where('field','twTokenSec')->get() as $d){
                if($d->value == ""){
                    return redirect('/settings');
                }
            }
        }
        else{
            return redirect('/settings');
        }


        if (Setting::where('field', 'twTokenSec')->exists()) {
            foreach (Setting::where('field', 'twTokenSec')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }
//
//        $consumerKey = Followers::get_value('twConKey');
//        $consumerSecret = Followers::get_value('twConSec');
//        $accessToken = Followers::get_value('twToken');
//        $tokenSecret = Followers::get_value('twTokenSec');
//
//        $query = "jhon";
//        $limit = 5;
//
//
//
//        $twitter = new \Twitter($consumerKey, $consumerSecret, $accessToken, $tokenSecret);
//        $datas = $twitter->request('users/search', 'GET', array('q' => $query, 'count' => $limit));
////        print_r($datas);
//

        return view('twscraper');


    }

    public function twitterScrapper(Request $re)
    {
        $consumerKey = Followers::get_value('twConKey');
        $consumerSecret = Followers::get_value('twConSec');
        $accessToken = Followers::get_value('twToken');
        $tokenSecret = Followers::get_value('twTokenSec');

        $query = $re->data;
        $limit = $re->limit;
        $type = $re->type;


        $twitter = new \Twitter($consumerKey, $consumerSecret, $accessToken, $tokenSecret);

//        $me = $twitter->load(\Twitter::ME);
//        $twRep = $twitter->load(\Twitter::REPLIES);
//        $tw = $twitter->load(\Twitter::ME_AND_FRIENDS);
        if ($type == 'tweets') {
            $datas = $twitter->request('search/tweets', 'GET', array('q' => $query, 'count' => $limit));

            echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>                          
                                <th>ID</th>
                                <th>Tweets</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Tweet link</th>
                                <th>Tweet details</th>
                                
                            </tr>
                            </thead>
                            <tbody>';
            foreach ($datas->statuses as $no => $data) {
//

                echo "<tr>";
                echo "<td>{$data->id}</td>";
                echo "<td>{$data->text}</td>";
                echo "<td>".Prappo::date($data->created_at)."</td>";
                echo "<td>".
                    '<img class="img-circle" src="' . $data->user->profile_image_url . '"><br>'.
                    "<a target='_blank' href='https://twitter.com/".$data->user->screen_name."'>{$data->user->name}</a>"
                    ."</td>";
                echo "<td><a target='_blank' href='https://twitter.com/".$data->user->screen_name."/status/".$data->id."'>Click here</a></td>";
                echo "<td>".
                    "Retweeted : ".$data->retweet_count."<br>".
                    "Favorite : ".$data->favorite_count
                    ."</td>";
                echo '</tr>';
            }


            echo '</tbody><tfoot>
                            <tr> 
                                <th>ID</th>
                                <th>Tweets</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Tweet link</th>
                                <th>Tweet details</th>
                                
                            </tr>
                            </tfoot>
                        </table>';



        } elseif ($type == 'user') {
            $datas = $twitter->request('users/search', 'GET', array('q' => $query, 'count' => $limit));
//            print_r($datas);

            echo '
                <table id="mytable" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                            <tr>                          
                                <th>Name</th>
                                <th>UserName</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Followers</th>
                                <th>Friends</th>
                                <th>Joined</th>
                                
                                <th>Statuses</th>
                            </tr>
                            </thead>
                            <tbody>';

            foreach ($datas as $datano => $data){
                echo "<tr>";
                echo "<td>".'<img class="img-circle" src="'.$data->profile_image_url.'"><br>'.'<a target="_blank" href="https://twitter.com/'.$data->screen_name.'">'.$data->name. "</a></td>";
                echo "<td>".$data->screen_name."</td>";
                echo "<td>".$data->location."</td>";
                echo "<td>".$data->description."</td>";
                echo "<td>".$data->followers_count."</td>";
                echo "<td>".$data->friends_count."</td>";
                echo "<td>".Prappo::date($data->created_at)."</td>";

                echo "<td>".$data->statuses_count."</td>";
                echo "</tr>";
            }

            echo '</tbody><tfoot>
                            <tr> 
                                <th>Name</th>
                                <th>UserName</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Followers</th>
                                <th>Friends</th>
                                <th>Joined</th>
                                
                                <th>Total Status</th>
                            </tr>
                            </tfoot>
                        </table>';
        } elseif ($type == 'geo') {
            $datas = $twitter->request('geo/search', 'GET', array('query' => $query, 'count' => $limit));
            print_r($datas);
        }
    }
}
