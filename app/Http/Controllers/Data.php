<?php

namespace App\Http\Controllers;

use App\FacebookPages;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Data extends Controller
{

    /**
     * @param $valueOf
     * @return mixed
     * get settings value
     */
    public static function get($valueOf)
    {
        return DB::table('settings')->where('field', $valueOf)->value('value');

    }

    /**
     * @param int $pageId
     * @return mixed
     * get facebook page access token from database
     */
    public static function getToken($pageId)
    {
        return DB::table('facebookPages')->where('pageId', $pageId)->value('pageToken');

    }

    /**
     * @param int $pageId
     * @return string
     * get page name using page id form database
     */
    public static function getPageName($pageId)
    {
        $data = FacebookPages::where('pageId', $pageId)->get();
        $val = "";
        foreach ($data as $d) {
            $val = $d->pageName;
        }
        return $val;
    }

    public static function botButton($userId, $data = array())
    {
        $result = "";
        foreach ($data as $d) {
            $result .= '{
                         "type":"postback",
                          "title":"' . $d['question'] . '",
                          "payload":"' . $d['question'] . '"
                          },
                        ';
        }
        $json = '{
                  "recipient":{
                    "id":"' . $userId . '"
                  },
                  "message":{
                    "attachment":{
                      "type":"template",
                      "payload":{
                        "template_type":"button",
                        "text":"Help Menu",
                        "buttons":[' . $result . ']
                      }
                    }
                  }
                }';

        return $json;
    }
}
