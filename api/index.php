<?php

/** Error reporting */
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
// date_default_timezone_set('PRC');
// session_start();
// $now = time();
//防止重复提交
// if (isset($_SESSION['lastpost_token'])) {

//     $lastpost_token = $now - $_SESSION['lastpost_token'];
// } else {
//     $lastpost_token = $now;
// }
// $_SESSION['lastpost_token'] = $now;




$server = "www.wondfun.com";
if ($server == "www.wondfun.com") {
    require '../../source/class/class_core.php';
} else {
    require '../../source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz->init();
$arr = json_decode(file_get_contents("php://input"), true);
$server = "www.wondfun.com";
$ispub = true; // true  线上版本   false  开发版本
if ($server == "www.wondfun.com") {
    //$swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
    $swiper = array();
    // $swiper[0]['pic'] = 'https://img.wondfun.com/wanba/img/wx0.jpg';
    // $swiper[0]['url'] = '/pages/shop/userPlan';
    $swiper[0]['pic'] = 'https://img.wondfun.com/wanba/img/wx3.jpg';
    $swiper[0]['url'] = '/pages/shop/userPlan';
    $swiper[1]['pic'] = 'https://img.wondfun.com/wanba/img/wx1.jpg';
    $swiper[1]['url'] = 'https://mp.weixin.qq.com/s/Yb04Ew3IZClJNceH-wQE_Q';
    $swiper[2]['pic'] = 'https://img.wondfun.com/wanba/img/wx2.jpg';
    $swiper[2]['url'] = 'https://mp.weixin.qq.com/s/dufnnuYup0k6XB0bJOd-tw';
} else {
    $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
}
$act = $_GET['act'];
switch ($act) {
    case 'getSwiper':
        echo json_encode(array('swiper' => $swiper));
        break;
        //首页数据
    case 'indexData':
        $openid = $arr['openid'];


        $myjoin = DB::fetch_first("SELECT a.aid,a.sharepic,a.title,a.date FROM `wf_wanba_user` u,`wf_wanba_act` a where u.openid='" . $openid . "' and u.currentaid=a.aid and a.status<5");
        $hot = DB::fetch_all("SELECT sharepic pic, aid,title FROM wf_wanba_act WHERE template =1 and price>0 ORDER BY buy_count desc, aid DESC ");
        $demo = DB::fetch_first("select aid,sharepic from wf_wanba_act where aid=1");

        echo json_encode(array('swiper' => $swiper, 'hot' => $hot, 'myjoin' => $myjoin, 'demo' => $demo));
        break;
        //商城数据
    case 'shopList':

        //  $routes=DB::fetch_all("select title,memo,pic from wf_wanba_routeapply where status=1 order by id  desc");
        $routes = DB::fetch_all("select aid,title, route_desc as memo, sharepic  as pic from wf_wanba_act where template=1 order by aid  desc");
        foreach ($routes as $k => $v) {
            $routes[$k]['memo'] = unserialize($v['memo']);
        }
        echo json_encode(array('swiper' => $swiper, 'routes' => $routes));
        break;
        //推荐列表

    case 'recommandData':

        echo json_encode(array('swiper' => $swiper, 'hot' => $hot));
        break;
        //文章页内容
    case 'fetchContent':
        $aid = $arr['aid'];
        $to = $arr['to'];
        $content = DB::fetch_first("SELECT t.pic,t.title,c.content,t.summary FROM `wf_portal_article_title` t, `wf_portal_article_content` c where t.aid=$aid and t.aid=c.aid");
        $summary = DB::fetch_first("SELECT t.pic,t.title,c.content,t.summary FROM `wf_portal_article_title` t, `wf_portal_article_content` c where t.aid=$to and t.aid=c.aid");
        $data = array('content' => $content, 'summary' => $summary);
        echo json_encode($data);
        break;
    case 'actMyInfo':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $my = DB::fetch_first("select u.currentaid FROM `wf_wanba_user` u, wf_wanba_act a where a.aid=$aid and u.openid='" . $openid . "'");
        echo json_encode($my);
        break;
    case 'joinTeam':
        $teamid = $arr['teamid'];
        $roleid = $arr['roleid'];
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $roleid = ($arr['aid'] == 1) ? 0 : $arr['roleid'];
        $data = array('currentaid' => $aid, 'currentteamid' => $teamid, 'currentrole' => $roleid, 'lastlogin' => time());
        DB::update("wanba_user", $data, "openid='" . $openid . "'");
        echo json_encode(array('msg' => 'success'));
        break;
    case 'addUser':
        $openid = $arr['openid'];
        $unionid = $arr['unionid'] ? $arr['unionid'] : '';
        $id = DB::fetch_first("select openid from wf_wanba_user where openid='" . $openid . "'");
        $data = array('openid' => $openid, 'unionid' => $unionid, 'date' => time());
        if (!$id) {
            DB::insert('wanba_user', $data);
        }
        break;
    case 'syncUser':
        $openid = $arr['openid'];
        $unionid = $arr['unionid'] ? $arr['unionid'] : '';
        $avatar = $arr['avatar'];
        $nickname = $arr['nick'];
        $nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);
        $nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);
        $nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);
        $nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);
        $nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);
        $nickname = str_replace(array('"', '\''), '', $nickname);
        $nickname = addslashes(trim($nickname));
        $id = DB::fetch_first("select openid from wf_wanba_user where openid='" . $openid . "'");
        $data = array('openid' => $openid, 'unionid' => $unionid, 'avatar' => $avatar, 'nick' => $nickname, 'lastlogin' => time());
        if ($id) {
            DB::update("wanba_user", $data, "openid='" . $openid . "'");
            echo json_encode(array('msg' => 'success'));
        } else {
            echo json_encode(array('msg' => 'err'));
        }
        break;
    case 'getActStatus':
        $aid = $arr['aid'];
        $data = DB::fetch_first("select status from wf_wanba_act where aid=$aid");
        $status = ($data) ? $data['status'] : -1;
        echo json_encode($status);
        break;
    case 'actBaseInfo':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $data = DB::fetch_first("select a.aid aid, a.title title, from_unixtime(a.date, '%Y-%m-%d') date, a.sharepic sharepic, a.teamnum teamnum,a.logopic logopic,a.slogan slogan,a.cat cat,t.id teamThemeId,t.title themeTitle from wf_wanba_act a,wf_wanba_team_theme t  where a.aid=$aid  and a.teamThemeId=t.id and a.creator='" . $openid . "'");
        //$data['sharepic']=$data['sharepic'].'?'.time();
        //$data['logopic']=$data['logopic'].'?'.time();
        echo json_encode($data);
        break;
    case 'actInfo':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $act = DB::fetch_first("SELECT * FROM  `wf_wanba_act`  where aid=$aid");

        //$task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        $task = DB::fetch_all("SELECT  `taskid`, `aid`, `name`, `memo`, `pvalue`,tip1,tip2, `poi`, `pmemo`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng`, GROUP_CONCAT( DISTINCT displayorder ) AS displayorder FROM  `wf_wanba_task`  WHERE aid =$aid GROUP BY displayorder");

        foreach ($task as $k => $v) {
            $task[$k]['pics'] = DB::fetch_all("select DISTINCT url from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
        }


        $teams = DB::fetch_all("select * from wf_wanba_team_setting where aid=$aid and displayorder<=$act[teamNum]");
        foreach ($teams as $k => $v) {
            $pos = strpos($v['pic'], '?');
            if ($pos == false) {
                $teams[$k]['pic'] = $v['pic'];
            } else {
                $teams[$k]['pic'] = substr($v['pic'], 0, $pos);
            }
        }
        $mycurrentaid = DB::result_first("select currentaid FROM `wf_wanba_user`   where  openid='" . $openid . "'");
        if ($mycurrentaid == 0) {
            $myteam = array();
        } else {
            if ($aid != $mycurrentaid) {
                //$data = array('currentaid' => 0, 'currentteamid' => 0, 'currentrole' => 2);
                $data = array('currentteamid' => 0, 'currentrole' => 2);
                DB::update('wanba_user', $data, "openid='" . $openid . "'");
                $myteam = array();
            } else {
                $myteam = DB::fetch_first("SELECT s.*,u.* FROM  `wf_wanba_team_setting` s,  `wf_wanba_user` u WHERE s.aid =$aid AND u.openid =  '" . $openid . "'  and u.currentteamid=s.displayorder");
            }
        }
        if (count($myteam) > 0) {
            $money = DB::fetch_first("SELECT sum(score) money FROM  `wf_wanba_logs`  where aid=$aid and teamid=$myteam[currentteamid]");
            if ($money) {
                $myteam['money'] = $money['money'];
            } else {
                $myteam['money'] = 0;
            }
            // $myteam['currentrole'] = 0;
        }
        // $myteampic=$myteam['pic'];
        // $pos=strpos($myteampic,'?');
        //     if($pos==false){
        //         $myteam['pic'] = $myteampic;
        //     }else{
        //         $myteam['pic']= substr($myteampic,0,$pos);
        //     }
        $data = array('task' => $task, 'myteam' => $myteam, 'act' => $act, 'teams' => $teams);
        echo json_encode($data);

        break;
    case 'getTeamSetting':
        $aid = $arr['aid'];
        $teamnum = DB::result_first("select teamNum from wf_wanba_act where aid=$aid");
        $teamsetting = DB::fetch_all("SELECT * FROM  `wf_wanba_team_setting`  where aid=$aid and displayorder<=$teamnum order by displayorder");
        $data = array('teamsetting' => $teamsetting);
        echo json_encode($data);
        break;
    case 'viewteam':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $myteam = DB::fetch_all("SELECT openid,nick,avatar,currentrole FROM  `wf_wanba_user`  where currentaid=$aid and currentteamid=$teamid");
        $data = array('teams' => $myteam);
        echo json_encode($data);

        break;
    case 'adminViewTeam':
        $aid = $arr['aid'];
        $openid = $arr['uid'];
        $teams = DB::fetch_all("SELECT name,displayorder  id FROM  `wf_wanba_team_setting`  where aid=$aid");
        foreach ($teams as $k => $v) {
            $teams[$k]['members'] = DB::fetch_all("SELECT openid,nick,avatar,currentrole  FROM  `wf_wanba_user`  where currentaid=$aid and currentteamid=$v[id] order by currentrole");
        }
        $data = array('teams' => $teams);
        echo json_encode($data);

        break;
    case 'quitteam':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $openid = $arr['openid'];
        // $data = array('currentaid' => 0, 'currentteamid' => 0, 'currentrole' => 2);
        $data = array('currentteamid' => 0, 'currentrole' => 2);
        DB::update('wanba_user', $data, "currentaid=$aid and currentteamid=$teamid and openid='" . $openid . "'");
        echo json_encode(array('status' => true));
        break;
    case 'iscaptain':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $openid = $arr['openid'];
        $data = array('currentaid' => $aid, 'currentteamid' => $teamid, 'currentrole' => 0);
        DB::update('wanba_user', $data, "openid='" . $openid . "'");
        echo json_encode(array('status' => true));
        break;
    case 'listenAddMoneyResult':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $token = $arr['token'];
        $teamid = $arr['teamid'];
        $data = DB::fetch_first("select token from wf_wanba_logs where aid=$aid and  teamid=$teamid  and  token='" . $token . "'");
        if ($data) {
            $status = true;
            $msg = "成功";
        } else {
            $status = false;
            $msg = "请等待";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        break;
    case 'listenScanResult':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $data = DB::fetch_first("select currentrole from wf_wanba_user where currentaid=$aid and  openid='" . $openid . "'");
        if ($data && $data['currentrole'] == 0) {
            $status = true;
            $msg = "恭喜获得队长权限";
        } else {
            $status = false;
            $msg = "请等待";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        break;
    case 'listenTradeResult':
        $token = $arr['token'];
        $aid = $arr['aid'];
        $taskid = $arr['taskid'];
        $teamid = $arr['teamid'];
        $score = $arr['score'];
        $data = DB::fetch_first("select token from wf_wanba_logs where aid=$aid and taskid=$taskid and teamid=$teamid and score=$score and  token='" . $token . "'");
        if ($data) {
            $status = true;
            $msg = "交易成功";
        } else {
            $status = false;
            $msg = "请等待";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        break;

    case 'listenAuctionResult':
        $token = $arr['token'];
        $aid = $arr['aid'];
        $taskid = $arr['taskid'];
        $teamid = $arr['teamid'];
        $score = 0 - $arr['score'];
        $data = DB::fetch_first("select token from wf_wanba_logs where aid=$aid and taskid=$taskid and teamid=$teamid and score=$score and  token='" . $token . "'");
        if ($data) {
            $status = true;
            $msg = "交易成功";
        } else {
            $status = false;
            $msg = "请等待";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        break;
    case 'listenCheckTaskResult':
        $token = $arr['token'];
        $aid = $arr['aid'];
        $taskid = $arr['taskid'];
        $teamid = $arr['teamid'];

        $data = DB::fetch_first("select token,memo,event from wf_wanba_logs where aid=$aid and taskid=$taskid and teamid=$teamid and  token='" . $token . "'");
        if ($data) {
            $status = true;
            $msg = ($data['event'] ? $data['event'] : '教练提交了你的挑战结果，请等待管理员最终判定');
        } else {
            $status = false;
            $msg = "请等待";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        break;
    case 'postAnswer':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $taskid = $arr['taskid'];
        $score = intval($arr['pvalue']);
        $date = time();
        $token = $arr['token'];
        $openid = $arr['openid'];
        $memo = $arr['memo'];

        $pass = DB::fetch_first("select * from wf_wanba_pass where  aid=$aid and teamid=$teamid and taskid=$taskid");
        if ($pass) {
            if ($pass['pass'] == -2) {
                $data = array('pass' => 0, 'date' => time());
                DB::update('wanba_pass', $data, "id=$pass[id]");
                $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'creator' => $openid, 'date' => $date, 'token' => $token, 'memo' => $memo);

                $id = DB::insert('wanba_logs', $data, 'id');
                if ($id) {
                    echo json_encode(array('status' => true, 'id' => $id));
                } else {
                    echo json_encode(array('status' => false, 'msg' => '操作失败'));
                }
            } else {
                echo json_encode(array('status' => false, 'msg' => '该队其他队长已经提交过此任务了'));
            }
        } else {
            $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time());
            DB::insert('wanba_pass', $data);
            $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'creator' => $openid, 'date' => $date, 'token' => $token, 'memo' => $memo);

            $id = DB::insert('wanba_logs', $data, 'id');
            if ($id) {
                echo json_encode(array('status' => true, 'id' => $id));
            } else {
                echo json_encode(array('status' => false, 'msg' => '操作失败'));
            }
        }

        break;
    case 'queryAnswerstatus':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $taskid = $arr['taskid'];
        $openid = $arr['openid'];
        $sessionname = $arr['sessionname'];
        $sessionvalue = $arr['sessionvalue'];
        $hassession = DB::fetch_first("select value from wf_wanba_session where name='" . $sessionname . "'");

        if ($hassession) {
            //$data=array('value'=>$sessionvalue);
            //DB::update('wanba_session',$data,"name='".$sessionname."'");
        } else {
            // $data = array('value' => $sessionvalue, 'name' => $sessionname);
            // DB::insert('wanba_session', $data);
        }
        $q = DB::fetch_first("select value from wf_wanba_session where name='" . $sessionname . "'");
        $n = $q ? $q['value'] : 0;
        $pass = DB::fetch_first("select pass from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
        //$stone = DB::fetch_first("select stonevar,stonerandom from wf_wanba_act where aid=$aid");
        // $stone['list'] = DB::fetch_all("SELECT * FROM  `wf_wanba_stone` ");
        // $data = array('pass' => $pass, 'stone' => $stone, "sessionvalue" => $n);
        $data = array('pass' => $pass);
        echo json_encode($data);
        break;
    case 'allUpload':
        $aid = $arr['aid'];
        //$sql = "SELECT FROM_UNIXTIME(l.date) date,t.aid aid,t.pvalue pvalue,t.name name,t.memo memo,s.name team,s.pic flag,l.id id FROM `wf_wanba_logs` l, `wf_wanba_task` t, `wf_wanba_team_setting` s  where  l.aid=$aid and l.status=0 and l.taskid=t.taskid and l.teamid=s.displayorder and l.aid=s.aid";
        $sql = "SELECT id passid,aid,teamid,taskid FROM `wf_wanba_pass`   where  aid=$aid and pass=0";
        $data = DB::fetch_all($sql);

        foreach ($data as $k => $v) {
            // $score = DB::fetch_first("SELECT FROM_UNIXTIME(l.date) date,t.aid aid,t.pvalue pvalue,t.name name,t.memo memo,s.name team,s.pic flag,l.id id FROM `wf_wanba_logs` l, `wf_wanba_task` t, `wf_wanba_team_setting` s  where  l.aid=$aid and l.status=0 and l.taskid=t.taskid and l.teamid=s.displayorder and l.aid=s.aid");
            $score = DB::fetch_first("select id logid, FROM_UNIXTIME(date) date from wf_wanba_logs where aid=$aid and teamid=$v[teamid] and taskid=$v[taskid]  order  by id desc");
            $data[$k]['logid'] = $score['logid'];
            $data[$k]['date'] = $score['date'];
            $task = DB::fetch_first("select name,pvalue,owner,ptype,displayorder,mine,memo,media,url from wf_wanba_task where taskid=$v[taskid]");
            $data[$k]['pvalue'] = $task['pvalue'];
            $data[$k]['name'] = $task['name'];
            $data[$k]['owner'] = $task['owner'];
            $data[$k]['ptype'] = $task['ptype'];
            $data[$k]['media'] = $task['media'];
            $data[$k]['url'] = $task['url'];
            $data[$k]['displayorder'] = $task['displayorder'];
            $data[$k]['mine'] = $task['mine'];
            $data[$k]['memo'] = $task['memo'];
            $team = DB::fetch_first("select name,pic,color from wf_wanba_team_setting where aid=$aid and displayorder=$v[teamid]");
            $data[$k]['team'] = $team['name'];
            $pos = strpos($team['pic'], '?');
            if ($pos == false) {
                $data[$k]['flag'] = $team['pic'];
            } else {
                $data[$k]['flag'] = substr($team['pic'], 0, $pos);
            }
            $data[$k]['color'] = $team['color'];
            //$data[$k]= DB::fetch_first("SELECT FROM_UNIXTIME(l.date) date,t.aid aid,t.pvalue pvalue,t.name name,t.memo memo,s.name team,s.pic flag,l.id id FROM `wf_wanba_logs` l, `wf_wanba_task` t, `wf_wanba_team_setting` s  where  l.aid=$aid and l.status=0 and l.taskid=t.taskid and l.teamid=s.displayorder and l.aid=s.aid");
        }
        echo json_encode($data);
        break;
    case 'viewUploadDetail':
        $logid = $arr['logid'];

        $sql = "SELECT  memo,id,teamid,taskid  FROM `wf_wanba_logs`  where  id=$logid";
        $data = DB::fetch_first($sql);

        $data['uploadpic'] = DB::fetch_all("SELECT * FROM  `wf_wanba_log_pic` where logid=$logid");
        $data['uploadvideo'] = DB::fetch_all("SELECT * FROM  `wf_wanba_log_video` where logid=$logid order by picid desc limit 0,1");
        $data['pic'] = DB::fetch_all("SELECT DISTINCT url FROM  `wf_wanba_pic` where taskid=$data[taskid]");

        echo json_encode($data);
        break;
    case 'pass':

        $taskid = $arr['taskid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $passid = $arr['passid'];
        $currentowner = $arr['owner'];
        $pvalue = $arr['pvalue'];
        $ptype = $arr['ptype'];
        $mine = $arr['mine'];
        $posid = $arr['displayorder'];
        $creator = $arr['creator'];
        $currentowner = DB::fetch_first("select owner,mine from wf_wanba_task where taskid=$taskid");

        //普通点
        if (intval($ptype) == 0) {
            //有人占了
            if ($currentowner && $currentowner[owner] != '') {
                //如有雷
                //$msg = ' 很遗憾，' . $posid . '号地已经有人占了';
                //触雷
                $mine = $currentowner['mine'];
                if (intval($mine) > 0) {
                    $money = 3 * $mine;
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => 0 - 3 * intval($mine), 'date' => time(), 'event' => '在' . $posid . '号地触雷，损失' . $money);
                    $id = DB::insert('wanba_logs', $data, 'id');
                    //布雷者加倍
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $currentowner[owner], 'score' => 3 * intval($mine), 'date' => time(), 'event' => '有人踩了' . $posid . '号地雷，你获得了' . $money);
                    $id = DB::insert('wanba_logs', $data, 'id');
                    //将雷设置归零
                    $data = array('mine' => 0);
                    DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                }
                $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'date' => time(), 'event' => '完成' . $posid . '号地的任务');
                $id = DB::insert('wanba_logs', $data, 'id');
                //写pass通关
                //$pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");

                $data = array('pass' => 2, 'date' => time());
                DB::update('wanba_pass', $data, "id=$passid");
            }
            //未占
            else {
                //写Log
                $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '获得' . $posid . '号地');
                $id = DB::insert('wanba_logs', $data, 'id');
                //更改拥有者
                $data = array('pvalue' => $pvalue, 'owner' => $teamid);
                DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                //写pass通关
                //$pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");

                $data = array('pass' => 2, 'date' => time());
                DB::update('wanba_pass', $data, "id=$passid");
            }
        }
        //拍卖点
        elseif (intval($ptype) == 1) {
            //写Log
            $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '获得' . $posid . '号地的拍卖权');
            $id = DB::insert('wanba_logs', $data, 'id');
            //更改拥有者
            if ($currentowner['owner'] == '') {
                $owner = $teamid;
            } else {
                if (strpos(strval($currentowner['owner']), strval($teamid)) !== false) {
                    $owner = $currentowner['owner'];
                } else {
                    $owner = $currentowner['owner'] . ',' . $teamid;
                }
            }
            $data = array('owner' => $owner);
            DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
            //写pass通关

            $data = array('pass' => 2, 'date' => time());
            DB::update('wanba_pass', $data, "id=$passid");
        } elseif (intval($ptype) == 2) {
            //写Log
            $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '占有' . $posid . '号地');
            $id = DB::insert('wanba_logs', $data, 'id');
            //更改拥有者
            $owner = $teamid;
            $data = array('owner' => $owner);
            DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
            //写pass通关

            $data = array('pass' => 2, 'date' => time());
            DB::update('wanba_pass', $data, "id=$passid");
        } else if (intval($ptype) == 3) { }

        echo json_encode(array('status' => true));
        break;
    case 'deny':
        $passid = $arr['passid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $taskid = $arr['taskid'];
        $event = $arr['event'];
        $pvalue = $arr['pvalue'];
        $creator = $arr['creator'];

        //改状态为未通过
        $data = array('pass' => -2, "checkdate" => time());
        DB::update("wanba_pass", $data, "id=$passid");

        $data = array('aid' => $aid, 'creator' => $creator, 'teamid' => $teamid, 'taskid' => $taskid, 'status' => 0, 'event' => '挑战失败，理由:' . $event, 'date' => time());
        DB::insert('wanba_logs', $data);

        echo json_encode(array('status' => true));

        break;

    case 'updateMine':
        $taskid = $arr['taskid'];
        $mine = intval($arr['mine']);
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $pvalue = $arr['pvalue'];
        $money = DB::fetch_first("SELECT  sum(score) money FROM  `wf_wanba_logs`  where aid=$aid and  teamid=$teamid");
        $money = ($money) ? $money['money'] : 0;
        $rule = DB::fetch_first("select minenum,minevalue from wf_wanba_act where aid=$aid");
        $q = DB::fetch_first("SELECT count(mine) num FROM `wf_wanba_task` where owner=$teamid and mine>0 and aid=$aid");
        $total_mine = $q['num'];
        $maxmoney = intval($pvalue * $rule['minevalue'] / 100);
        $hasposted = DB::fetch_first("select mine from wf_wanba_task where aid=$aid  and taskid=$taskid");
        if ($hasposted && $hasposted['mine'] > 0) {
            echo json_encode(array('status' => false, 'msg' => '其他队长已经布雷'));
        } else {

            if ($total_mine >= $rule['minenum']) {
                echo json_encode(array('status' => false, 'msg' => '你已布了个' . $total_mine . '雷,最多可以同时布' . $rule['minenum'] . '个雷'));
            } else {
                if ($mine <= $money) {
                    if ($mine > $maxmoney) {
                        echo json_encode(array('status' => false, 'msg' => '最多可以布' . $maxmoney . '的雷'));
                    } else {
                        $data = array('mine' => $mine);
                        DB::update('wanba_task', $data, "taskid=$taskid");
                        //写入log
                        $task = DB::fetch_first("select * from wf_wanba_task where taskid=$taskid");
                        $data = array('aid' => $task['aid'], 'taskid' => $taskid, 'teamid' => $task['owner'], 'score' => 0 - $mine, 'date' => time(), 'event' => '你在' . $task['displayorder'] . '号地布了一颗' . $mine . '的雷');
                        $id = DB::insert('wanba_logs', $data, 'id');
                        echo json_encode(array('status' => true, 'msg' => '布雷成功'));
                    }
                } else {
                    echo json_encode(array('status' => false, 'msg' => '钱不够哦,你有' . $money . ',布雷需要' . $mine));
                }
            }
        }
        break;
    case 'getMyteamMoney':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $money = DB::fetch_first("SELECT l.teamid buyer, sum(l.score) money FROM `wf_wanba_user`  u,`wf_wanba_logs`  l where l.aid =$aid and u.openid= '" . $openid . "' and u.currentteamid=l.teamid and u.currentaid=l.aid");
        $teamname = DB::result_first("select name from wf_wanba_team_setting where id=$money[buyer]");
        $money['buyer_teamname'] = $teamname;
        echo json_encode($money);
        break;
    case 'dealTrade':


        $price = $arr['price'];
        $buyer = $arr['buyer'];
        $buyer_teamname = $arr['buyer_teamname'];
        $seller = $arr['seller'];
        $seller_teamname = $arr['seller_teamname'];
        $taskid = $arr['taskid'];
        $aid = $arr['aid'];
        $posid = $arr['posid'];
        $token = $arr['token'];
        
        //取出不可交易点
        $lands = DB::fetch_all("select displayorder from wf_wanba_task where aid=$aid and (ptype=1 or ptype=3)");
        $arr1 = array();
        foreach ($lands as $k => $v) {
            $arr1[] = $v['displayorder'];
        }
        //$arr1 = array(1, 9, 17,  33, 41, 49, 7, 13, 19, 31, 37, 43);
        //计算购买者的钱
        $money = DB::fetch_first("SELECT SUM( score ) money  FROM  `wf_wanba_logs`   WHERE aid =$aid AND teamid =$buyer");
        if ($money) {
            $m = $money['money'];
            if ($m > 300 && $m - $price > 0) {
                if (count($arr1) > 0) {
                    if (in_array($posid, $arr1)) {
                        echo json_encode(array('status' => false));
                    } else {
                        //减去购买者钱，做log
                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $buyer, 'score' => 0 - intval($price), 'date' => time(), 'token' => $token, 'event' => '以' . $price . '的价格从' . $seller_teamname . '购得' . $posid . '号地');
                        $id = DB::insert('wanba_logs', $data, 'id');
                        //给出售者加钱，做log
                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $seller, 'score' => $price, 'date' => time(), 'token' => $token, 'event' => $buyer_teamname . '以' . $price . '的价格购得您的' . $posid . '号地');
                        $id = DB::insert('wanba_logs', $data, 'id');
                        //更新此地块地价
                        $data = array('pvalue' => $price, 'owner' => $buyer);
                        DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");

                        echo json_encode(array('status' => true));
                    }
                } else {
                    echo json_encode(array('status' => false));
                }
            } else {
                echo json_encode(array('status' => false));
            }
        } else {
            echo json_encode(array('status' => false));
        }

        break;
    case 'dealAuction':
        $sellprice = $arr['sellprice'];
        $teamid = $arr['teamid'];
        $seller_teamname = $arr['seller_teamname'];
        $taskid = $arr['taskid'];
        $aid = $arr['aid'];
        $posid = $arr['posid'];
        $token = $arr['token'];
        //减去竞拍队伍钱,插入log
        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'token' => $token, 'score' => 0 - intval($sellprice), 'date' => time(), 'event' => $seller_teamname . '以' . $sellprice . '的价格拍得' . $posid . '号地');
        $id = DB::insert('wanba_logs', $data, 'id');
        //更改地产所有者
        $data = array('pvalue' => $sellprice, 'owner' => $teamid);
        DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
        echo json_encode(array('status' => true));
        break;

    case 'autoUpdateScore':
        $n = rand(0, 100);
        usleep($n * 1000);
        $taskid = $arr['taskid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $pass = $arr['pass'];
        $owner = $arr['owner'];
        $pvalue = $arr['pvalue'];
        $ptype = $arr['ptype'];
        $mine = $arr['mine'];
        $posid = $arr['displayorder'];
        $currentowner = DB::fetch_first("select owner,mine from wf_wanba_task where taskid=$taskid");

        $hasposted = DB::fetch_first("select pass from wf_wanba_pass where pass>=0 and aid=$aid and teamid=$teamid and taskid=$taskid");
        if ($hasposted) {
            $msg = '该队其他队长已经提交过此任务了';
        } else {
            //普通点
            if (intval($ptype) == 0) {
                //有人占了
                if ($currentowner && $currentowner[owner] != '') {
                    //如有雷
                    $msg = ' 很遗憾，' . $posid . '号地已经有人占了';
                    $mine = $currentowner['mine'];
                    //触雷
                    if (intval($mine) > 0) {
                        $money = 3 * $mine;
                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => 0 - 3 * intval($mine), 'date' => time(), 'event' => '在' . $posid . '号地触雷，损失' . $money);
                        $id = DB::insert('wanba_logs', $data, 'id');
                        //布雷者加倍

                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $currentowner[owner], 'score' => 3 * intval($mine), 'date' => time(), 'event' => '有人踩了' . $posid . '号地雷，你获得了' . $money);
                        $id = DB::insert('wanba_logs', $data, 'id');
                        //将雷设置归零
                        $data = array('mine' => 0);
                        DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                    }
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'date' => time(), 'event' => '完成' . $posid . '号地的任务');
                    $id = DB::insert('wanba_logs', $data, 'id');
                    //写pass通关
                    $pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
                    if ($pass) {
                        $data = array('pass' => 2, 'date' => time());
                        DB::update('wanba_pass', $data, "id=$pass[id]");
                    } else {
                        $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time(), 'pass' => 2);
                        DB::insert('wanba_pass', $data);
                    }
                }
                //未占
                else {
                    //写Log
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '获得' . $posid . '号地');
                    $id = DB::insert('wanba_logs', $data, 'id');
                    //更改拥有者
                    $data = array('pvalue' => $pvalue, 'owner' => $teamid);
                    DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                    //写pass通关
                    $pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
                    if ($pass) {
                        $data = array('pass' => 2, 'date' => time());
                        DB::update('wanba_pass', $data, "id=$pass[id]");
                    } else {
                        $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time(), 'pass' => 2);
                        DB::insert('wanba_pass', $data);
                    }
                    $msg = '获得' . $posid . '号地';
                }
            }
            //拍卖点
            elseif (intval($ptype) == 1) {
                //写Log
                $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '获得' . $posid . '号地的拍卖权');
                $id = DB::insert('wanba_logs', $data, 'id');
                //更改拥有者
                if ($currentowner['owner'] == '') {
                    $owner = $teamid;
                } else {
                    if (strpos(strval($currentowner['owner']), strval($teamid)) !== false) {
                        $owner = $currentowner['owner'];
                    } else {
                        $owner = $currentowner['owner'] . ',' . $teamid;
                    }
                }


                $data = array('owner' => $owner);

                DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                //写pass通关
                $pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
                if ($pass) {
                    $data = array('pass' => 2, 'date' => time());
                    DB::update('wanba_pass', $data, "id=$pass[id]");
                } else {
                    $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time(), 'pass' => 2);
                    DB::insert('wanba_pass', $data);
                }
                $msg = ' 获得' . $posid . '号地的拍卖权';
            } elseif ($ptype == 3) {
                //写Log
                $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => $pvalue, 'date' => time(), 'event' => '完成' . $posid . '号地的任务');
                $id = DB::insert('wanba_logs', $data, 'id');
                //更改拥有者
                if ($currentowner['owner'] == '') {
                    $owner = $teamid;
                } else {
                    if (strpos(strval($currentowner['owner']), strval($teamid)) !== false) {
                        $owner = $currentowner['owner'];
                    } else {
                        $owner = $currentowner['owner'] . ',' . $teamid;
                    }
                }


                $data = array('owner' => $owner);

                DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                //写pass通关
                $pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
                if ($pass) {
                    $data = array('pass' => 2, 'date' => time());
                    DB::update('wanba_pass', $data, "id=$pass[id]");
                } else {
                    $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time(), 'pass' => 2);
                    DB::insert('wanba_pass', $data);
                }
                $msg = ' 完成' . $posid . '号地的任务';
            }
        }

        echo json_encode($msg);
        break;
    case 'getLogs':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        if ($teamid == -99) {
            $sql = " SELECT from_unixtime(date, '%H:%i:%s') date, score, memo, event, id,status FROM `wf_wanba_logs` where aid = $aid and (event<>'' or memo<>'') order by id desc ";
        } else {
            $sql = " SELECT from_unixtime(date, '%H:%i:%s') date, score, memo, event, id,status FROM `wf_wanba_logs` where ((teamid = $teamid and aid = $aid) or (status=1 and aid=$aid)) and (event<>'' or memo<>'') order by id desc ";
        }
        $data = DB::fetch_all($sql);
        // foreach ($data as $v) {
        // 	$key_arrays[] = $v['id'];
        // }
        // array_multisort($key_arrays, SORT_DESC, SORT_NUMERIC, $data);
        echo json_encode($data);
        break;
    case 'coachCheckTask':
        $taskid = $arr['taskid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $pass = $arr['pass'];
        $openid = $arr['openid'];
        $token = $arr['token'];
        $teamname = $arr['teamname'];
        $posid = $arr['posid'];
        $date = time();
        $ptype=DB::result_first("select ptype from wf_wanba_task where taskid=$taskid");
        $hasposted = ($ptype == 2) ? false : DB::fetch_first(" select pass from wf_wanba_pass where pass >= 0  and and aid = $aid and  teamid = $teamid and taskid = $taskid ");
        if ($hasposted) {
            echo json_encode(array('status' => false, 'msg' => '该队其他队长已经提交过此任务了'));
        } else {
            if ($pass == -2) {
                $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'creator' => $openid, 'date' => $date, 'token' => $token, 'event' => '你在' . $posid . '号点的挑战失败');
                $id = DB::insert('wanba_logs', $data, 'id');
                $pass = DB::fetch_first(" select * from wf_wanba_pass where aid = $aid and teamid = $teamid and taskid = $taskid ");
                if ($pass) {
                    $data = array('pass' => -2, 'date' => time());
                    DB::update('wanba_pass', $data, " id = $pass[id] ");
                } else {
                    $data = array('aid' => $aid, 'pass' => -2, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time());
                    DB::insert('wanba_pass', $data);
                }
            } else if ($pass == 0) {
                $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'creator' => $openid, 'date' => $date, 'token' => $token, 'memo' => '教练判定' . $teamname . '在' . $posid . '号点的挑战成功');

                $id = DB::insert('wanba_logs', $data, 'id');
                $pass = DB::fetch_first(" select * from wf_wanba_pass where aid = $aid and teamid = $teamid and taskid = $taskid ");
                if ($pass) {
                    $data = array('pass' => 0, 'date' => time());
                    DB::update('wanba_pass', $data, " id = $pass[id] ");
                } else {
                    $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time());
                    DB::insert('wanba_pass', $data);
                }
            }

            if ($id) {
                echo json_encode(array('status' => true, 'id' => $id));
            } else {
                echo json_encode(array('status' => false, 'msg' => '操作失败'));
            }
        }

        break;
    case 'addMoney':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $teamid = $arr['teamid'];
        $score = $arr['score'];
        $teamname = $arr['teamname'];
        $token = $arr['token'];
        $posid = intval($arr['posid']);
        $taskid = intval($arr['taskid']);
        //给挑战点任务评分
        if ($posid > 0) {
            $currentowner = DB::fetch_first("select owner from wf_wanba_task where taskid=$taskid");
            $hasposted = DB::fetch_first("select pass from wf_wanba_pass where pass>=0 and aid=$aid and teamid=$teamid and taskid=$taskid");
            if ($hasposted) {
                $msg = '该队其他队长已经提交过此任务了';
            } else {
                $data = array('aid' => $aid, 'score' => $score, 'teamid' => $teamid, 'taskid' => $taskid, 'token' => $token, 'date' => time(), 'event' => '完成' . $posid . '号点任务，获得加分' . $score);
                $id = DB::insert('wanba_logs', $data, 'id');
                //更改拥有者
                if ($currentowner['owner'] == '') {
                    $owner = $teamid;
                } else {
                    if (strpos(strval($currentowner['owner']), strval($teamid)) !== false) {
                        $owner = $currentowner['owner'];
                    } else {
                        $owner = $currentowner['owner'] . ',' . $teamid;
                    }
                }


                $data = array('owner' => $owner);

                DB::update('wanba_task', $data, "aid=$aid and taskid=$taskid");
                //写pass通关
                $pass = DB::fetch_first("select * from wf_wanba_pass where aid=$aid and teamid=$teamid and taskid=$taskid");
                if ($pass) {
                    $data = array('pass' => 2, 'date' => time());
                    DB::update('wanba_pass', $data, "id=$pass[id]");
                } else {
                    $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => $taskid, 'date' => time(), 'pass' => 2);
                    DB::insert('wanba_pass', $data);
                }
            }
        } else {
            $data = array('aid' => $aid, 'score' => $score, 'teamid' => $teamid, 'token' => $token, 'date' => time(), 'event' => '获得加分' . $score);
            $id = DB::insert('wanba_logs', $data, 'id');
        }

        if ($id) {
            echo json_encode(array('status' => true, 'msg' => '操作成功', 'id' => $id));
        } else {
            echo json_encode(array('status' => false, 'msg' => '操作失败'));
        }
        break;
    case 'updateActModeTest':

        $aid = $arr['aid'];
        $oldmode = DB::result_first("select mode from wf_wanba_act where aid=$aid");
        if ($oldmode == -1) {
            //删除现有设置
            $tasks = DB::fetch_all("select taskid, `aid`, `name`, `memo`, `pvalue`, `poi`, `pmemo`, `displayorder`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng` from `wf_wanba_task_bak` where aid=$aid");
            foreach ($tasks as $ke => $va) {
                DB::delete("wanba_pic", "taskid=$va[taskid]");
            }
            DB::delete("wanba_task", "aid=$aid");
            //从task_bak表取出设置写入
            $tasks_bak = DB::fetch_all("select taskid, `aid`, `name`, `memo`, `pvalue`, `poi`, `pmemo`, `displayorder`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng` from `wf_wanba_task_bak` where aid=$aid");
            foreach ($tasks_bak as $k => $v) {
                //DB::insert("wanba_task_bak",$v);
                $pics = DB::fetch_all("select url from wf_wanba_pic where taskbakid=$v[taskid]");
                $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => $v['answer'], "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $aid, "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);

                $insert_task_id = DB::insert('wanba_task', $insert, 'taskid');
                foreach ($pics as $ke => $va) {
                    $u = array("url" => $va['url'], 'taskid' => $insert_task_id);
                    DB::insert('wanba_pic', $u);
                }
            }

            DB::delete("wanba_task_bak", "aid=$aid");
        }
        $mode = $arr['mode'];
        $sql = "update `wf_wanba_team_setting`  set stone1=0,stone2=0,stone3=0,stone4=0,stone5=0,stone6=0,godview=0  where aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_pass` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_logs` WHERE aid=$aid";
        DB::query($sql);
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0,pvalue=300  where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        DB::query($sql);
        DB::delete("wanba_album", "aid=$aid");
        $data = array('mode' => $mode, 'status' => -1);
        DB::update('wanba_act', $data, "aid=$aid");

        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'updateActMode':
        $aid = $arr['aid'];
        $mode = $arr['mode'];
        $sql = "update `wf_wanba_team_setting`  set stone1=0,stone2=0,stone3=0,stone4=0,stone5=0,stone6=0,godview=0  where aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_pass` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_logs` WHERE aid=$aid";
        DB::query($sql);
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0,pvalue=300  where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        DB::query($sql);
        DB::delete("wanba_album", "aid=$aid");
        $data = array('mode' => $mode, 'status' => -1);
        DB::update('wanba_act', $data, "aid=$aid");

        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'updateStoneMode':
        $aid = $arr['aid'];
        $mode = $arr['mode'];
        $data = array('stone_mode' => $mode);
        DB::update('wanba_act', $data, "aid=$aid");
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'updateActStatus':
        $aid = $arr['aid'];
        $status = $arr['status'];
        $mode = $arr['mode'];
        // $oldStatus=DB::result_first("select status from wf_wanba_act where aid=$aid");
        // if(($oldStatus==0 || $oldStatus==1) && $status==-1 ){
        //     echo json_encode(array('status' => false, 'msg' => '操作失败'));
        // } else if ($oldStatus==2 && $status<$oldStatus){
        // 	echo json_encode(array('status' => false, 'msg' => '操作失败'));
        // }else{
        // 	$data=array('status'=>$status);
        // 	DB::update('wanba_act',$data,"aid=$aid");
        // 	echo json_encode(array('status' => true, 'msg' => '操作成功'));
        // }
        if ($status == -1) {
            $event = '管理员更改状态为未开放';
        } else if ($status == 0) {
            $event = '管理员更改状态为游戏中';
        } else if ($status == 1) {
            $event = '管理员更改状态为游戏结束，即将开始交易拍卖环节';
        } else if ($status == 2) {
            $event = '管理员更改状态为交易状态';
        } else if ($status == 3) {
            $event = '管理员更改状态为交易结束';
        } else if ($status == 4) {
            $event = '管理员更改状态为拍卖状态';
        } else if ($status == 5) {
            $event = '管理员更改状态为全场结束';
        }
        $data = array('status' => $status);
        DB::update('wanba_act', $data, "aid=$aid");
        $data = array('event' => $event, 'status' => 1, date => time(), 'aid' => $aid);
        DB::insert('wanba_logs', $data);

        //如mode=2 正式模式 且status=5 活动结束
        if ($mode == 2 && $status == 5) {
            //删除原有用户数据
            DB::delete("wanba_history", "aid=$aid");
            $actusers = DB::fetch_all("SELECT openid,currentaid aid, currentteamid teamid,currentrole role,lastlogin FROM `wf_wanba_user` where currentaid=$aid");
            //重新插入数据
            foreach ($actusers as $k => $v) {
                DB::insert('wanba_history', $v);
            }
        }
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;

    case 'postPhotoUploadSettting':
        $aid = $arr['aid'];
        $va = ($arr['va'] > 20) ? 20 : $arr['va'];


        $data = array('uploadPhotoSetting' => $va);
        DB::update("wanba_act", $data, "aid=$aid");
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'postRedbagSetting':
        $aid = $arr['aid'];
        $redbagtotal = $arr['redbagtotal'];
        $redbagrand = $arr['redbagrand'];

        $data = array('redbagtotal' => $redbagtotal, 'redbagrand' => $redbagrand);
        DB::update("wanba_act", $data, "aid=$aid");
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'postSetting':
        $aid = $arr['aid'];
        $pvalue = $arr['pvalue'] ? $arr['pvalue'] : 300;
        $pvalue1 = $arr['pvalue1'] ? $arr['pvalue1'] : 300;
        $pvalue2 = $arr['pvalue2'] ? $arr['pvalue2'] : 300;
        $pvalue3 = $arr['pvalue3'] ? $arr['pvalue3'] : 300;
        //$pvalue = 300;
        $gps = intval($arr['gps']);
        $offset = ($gps > 0) ? $gps : 0;
        $gps = ($gps > 0) ? 1 : 0;
        $mineNum = $arr['mineNum'];
        $mineMoney = $arr['mineMoney'];
        $end = $arr['endTime'];
        if ($end != '') {
            // $end = strtotime($end) - 8 * 60 * 60;  开发版本设置
            $end = $ispub ? strtotime($end) : strtotime($end) - 8 * 60 * 60;  //开发服务器要减去8小时时差

        }
        $data = array('pvalue' => $pvalue, 'pvalue1' => $pvalue1, 'pvalue2' => $pvalue2, 'pvalue3' => $pvalue3, 'minenum' => $mineNum, 'minevalue' => $mineMoney, 'gpsEnabled' => $gps, 'offset' => $offset, 'endTime' => $end);
        DB::update("wanba_act", $data, "aid=$aid");
        //$data = array('pvalue' => $pvalue);
        // DB::update("wanba_task", $data, "aid=$aid and ptype=0");
        // $data1 = array('pvalue' => $pvalue1);
        // DB::update("wanba_task", $data, "aid=$aid and ptype=1");
        // if ($aid == 11 || $aid == 12) {
        //     $data = array('pvalue' => 0);
        //     DB::update("wanba_task", $data, "aid=$aid and displayorder=25");
        // }
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'postCoach':
        $aid = $arr['aid'];
        $coach = $arr['coach'];
        $data = array('coach' => $coach);
        DB::update("wanba_act", $data, "aid=$aid");
        echo json_encode(array('status' => true, 'msg' => '操作成功'));
        break;
    case 'topBoard':
        $aid = $arr['aid'];
        //$data = DB::fetch_all("SELECT sum(s.score) score,t.name name,t.color color FROM `wf_wanba_logs` s, `wf_wanba_team_setting`  t WHERE s.aid=$aid and t.id=s.teamid group by t.id order by score desc");
        $data = DB::fetch_all("SELECT sum(score) score,teamid  FROM `wf_wanba_logs` WHERE aid=$aid and teamid>0 group by teamid order by score desc");
        foreach ($data as $k => $v) {
            $team = DB::fetch_first("select name,color from `wf_wanba_team_setting` where aid=$aid and displayorder=$v[teamid]");
            $data[$k]['name'] = $team['name'];
            $data[$k]['color'] = $team['color'];
        }
        echo json_encode(array('list' => $data));
        break;
    case 'updateMyStep':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $step = $arr['step'];
        $data = array('aid' => $aid, 'openid' => $openid, 'teamid' => $teamid, 'step' => $step, 'date' => date("Y-m-d"));
        $isExist = DB::fetch_first("select aid from wf_wanba_werun where aid=$aid  and openid='" . $openid . "'");
        if ($isExist) {
            if (count($isExist) > 0) {
                DB::update('wanba_werun', $data, "aid=$aid  and openid='" . $openid . "'");
            } else {
                DB::insert('wanba_werun', $data);
            }
        } else {
            DB::insert('wanba_werun', $data);
        }


        $teamstep = DB::result_first("select sum(step) from wf_wanba_werun where teamid=$teamid and aid=$aid and date=CURDATE()");
        $allstep = DB::result_first("select sum(step) from wf_wanba_werun where aid=$aid and teamid<>0 and date=CURDATE()");
        $teamsteplist = DB::fetch_all("SELECT teamid,sum(step) step FROM `wf_wanba_werun`   where  aid=$aid and date=CURDATE() and teamid>0 group by teamid order by step desc");
        foreach ($teamsteplist as $k => $v) {
            $vars = DB::fetch_first("select name,color from `wf_wanba_team_setting` where aid=$aid and displayorder=$v[teamid]");
            $teamsteplist[$k]['teamname'] = $vars['name'];
            $teamsteplist[$k]['color'] = $vars['color'];
        }
        echo json_encode(array('mystep' => $step, 'teamstep' => $teamstep, 'allstep' => $allstep, 'teamsteplist' => $teamsteplist));
        break;
    case 'updateStone':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $teamname = $arr['teamname'];
        $stonename = $arr['stoneName'];
        $sessionname = $arr['sessionname'];
        $sessionvalue = $arr['sessionvalue'];
        $stone = $arr['stone'];
        $stoneIndex = $arr['stoneIndex'];
        //记录缓存
        $hassession = DB::fetch_first("select * from wf_wanba_session where name='" . $sessionname . "'");
        if ($hassession) {
            $data = array('value' => $sessionvalue);
            DB::update('wanba_session', $data, "name='" . $sessionname . "'");
        } else {
            $data = array('value' => $sessionvalue, 'name' => $sessionname);
            DB::insert('wanba_session', $data);
        }
        //更新宝石池
        $db = array('stonevar' => $stone);
        DB::update('wanba_act', $db, "aid=$aid");
        //更新队伍的宝石数量
        $col = 'stone' . $stoneIndex;
        $q = DB::fetch_first("select " . $col . " as f from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
        $n = ($q) ? $q['f'] : 0;
        $n = $n + 1;
        DB::query("update wf_wanba_team_setting set " . $col . "=" . $n . " where aid=$aid and displayorder=$teamid");
        $log = array('event' => $teamname . '获得了一颗' . $stonename, 'status' => 1, date => time(), 'aid' => $aid, 'teamid' => $teamid);
        DB::insert('wanba_logs', $log);
        break;
        //旧的ai逻辑
    case 'getRandomStone1':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $stones = DB::fetch_all("select type,path,token from wf_wanba_stone_list where aid=$aid");
        if ($stones) {
            if (count($stones) > 0) {
                $selectedstone = $stones[array_rand($stones)];
                //从宝石池中删除这颗宝石
                @unlink($selectedstone['path']);
                DB::delete('wanba_stone_list', "aid=$aid and token='" . $selectedstone['token'] . "'");
                //宝石信息
                $stoneinfo = DB::fetch_first("select * from wf_wanba_stone where id=$selectedstone[type]");

                //更新队伍的宝石数量
                $col = 'stone' . $selectedstone[type];
                $q = DB::fetch_first("select " . $col . " as f from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                $n = ($q) ? $q['f'] : 0;
                $n = $n + 1;
                DB::query("update wf_wanba_team_setting set " . $col . "=" . $n . " where aid=$aid and displayorder=$teamid");
                $teamname = DB::result_first("select name from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                $log = array('event' => $teamname . '获得了一颗' . $stoneinfo[name], 'status' => 1, date => time(), 'aid' => $aid, 'teamid' => $teamid);
                DB::insert('wanba_logs', $log);
                $result = array('status' => true, 'stonetype' => $selectedstone[type]);
            } else {
                // $result = array('status' => false);
            }
        } else {
            // $result = array('status' => false);
        }
        echo json_encode($result);

        break;
        //ai寻宝逻辑
    case 'getRandomStone':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $keyword = $arr['keyword'];
        $aipics = DB::fetch_all("select title from wf_wanba_ai_pic where aid=$aid");

        $stones = DB::fetch_all("select type,path,token from wf_wanba_stone_list where aid=$aid");
        $redbag = DB::fetch_first("select redbagrand,ai_random from wf_wanba_act where aid=$aid");
        if ($redbag['redbagrand'] == 0) {
            $setting = 0;
        } else {
            $s = preg_replace('/\|/', ',', $redbag['redbagrand']);
            $setting = explode(',', $s);
            $redbagrnd = $setting[array_rand($setting)];
        }

        if ($stones) {
            if (count($stones) > 0) {
                //有设置关键词匹配
                if ($aipics) {
                    $keywords = array();
                    foreach ($aipics as $k => $v) {
                        $keywords[] = $v['title'];
                    }
                    if (in_array($keyword, $keywords)) {
                        //匹配到
                        //删除关键词图片
                        DB::delete("wanba_ai_pic", "aid=$aid and title='" . $keyword . "'");
                        $selectedstone = $stones[array_rand($stones)];
                        //从宝石池中删除这颗宝石
                        @unlink($selectedstone['path']);
                        DB::delete('wanba_stone_list', "aid=$aid and token='" . $selectedstone['token'] . "'");
                        //宝石信息
                        $stoneinfo = DB::fetch_first("select * from wf_wanba_stone where id=$selectedstone[type]");

                        //更新队伍的宝石数量
                        $col = 'stone' . $selectedstone[type];
                        $q = DB::fetch_first("select " . $col . " as f from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                        $n = ($q) ? $q['f'] : 0;
                        $n = $n + 1;
                        DB::query("update wf_wanba_team_setting set " . $col . "=" . $n . " where aid=$aid and displayorder=$teamid");
                        $teamname = DB::result_first("select name from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                        $log = array('event' => $teamname . '获得了一颗' . $stoneinfo[name], 'status' => 1, date => time(), 'aid' => $aid, 'teamid' => $teamid);
                        DB::insert('wanba_logs', $log);
                        $result = array('status' => true, 'stonetype' => $selectedstone[type]);
                    } else {
                        //未匹配到 则走红包
                        $rnd = mt_rand(0, 100);
                        if ($rnd <= 40) {
                            if ($setting == 0) {
                                $result = array('status' => false);
                            } else {
                                //插入log
                                $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => -1,  'date' => time(), 'score' => $redbagrnd, 'memo' =>  '本队队员在AI寻宝中赢得了' . $redbagrnd . '红包');

                                $id = DB::insert('wanba_logs', $data, 'id');
                                $result = array('status' => true, 'redbag' => $redbagrnd);
                            }
                        } else {
                            $result = array('status' => false);
                        }
                    }
                } else {
                    //没有设置宝石匹配关键词
                    //如小于设定的爆率
                    $rate = $redbag['ai_random'];
                    $rnd = mt_rand(0, 100);
                    if ($rnd <= $rate) {
                        //得到宝石
                        $selectedstone = $stones[array_rand($stones)];
                        //从宝石池中删除这颗宝石
                        @unlink($selectedstone['path']);
                        DB::delete('wanba_stone_list', "aid=$aid and token='" . $selectedstone['token'] . "'");
                        //宝石信息
                        $stoneinfo = DB::fetch_first("select * from wf_wanba_stone where id=$selectedstone[type]");

                        //更新队伍的宝石数量
                        $col = 'stone' . $selectedstone[type];
                        $q = DB::fetch_first("select " . $col . " as f from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                        $n = ($q) ? $q['f'] : 0;
                        $n = $n + 1;
                        DB::query("update wf_wanba_team_setting set " . $col . "=" . $n . " where aid=$aid and displayorder=$teamid");
                        $teamname = DB::result_first("select name from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
                        $log = array('event' => $teamname . '获得了一颗' . $stoneinfo[name], 'status' => 1, date => time(), 'aid' => $aid, 'teamid' => $teamid);
                        DB::insert('wanba_logs', $log);
                        $result = array('status' => true, 'stonetype' => $selectedstone[type]);
                    } else {
                        //走红包
                        $rnd = mt_rand(0, 100);
                        if ($rnd <= 40) {
                            if ($setting == 0) {
                                $result = array('status' => false);
                            } else {
                                //插入log
                                $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => -1,  'date' => time(), 'score' => $redbagrnd, 'memo' =>  '本队队员在AI寻宝中赢得了' . $redbagrnd . '红包');

                                $id = DB::insert('wanba_logs', $data, 'id');
                                $result = array('status' => true, 'redbag' => $redbagrnd);
                            }
                        } else {
                            $result = array('status' => false);
                        }
                    }
                }
            } else {
                //无宝石则走红包
                if ($redbag) {
                    if ($setting == 0) {
                        $result = array('status' => false);
                    } else {
                        //插入log
                        $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => -1,  'date' => time(), 'score' => $redbagrnd, 'memo' =>  '本队队员在AI寻宝中赢得了' . $redbagrnd . '红包');

                        $id = DB::insert('wanba_logs', $data, 'id');
                        $result = array('status' => true, 'redbag' => $redbagrnd);
                    }
                } else {
                    $result = array('status' => false);
                }
            }
        } else {
            //无宝石则走红包
            if ($redbag) {
                if ($setting == 0) {
                    $result = array('status' => false);
                } else {
                    //插入log
                    $data = array('aid' => $aid, 'teamid' => $teamid, 'taskid' => -1,  'date' => time(), 'score' => $redbagrnd, 'memo' =>  '本队队员在AI寻宝中赢得了' . $redbagrnd . '红包');

                    $id = DB::insert('wanba_logs', $data, 'id');
                    $result = array('status' => true, 'redbag' => $redbagrnd);
                }
            } else {
                $result = array('status' => false);
            }
        }

        echo json_encode($result);

        break;
    case 'getStone':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $teamname = $arr['teamname'];
        $token = $arr['token'];
        $stoneIndex = $arr['stone'];
        $hasStone = DB::fetch_first("select token,path from wf_wanba_stone_list where aid=$aid and token='" . $token . "'");
        if ($hasStone) {
            //从宝石池中删除这颗宝石
            @unlink($hasStone['path']);
            DB::delete('wanba_stone_list', "aid=$aid and token='" . $token . "'");


            //宝石信息
            $stoneinfo = DB::fetch_first("select * from wf_wanba_stone where id=$stoneIndex");

            //更新队伍的宝石数量
            $col = 'stone' . $stoneIndex;
            $q = DB::fetch_first("select " . $col . " as f from wf_wanba_team_setting where aid=$aid and displayorder=$teamid");
            $n = ($q) ? $q['f'] : 0;
            $n = $n + 1;
            DB::query("update wf_wanba_team_setting set " . $col . "=" . $n . " where aid=$aid and displayorder=$teamid");
            $log = array('event' => $teamname . '获得了一颗' . $stoneinfo[name], 'status' => 1, date => time(), 'aid' => $aid, 'teamid' => $teamid);
            DB::insert('wanba_logs', $log);
            echo json_encode($stoneinfo);
        }
        break;
    case 'getBoxStone':
        $teamid = $arr['teamid'];
        $aid = $arr['aid'];
        $stones = DB::fetch_all("SELECT *  FROM  `wf_wanba_stone`  order by id");

        foreach ($stones as $k => $v) {
            $field = 'stone' . $v['id'];

            $stones[$k][$field] = DB::result_first("select " . $field . " from `wf_wanba_team_setting`  where aid=$aid and displayorder=$teamid");
        }
        echo json_encode($stones);
        break;
    case 'useStone2':
        $teamid = $arr['teamid'];
        $oldteamid = $arr['teamid'];
        $aid = $arr['aid'];
        $stoneid = $arr['stoneid'];
        $teamname = $arr['teamname'];
        $myteamname = $arr['myteamname'];
        $myteamid = $arr['myteamid'];
        $teamid1 = $arr['teamid1'];
        $teamid2 = $arr['teamid2'];
        $taskid1 = $arr['taskid1'];
        $taskid2 = $arr['taskid2'];
        $landIdexchange1 = $arr['landIdexchange1'];
        $landIdexchange2 = $arr['landIdexchange2'];
        $landid = $arr['landid'];
        $taskid = $arr['taskid'];
        // $result = array('msg' => $arr, 'status' => true);
        // echo json_encode($result);
        // exit();
        //判断宝石数量
        //减去宝石数量
        $field = 'stone' . $stoneid;
        $num = DB::result_first("select " . $field . " from wf_wanba_team_setting where aid=$aid and displayorder=$myteamid");

        if ($num > 0) {
            $num = $num - 1;
            $arr = array($field => $num);
            DB::update('wanba_team_setting', $arr, "aid=$aid and displayorder=$myteamid");
            //判断是否有防御
            if ($stoneid != 3) {
                //$teamid = $teamid ? $teamid : $myteamid;
                if ($teamid) {
                    $blocked = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and owner=$teamid and block=1");
                } else {
                    $blocked = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and owner=$myteamid and block=1");
                }
                if ($blocked) {
                    $msg = '对方正用现实宝石进行防御';
                    //取消对方防御
                    $data = array('block' => 0);
                    if ($teamid) {
                        DB::update('wanba_task', $data, "aid=$aid and ptype=0 and owner=$teamid");
                    } else {
                        DB::update('wanba_task', $data, "aid=$aid and ptype=0 and owner=$myteamid");
                    }
                    //写log
                    if ($teamid) {
                        $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                        DB::insert('wanba_logs', $data);
                        $data = array('aid' => $aid, 'teamid' => $teamid, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                        DB::insert('wanba_logs', $data);
                    } else {
                        $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                        DB::insert('wanba_logs', $data);
                        $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                        DB::insert('wanba_logs', $data);
                    }

                    $result = array('msg' => $msg, 'status' => true);
                    echo json_encode($result);
                    exit();
                } else {
                    switch ($stoneid) {
                        case 1:



                            //改变该地块的占有者
                            $arr = array('owner' => $myteamid);
                            DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid");
                            //写pass通关表
                            $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid and aid=$aid and teamid=$myteamid");
                            if ($isExist) {

                                $data = array('pass' => 2, 'date' => time());
                                DB::update('wanba_pass', $data, " id = $isExist[id] ");
                            } else {
                                $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $myteamid, 'taskid' => $taskid, 'date' => time());
                                DB::insert('wanba_pass', $data);
                            }
                            //插入公告
                            $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了力量宝石，抢夺了' . $landid . '号地块');
                            DB::insert('wanba_logs', $data);
                            $msg = '成功抢夺了' . $landid . '号地块';
                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);


                            break;
                        case 2:
                            //查出队伍20分钟内的收益,并写log
                            $now = time();
                            $before = $now - 20 * 60;
                            $d = DB::fetch_first("SELECT sum(score) score FROM `wf_wanba_logs`  where  aid=$aid and teamid=$myteamid and date>=$before  and date<=$now");

                            if ($d) {
                                $score = $d['score'];
                                if ($score < 0) {
                                    $win = 0 - $score;
                                    $msg = $teamname . '在最近20分钟内收益是负的，你损失了' . $win;
                                    //给自己扣钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);
                                    //插入公告
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score == 0) {
                                    $msg = $teamname . '在最近20分钟内无任何收益';
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，然而一无所获');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score > 0) {
                                    $msg = '获得了' . $teamname . '在最近20分钟内的收益' . $score;
                                    //给自己加钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);

                                    //插入公告
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，获得了其在最近20分钟内的收益');
                                    DB::insert('wanba_logs', $data);
                                }
                            } else {
                                $msg = $teamname . '在最近20分钟内无任何收益';
                                $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，然而一无所获');
                                DB::insert('wanba_logs', $data);
                            }

                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            break;

                        case 3:

                            //改变该地块的占有者
                            $arr = array('owner' => $teamid2);
                            DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid1");
                            $arr = array('owner' => $teamid1);
                            DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid2");
                            //写pass通关表
                            $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid1 and aid=$aid and teamid=$teamid2");
                            if ($isExist) {

                                $data = array('pass' => 2, 'date' => time());
                                DB::update('wanba_pass', $data, " id = $isExist[id] ");
                            } else {
                                $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $teamid2, 'taskid' => $taskid1, 'date' => time());
                                DB::insert('wanba_pass', $data);
                            }
                            //插入公告
                            $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了空间宝石');
                            DB::insert('wanba_logs', $data);
                            //通知
                            $data = array('aid' => $aid, 'teamid' => $teamid1, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                            DB::insert('wanba_logs', $data);
                            $data = array('aid' => $aid, 'teamid' => $teamid2, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                            DB::insert('wanba_logs', $data);
                            $msg = '成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块';
                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            break;
                        case 4:

                            //查出对方队伍20分钟内的收益,并写log
                            $now = time();
                            $before = $now - 20 * 60;
                            $d = DB::fetch_first("SELECT sum(score) score FROM `wf_wanba_logs`  where   aid=$aid and  teamid=$oldteamid and date>=$before  and date<=$now");

                            if ($d) {
                                $score = $d['score'];
                                if ($score < 0) {
                                    $win = 0 - $score;
                                    $msg = $teamname . '在最近20分钟内收益是负的，你损失了' . $win;
                                    //给自己扣钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);
                                    //给对方加钱

                                    $data = array('aid' => $aid, 'teamid' => $oldteamid, 'score' => $win, 'date' => time(), 'event' => $myteamname . '对你使用了心灵宝石，由于你在最近20分钟内的收益是负的，你意外获得了财富' . $win);
                                    DB::insert('wanba_logs', $data);
                                    //插入公告
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了心灵宝石，获得了' . $teamname . '在最近20分钟内的收益');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score == 0) {
                                    $msg = $teamname . '在最近20分钟内无任何收益';
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '对' . $teamname . '使用了心灵宝石，然而一无所获');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score > 0) {
                                    $msg = '获得了' . $teamname . '在最近20分钟内的收益' . $score;
                                    //给自己加钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);
                                    //给对方扣钱
                                    $lost = 0 - $score;

                                    $data = array('aid' => $aid, 'teamid' => $oldteamid, 'score' => $lost, 'date' => time(), 'event' => $myteamname . '使用了心灵宝石，获得了你在最近20分钟内的收益' . $score);
                                    DB::insert('wanba_logs', $data);
                                    //插入公告
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了心灵宝石，获得了' . $teamname . '在最近20分钟内的收益');
                                    DB::insert('wanba_logs', $data);
                                }
                            } else {
                                $msg = $teamname . '在最近20分钟内无任何收益';
                                $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '对' . $teamname . '使用了心灵宝石，然而一无所获');
                                DB::insert('wanba_logs', $data);
                            }


                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            break;
                        case 5:
                            $msg = '成功使用了灵魂宝石，可以看全局信息3分钟';
                            //写入失效时间
                            $d = array('godview' => time() + 3 * 60);
                            DB::update("wanba_team_setting", $d, "aid=$aid and displayorder=$myteamid");

                            //写入log
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '使用了灵魂宝石');
                            DB::insert('wanba_logs', $data);
                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            break;
                        case 6:
                            $msg = '成功使用了现实宝石，正在防御你的地产';


                            //写入log
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'taskid' => $taskid, 'date' => time(), 'event' => '使用了现实宝石');
                            DB::insert('wanba_logs', $data);
                            //更新拥有地产的block
                            $data = array('block' => 1);

                            DB::update('wanba_task', $data, "aid=$aid and owner=$myteamid and ptype=0");

                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            break;
                    }
                }
            } else {

                // $block1 = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and owner=$teamid1 and block=1");
                // $block2 = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and  owner=$teamid2 and block=1");
                $block1 = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and taskid=$taskid1 and block=1");
                $block2 = DB::fetch_first("select * from wf_wanba_task where aid=$aid and ptype=0 and  taskid=$taskid2 and block=1");
                //取消对方防御
                if ($block1 || $block2) {
                    $msg = '有现实宝石进行防御，这次操作失效了';

                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                    DB::insert('wanba_logs', $data);
                    if ($block1) {
                        $data = array('block' => 0);
                        if ($teamid1 > 0) {
                            DB::update('wanba_task', $data, "aid=$aid and ptype=0 and owner=$teamid1");
                        }
                        if ($teamid1 > 0) {
                            $data = array('aid' => $aid, 'teamid' => $teamid1, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                            DB::insert('wanba_logs', $data);
                        }
                    } else if ($block2) {
                        $data = array('block' => 0);
                        if ($teamid2 > 0) {
                            DB::update('wanba_task', $data, "aid=$aid and ptype=0 and owner=$teamid2");
                        }
                        if ($teamid2 > 0) {
                            $data = array('aid' => $aid, 'teamid' => $teamid2, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                            DB::insert('wanba_logs', $data);
                        }
                    }
                    $result = array('msg' => $msg, 'status' => true);
                    echo json_encode($result);
                    exit();
                } else {
                    //写pass通关表

                    $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid1 and aid=$aid and teamid=$teamid2");
                    if ($isExist) {

                        $data = array('pass' => 2, 'date' => time());
                        DB::update('wanba_pass', $data, " id = $isExist[id] ");
                    } else {
                        $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $teamid2, 'taskid' => $taskid1, 'date' => time());
                        DB::insert('wanba_pass', $data);
                    }

                    $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid2 and aid=$aid and teamid=$teamid1");
                    if ($isExist) {

                        $data = array('pass' => 2, 'date' => time());
                        DB::update('wanba_pass', $data, " id = $isExist[id] ");
                    } else {
                        $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $teamid1, 'taskid' => $taskid2, 'date' => time());
                        DB::insert('wanba_pass', $data);
                    }
                    //插入公告
                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了空间宝石');
                    DB::insert('wanba_logs', $data);
                    //通知
                    $data = array('aid' => $aid, 'teamid' => $teamid1, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                    DB::insert('wanba_logs', $data);
                    $data = array('aid' => $aid, 'teamid' => $teamid2, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                    DB::insert('wanba_logs', $data);
                    $msg = '成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块';
                    //交换地块的占有者
                    if ($teamid1 == 0) {
                        $teamid1 = '';
                    }
                    if ($teamid2 == 0) {
                        $teamid2 = '';
                    }
                    $arr = array('owner' => $teamid2);
                    DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid1");
                    $arr = array('owner' => $teamid1);
                    DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid2");

                    $result = array('msg' => $msg, 'status' => true);
                    echo json_encode($result);
                    break;
                }
            }
        } else {
            $msg = '宝石数量不足';
            $result = array('msg' => $msg, 'status' => true);
            echo json_encode($result);
            exit();
        }
        break;
    case 'useStone':
        $aid = $arr['aid'];
        $actstatus = DB::result_first("select status from `wf_wanba_act` where aid=$aid");
        if ($actstatus > 0) {
            $result = array('msg' => '游戏已结束，无法再使用宝石', 'status' => true);
            echo json_encode($result);
        } else {
            $teamid = $arr['teamid'];
            $oldteamid = $arr['teamid'];

            $stoneid = $arr['stoneid'];
            $teamname = $arr['teamname'];
            $myteamname = $arr['myteamname'];
            $myteamid = $arr['myteamid'];
            $teamid1 = $arr['teamid1'];
            $teamid2 = $arr['teamid2'];
            $taskid1 = $arr['taskid1'];
            $taskid2 = $arr['taskid2'];
            $landIdexchange1 = $arr['landIdexchange1'];
            $landIdexchange2 = $arr['landIdexchange2'];
            $landid = $arr['landid'];
            $taskid = $arr['taskid'];
            // $result = array('msg' => $arr, 'status' => true);
            // echo json_encode($result);
            // exit();
            //判断宝石数量
            //减去宝石数量
            $field = 'stone' . $stoneid;
            $num = DB::result_first("select " . $field . " from wf_wanba_team_setting where aid=$aid and displayorder=$myteamid");

            if ($num > 0) {
                $num = $num - 1;
                $arr = array($field => $num);
                DB::update('wanba_team_setting', $arr, "aid=$aid and displayorder=$myteamid");
                //判断是否有防御
                switch ($stoneid) {
                        //风暴宝石
                    case 7:
                        $blocked = DB::fetch_first("select block from `wf_wanba_team_setting`  where aid=$aid and displayorder=$teamid and block=1");
                        if ($blocked) {
                            $msg = '对方正用现实宝石进行防御';
                            //取消对方防御
                            $data = array('block' => 0);
                            DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$teamid");
                            //写log
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                            DB::insert('wanba_logs', $data);
                            $data = array('aid' => $aid, 'teamid' => $teamid, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                            DB::insert('wanba_logs', $data);


                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            exit();
                        } else {
                            $landonwer = DB::fetch_first("SELECT owner  FROM  `wf_wanba_task`   WHERE taskid=$taskid");

                            if ($landonwer['owner'] <> '') {
                                //$land = $lands[array_rand($lands)]['displayorder'];
                                //删pass通关表
                                DB::delete("wanba_pass", "taskid=$taskid and aid=$aid");
                                DB::query("update `wf_wanba_task` set owner='' where aid =$aid and displayorder=$landid");
                                $msg = '成功使用了风暴宝石，' . $landid . '号地现在成了无主之地';
                                //写入log
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => $msg);
                                DB::insert('wanba_logs', $data);
                                //插入公告
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => $landid . '号地遭受风暴，成了无主之地，大家冲鸭！');
                                DB::insert('wanba_logs', $data);
                            } else {
                                $msg = '成功使用了风暴宝石，然而好像什么都没发生';
                                //写入log
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => $msg);
                                DB::insert('wanba_logs', $data);
                                //插入公告
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => $landid . '号地遭受风暴，成了无主之地，大家冲鸭！');
                                DB::insert('wanba_logs', $data);
                            }
                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                        }
                        break;
                    case 6:
                        $msg = '成功使用了现实宝石，正在防御你的地产';
                        $data = array('aid' => $aid, 'teamid' => $myteamid, 'taskid' => $taskid, 'date' => time(), 'event' => '使用了现实宝石');
                        DB::insert('wanba_logs', $data);
                        //更新拥有地产的block
                        $data = array('block' => 1);

                        DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$myteamid");

                        $result = array('msg' => $msg, 'status' => true);
                        echo json_encode($result);
                        break;
                    case 5:
                        $msg = '成功使用了灵魂宝石，可以看全局信息3分钟';
                        //写入失效时间
                        $d = array('godview' => time() + 3 * 60);
                        DB::update("wanba_team_setting", $d, "aid=$aid and displayorder=$myteamid");

                        //写入log
                        $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '使用了灵魂宝石');
                        DB::insert('wanba_logs', $data);
                        $result = array('msg' => $msg, 'status' => true);
                        echo json_encode($result);

                        break;
                    case 2:
                        //查出队伍20分钟内的收益,并写log
                        $now = time();
                        $before = $now - 20 * 60;
                        $d = DB::fetch_first("SELECT sum(score) score FROM `wf_wanba_logs`  where  aid=$aid and teamid=$myteamid and date>=$before  and date<=$now");

                        if ($d) {
                            $score = $d['score'];
                            if ($score < 0) {
                                $win = 0 - $score;
                                $msg = $teamname . '在最近20分钟内收益是负的，你损失了' . $win;
                                //给自己扣钱
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                DB::insert('wanba_logs', $data);
                                //插入公告
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石');
                                DB::insert('wanba_logs', $data);
                            } elseif ($score == 0) {
                                $msg = $teamname . '在最近20分钟内无任何收益';
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，然而一无所获');
                                DB::insert('wanba_logs', $data);
                            } elseif ($score > 0) {
                                $msg = '获得了' . $teamname . '在最近20分钟内的收益' . $score;
                                //给自己加钱
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                DB::insert('wanba_logs', $data);

                                //插入公告
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，获得了其在最近20分钟内的收益');
                                DB::insert('wanba_logs', $data);
                            }
                        } else {
                            $msg = $teamname . '在最近20分钟内无任何收益';
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了时间宝石，然而一无所获');
                            DB::insert('wanba_logs', $data);
                        }

                        $result = array('msg' => $msg, 'status' => true);
                        echo json_encode($result);
                        break;
                    case 4:
                        $blocked = DB::fetch_first("select block from `wf_wanba_team_setting`  where aid=$aid and displayorder=$teamid and block=1");
                        if ($blocked) {
                            $msg = '对方正用现实宝石进行防御';
                            //取消对方防御
                            $data = array('block' => 0);
                            DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$teamid");
                            //写log
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                            DB::insert('wanba_logs', $data);
                            $data = array('aid' => $aid, 'teamid' => $teamid, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                            DB::insert('wanba_logs', $data);


                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                            exit();
                        } else {
                            //查出对方队伍20分钟内的收益,并写log
                            $now = time();
                            $before = $now - 20 * 60;
                            $d = DB::fetch_first("SELECT sum(score) score FROM `wf_wanba_logs`  where   aid=$aid and  teamid=$oldteamid and date>=$before  and date<=$now");

                            if ($d) {
                                $score = $d['score'];
                                if ($score < 0) {
                                    $win = 0 - $score;
                                    $msg = $teamname . '在最近20分钟内收益是负的，你损失了' . $win;
                                    //给自己扣钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);
                                    //给对方加钱

                                    $data = array('aid' => $aid, 'teamid' => $oldteamid, 'score' => $win, 'date' => time(), 'event' => $myteamname . '对你使用了心灵宝石，由于你在最近20分钟内的收益是负的，你意外获得了财富' . $win);
                                    DB::insert('wanba_logs', $data);
                                    //插入公告
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了心灵宝石，获得了' . $teamname . '在最近20分钟内的收益');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score == 0) {
                                    $msg = $teamname . '在最近20分钟内无任何收益';
                                    $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '对' . $teamname . '使用了心灵宝石，然而一无所获');
                                    DB::insert('wanba_logs', $data);
                                } elseif ($score > 0) {
                                    $msg = '获得了' . $teamname . '在最近20分钟内的收益' . $score;
                                    //给自己加钱
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'score' => $score, 'date' => time(), 'event' => $msg);
                                    DB::insert('wanba_logs', $data);
                                    //给对方扣钱
                                    $lost = 0 - $score;

                                    $data = array('aid' => $aid, 'teamid' => $oldteamid, 'score' => $lost, 'date' => time(), 'event' => $myteamname . '使用了心灵宝石，获得了你在最近20分钟内的收益' . $score);
                                    DB::insert('wanba_logs', $data);
                                    //插入公告
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了心灵宝石，获得了' . $teamname . '在最近20分钟内的收益');
                                    DB::insert('wanba_logs', $data);
                                }
                            } else {
                                $msg = $teamname . '在最近20分钟内无任何收益';
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '对' . $teamname . '使用了心灵宝石，然而一无所获');
                                DB::insert('wanba_logs', $data);
                            }


                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                        }
                        break;
                    case 3:
                        $flag1 = false;
                        $owner1 = DB::fetch_first("select owner from wf_wanba_task where aid=$aid and taskid=$taskid1");
                        $owner2 = DB::fetch_first("select owner from wf_wanba_task where aid=$aid and taskid=$taskid2");
                        if ($owner1) {
                            if ($owner1['owner'] == '') {
                                $owner1 = 0;
                            } else {
                                $owner1 = $owner1['owner'];
                            }
                        } else {
                            $owner1 = 0;
                        }
                        if ($owner2) {
                            if ($owner2['owner'] == '') {
                                $owner2 = 0;
                            } else {
                                $owner2 = $owner2['owner'];
                            }
                        } else {
                            $owner2 = 0;
                        }
                        if ($owner1 == 0 && $owner2 == 0) {
                            $result = array('msg' => '不能对两块无主之地使用', 'status' => false);
                            //加回宝石
                            $field = 'stone' . $stoneid;
                            $num = DB::result_first("select " . $field . " from wf_wanba_team_setting where aid=$aid and displayorder=$myteamid");
                            $num = $num + 1;
                            $arr = array($field => $num);
                            DB::update('wanba_team_setting', $arr, "aid=$aid and displayorder=$myteamid");
                            echo json_encode($result);
                        } else {
                            $blocked1 = DB::fetch_first("select block from `wf_wanba_team_setting`  where aid=$aid and displayorder=$owner1 and displayorder<>$myteamid and block=1");
                            if ($blocked1) {
                                $msg = '对方正用现实宝石进行防御';
                                //取消对方防御
                                $data = array('block' => 0);
                                DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$owner1");
                                //写log
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                                DB::insert('wanba_logs', $data);
                                $data = array('aid' => $aid, 'teamid' => $owner1, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                                DB::insert('wanba_logs', $data);
                                $result = array('msg' => $msg, 'status' => true);
                            }
                            $blocked2 = DB::fetch_first("select block from `wf_wanba_team_setting`  where aid=$aid and displayorder=$owner2 and displayorder<>$myteamid and block=1");
                            if ($blocked2) {
                                $msg = '对方正用现实宝石进行防御';
                                //取消对方防御
                                $data = array('block' => 0);
                                DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$owner2");
                                //写log
                                $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                                DB::insert('wanba_logs', $data);
                                $data = array('aid' => $aid, 'teamid' => $owner2, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                                DB::insert('wanba_logs', $data);
                                $result = array('msg' => $msg, 'status' => true);
                            }
                            if ($blocked1 || $blocked2) {
                                echo json_encode($result);
                            } else {
                                //写pass通关表

                                $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid1 and aid=$aid and teamid=$owner2");
                                if ($isExist) {

                                    $data = array('pass' => 2, 'date' => time());
                                    DB::update('wanba_pass', $data, " id = $isExist[id] ");
                                } else {
                                    $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $teamid2, 'taskid' => $taskid1, 'date' => time());
                                    DB::insert('wanba_pass', $data);
                                }

                                $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid2 and aid=$aid and teamid=$owner1");
                                if ($isExist) {

                                    $data = array('pass' => 2, 'date' => time());
                                    DB::update('wanba_pass', $data, " id = $isExist[id] ");
                                } else {
                                    $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $owner1, 'taskid' => $taskid2, 'date' => time());
                                    DB::insert('wanba_pass', $data);
                                }
                                //插入公告
                                $data = array('aid' => $aid, 'teamid' => $owner1, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了空间宝石');
                                DB::insert('wanba_logs', $data);
                                //通知
                                $data = array('aid' => $aid, 'teamid' => $owner1, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                                DB::insert('wanba_logs', $data);
                                $data = array('aid' => $aid, 'teamid' => $owner2, 'date' => time(), 'event' => $myteamname . '使用了空间宝石，成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块');
                                DB::insert('wanba_logs', $data);
                                $msg = '成功交换了' . $landIdexchange1 . '和' . $landIdexchange2 . '号地块';
                                //交换地块的占有者
                                if ($owner1 == 0) {
                                    $owner1 = '';
                                }
                                if ($owner2 == 0) {
                                    $owner2 = '';
                                }
                                $arr = array('owner' => $owner2);
                                DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid1");
                                $arr = array('owner' => $owner1);
                                DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid2");

                                $result = array('msg' => $msg, 'status' => true);
                                echo json_encode($result);
                            }
                        }
                        break;
                    case 1:
                        $flag = false;
                        $owner = DB::fetch_first("select owner from wf_wanba_task where aid=$aid and taskid=$taskid");
                        if ($owner && $owner['owner'] != '') {
                            $owner = $owner['owner'];
                            if ($owner == $myteamid) {
                                $result = array('msg' => '不能对自己拥有的地产使用宝石', 'status' => false);
                                //加回宝石
                                $field = 'stone' . $stoneid;
                                $num = DB::result_first("select " . $field . " from wf_wanba_team_setting where aid=$aid and displayorder=$myteamid");
                                $num = $num + 1;
                                $arr = array($field => $num);
                                DB::update('wanba_team_setting', $arr, "aid=$aid and displayorder=$myteamid");
                                $flag = false;
                                echo json_encode($result);
                            } else {
                                $blocked = DB::fetch_first("select block from `wf_wanba_team_setting`  where aid=$aid and displayorder=$owner and block=1");
                                if ($blocked) {
                                    $msg = '对方正用现实宝石进行防御';
                                    //取消对方防御
                                    $data = array('block' => 0);
                                    DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$owner");
                                    //写log
                                    $data = array('aid' => $aid, 'teamid' => $myteamid, 'date' => time(), 'event' => '对方正用现实宝石进行防御,你的操作失效了');
                                    DB::insert('wanba_logs', $data);
                                    $data = array('aid' => $aid, 'teamid' => $owner, 'date' => time(), 'event' => '抵御了一次' . $myteamname . '的宝石攻击');
                                    DB::insert('wanba_logs', $data);
                                    $result = array('msg' => $msg, 'status' => true);
                                    $flag = false;
                                    echo json_encode($result);
                                } else {
                                    $flag = true;
                                }
                            }
                        } else {
                            $flag = true;
                        }
                        if ($flag) {
                            //改变该地块的占有者
                            $arr = array('owner' => $myteamid);
                            DB::update('wanba_task', $arr, "aid=$aid and taskid=$taskid");
                            //写pass通关表
                            $isExist = DB::fetch_first("SELECT id FROM `wf_wanba_pass`  where  taskid=$taskid and aid=$aid and teamid=$myteamid");
                            if ($isExist) {

                                $data = array('pass' => 2, 'date' => time());
                                DB::update('wanba_pass', $data, " id = $isExist[id] ");
                            } else {
                                $data = array('aid' => $aid, 'pass' => 2, 'teamid' => $myteamid, 'taskid' => $taskid, 'date' => time());
                                DB::insert('wanba_pass', $data);
                            }
                            //插入公告
                            $data = array('aid' => $aid, 'teamid' => $myteamid, 'status' => 1, 'date' => time(), 'event' => '江湖公告：' . $myteamname . '使用了力量宝石，抢夺了' . $landid . '号地块');
                            DB::insert('wanba_logs', $data);
                            $msg = '成功抢夺了' . $landid . '号地块';
                            $result = array('msg' => $msg, 'status' => true);
                            echo json_encode($result);
                        }
                        break;
                }
            } else {
                $msg = '宝石数量不足';
                $result = array('msg' => $msg, 'status' => true);
                echo json_encode($result);
                exit();
            }
        }
        break;

    case 'sysInitTest':
        $aid = $arr['aid'];
        $mode = $arr['mode'];
        $sql = "update `wf_wanba_team_setting`  set stone1=0,stone2=0,stone3=0,stone4=0,stone5=0,stone6=0,godview=0  where aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_pass` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_logs` WHERE aid=$aid";
        DB::query($sql);
        deldir('./upload/' . $aid . '/');
        $sql = "delete FROM `wf_wanba_log_pic` WHERE aid=$aid";
        DB::query($sql);
        // $sql = "delete FROM `wf_wanba_album`  where aid=$aid";
        // DB::query($sql);
        DB::delete("wanba_album", "aid=$aid");
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0,pvalue=300  where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        DB::query($sql);

        if ($mode == -1) {
            //拷贝现有任务设置和图片到task_bak表
            $tasks = DB::fetch_all("select taskid, `aid`, `name`, `memo`, `pvalue`, `poi`, `pmemo`, `displayorder`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng` from `wf_wanba_task` where aid=$aid");
            foreach ($tasks as $k => $v) {
                $pics = DB::fetch_all("select url from wf_wanba_pic where taskid=$v[taskid]");
                $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => trim($v['answer']), "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $aid, "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);
                $insert_task_id = DB::insert('wanba_task_bak', $insert, 'taskid');
                foreach ($pics as $ke => $va) {
                    $u = array("url" => $va['url'], 'taskbakid' => $insert_task_id);
                    DB::insert('wanba_pic', $u);
                }
                DB::delete('wanba_pic', "taskid=$v[taskid]");
            }
            //删除现有任务表
            DB::delete("wanba_task", "aid=$aid");
            //从demo里取任务写入
            $tasks_demo = DB::fetch_all("select * from wf_wanba_task where aid=1");
            foreach ($tasks_demo as $k => $v) {
                $pics = DB::fetch_all("select url from wf_wanba_pic where taskid=$v[taskid]");
                $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => $v['answer'], "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $aid, "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);

                $insert_task_id = DB::insert('wanba_task', $insert, 'taskid');
                foreach ($pics as $ke => $va) {
                    $u = array("url" => $va['url'], 'taskid' => $insert_task_id);
                    DB::insert('wanba_pic', $u);
                }
            }
        } elseif ($mode >= 0 && $mode <= 1) {
            $oldmode = DB::result_first("select mode from wf_wanba_act where aid=$aid");
            if ($oldmode == -1) {
                //删除现有设置
                $tasks = DB::fetch_all("select taskid, `aid`, `name`, `memo`, `pvalue`, `poi`, `pmemo`, `displayorder`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng` from `wf_wanba_task_bak` where aid=$aid");
                foreach ($tasks as $ke => $va) {
                    DB::delete("wanba_pic", "taskid=$va[taskid]");
                }
                DB::delete("wanba_task", "aid=$aid");
                //从task_bak表取出设置写入
                $tasks_bak = DB::fetch_all("select taskid, `aid`, `name`, `memo`, `pvalue`, `poi`, `pmemo`, `displayorder`, `qtype`, `answer`, `ptype`, `owner`, `gps`, `open`, `mine`, `url`, `media`, `latlng` from `wf_wanba_task_bak` where aid=$aid");
                foreach ($tasks_bak as $k => $v) {
                    //DB::insert("wanba_task_bak",$v);
                    $pics = DB::fetch_all("select url from wf_wanba_pic where taskbakid=$v[taskid]");
                    $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => $v['answer'], "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $aid, "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);

                    $insert_task_id = DB::insert('wanba_task', $insert, 'taskid');
                    foreach ($pics as $ke => $va) {
                        $u = array("url" => $va['url'], 'taskid' => $insert_task_id);
                        DB::insert('wanba_pic', $u);
                    }
                }

                DB::delete("wanba_task_bak", "aid=$aid");
            }
        }
        $sql = "update  `wf_wanba_act` set `mode`=$mode,`status`=-1  where `aid`=$aid";
        DB::query($sql);
        $result = array('msg' => '操作成功', 'status' => true);
        echo json_encode($result);
        break;
    case 'sysInit':
        $aid = $arr['aid'];
        $mode = $arr['mode'];
        $sql = "update `wf_wanba_team_setting`  set stone1=0,stone2=0,stone3=0,stone4=0,stone5=0,stone6=0,godview=0  where aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_pass` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_logs` WHERE aid=$aid";
        DB::query($sql);
        deldir('./upload/' . $aid . '/');
        $sql = "delete FROM `wf_wanba_log_pic` WHERE aid=$aid";
        DB::query($sql);
        // $sql = "delete FROM `wf_wanba_album`  where aid=$aid";
        // DB::query($sql);
        DB::delete("wanba_album", "aid=$aid");
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0,pvalue=300  where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_act` set `mode`=$mode,`status`=-1  where `aid`=$aid";
        DB::query($sql);

        $result = array('msg' => '操作成功', 'status' => true);
        echo json_encode($result);
        break;
    case 'convertLatLng':
        $taskid = $arr['taskid'];
        $latlng = $arr['latlng'];
        $sql = "update `wf_wanba_task`  set latlng='" . $latlng . "'  where taskid=$taskid";
        DB::query($sql);
        break;
    case 'getWanba':
        $myteam = $arr['myteam'];
        $myteamname = $arr['myteamname'];
        $aid = $arr['aid'];

        //公告
        $data = array('aid' => $aid, 'status' => 1, 'date' => time(), 'event' => '江湖惊现玩霸');
        DB::insert('wanba_logs', $data);
        $stones = DB::fetch_first("select stone1,stone2,stone3,stone4,stone5,stone6,stone7  from wf_wanba_team_setting where aid=$aid and displayorder=$myteam");
        $stone1 = $stones['stone1'] - 1;
        $stone2 = $stones['stone2'] - 1;
        $stone3 = $stones['stone3'] - 1;
        $stone4 = $stones['stone4'] - 1;
        $stone5 = $stones['stone5'] - 1;
        $stone6 = $stones['stone6'] - 1;
        $stone7 = $stones['stone7'] - 1;
        $data = array('stone1' => $stone1, 'stone2' => $stone2, 'stone3' => $stone3, 'stone4' => $stone4, 'stone5' => $stone5, 'stone6' => $stone6, 'stone7' => $stone7);
        DB::update('wanba_team_setting', $data, "aid=$aid and displayorder=$myteam");

        $data = DB::fetch_all("SELECT sum(score) score,teamid  FROM `wf_wanba_logs` WHERE aid=$aid and teamid>0 group by teamid order by score desc");
        $total = 0;
        foreach ($data as $k => $v) {
            $team = DB::fetch_first("select name,color from `wf_wanba_team_setting` where aid=$aid and displayorder=$v[teamid]");
            // $data[$k]['name'] = $team['name'];
            // $data[$k]['color'] = $team['color'];
            if ($v[teamid] != $myteam) {
                if ($v['score'] > 0) {
                    $score = intval($v['score'] / 2);
                    $total += $score;
                    $lost = 0 - $score;
                    $data = array('aid' => $aid, 'status' => 0, 'teamid' => $v[teamid], 'score' => $lost, 'date' => time(), 'event' => $myteamname . '合成了玩霸，掠夺了你一半的财富，计' . $score);
                    DB::insert('wanba_logs', $data);
                }
            }
        }

        $data = array('aid' => $aid, 'status' => 0, 'teamid' => $myteam, 'score' => $total, 'date' => time(), 'event' => '成功使用了玩霸，掠夺了其他帮派财富共计' . $total);
        DB::insert('wanba_logs', $data);
        $result = array('msg' => '恭喜你使用玩霸抢夺了财富' . $total, 'status' => true);
        echo json_encode($result);
        break;
    case 'batchMakeStone':
        $aid = $arr['aid'];
        $stones = $arr['stones'];
        $stone1 = $stones['stone1'];
        $stone2 = $stones['stone2'];
        $stone3 = $stones['stone3'];
        $stone4 = $stones['stone4'];
        $stone5 = $stones['stone5'];
        $stone6 = $stones['stone6'];
        $stone7 = $stones['stone7'];
        $n = 0;
        if (intval($stone1) > 0) {
            for ($i = 0; $i < $stone1; $i++) {
                $data = array('type' => 1, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone2) > 0) {
            for ($i = 0; $i < $stone2; $i++) {
                $data = array('type' => 2, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone3) > 0) {
            for ($i = 0; $i < $stone3; $i++) {
                $data = array('type' => 3, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone4) > 0) {
            for ($i = 0; $i < $stone4; $i++) {
                $data = array('type' => 4, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone5) > 0) {
            for ($i = 0; $i < $stone5; $i++) {
                $data = array('type' => 5, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone6) > 0) {
            for ($i = 0; $i < $stone6; $i++) {
                $data = array('type' => 6, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        if (intval($stone7) > 0) {
            for ($i = 0; $i < $stone7; $i++) {
                $data = array('type' => 7, 'aid' => $aid, 'token' => getRandomString());
                DB::insert('wanba_stone_list', $data);
                $n++;
            }
        }
        $event = '成功生成了' . $n . '颗宝石';
        $data = array('aid' => $aid, 'status' => 2, 'date' => time(), 'event' => $event);

        $id = DB::insert('wanba_logs', $data, 'id');
        $result = array('status' => true, 'msg' => $event);
        echo json_encode($result);
        break;
    case 'downPhotos':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $dir = 'upload/' . $aid . '/';
        mkDirs($dir);
        $zipfilename = $dir . 'photos_' . $aid . ".zip";
        @unlink($zipfilename);
        $zip = new ZipArchive();
        if ($zip->open($zipfilename, ZIPARCHIVE::CREATE) !== true) {
            exit('无法打开文件，或者文件创建失败');
        }
        $path = realpath('./upload/' . $aid . '/');
        $result = getDir($path);
        foreach ($result as $k => $v) {
            $zip->addFile($v, basename($v));
        }
        $zip->close(); // 关闭
        if ($server == "www.wondfun.com") {
            $downurl = "http://www.wondfun.com/wanba/api/" . $zipfilename;
        } else {
            $downurl = "http://www.wondball.com/wanba/api/" . $zipfilename;
        }


        echo json_encode($downurl);
        break;
    case 'downStones':
        include 'phpqrcode.php';
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $data = DB::fetch_all("select `type`,aid,token from wf_wanba_stone_list where aid=$aid");
        $dir = 'stones/' . $aid . '/';
        mkDirs($dir);
        $zipfilename = $dir . $aid . ".zip"; // 最终生成的文件名（含路径）
        // 生成文件
        @unlink($zipfilename);
        $zip = new ZipArchive();
        if ($zip->open($zipfilename, ZIPARCHIVE::CREATE) !== true) {
            exit('无法打开文件，或者文件创建失败');
        }



        foreach ($data as $k => $v) {
            $str = 't=' . $v['type'] . '&t=' . $v[aid] . '&t=' . $v['token'];
            $filename = 'stone_' . $v['type'] . '_' . $k . '.png';
            $path = $dir . $filename;
            $newfile = fopen($dir . $filename, "w") or die("Unable to open file!");
            QRcode::png($str, $newfile, 'L', 4);
            $dt = array('path' => $path);
            DB::update('wanba_stone_list', $dt, "`type`=$v[type] and aid=$aid and token='" . $v['token'] . "'");
            $zip->addFile($path, basename($path));
        }
        $zip->close(); // 关闭
        if ($server == "www.wondfun.com") {
            $downurl = "http://www.wondfun.com/wanba/api/" . $zipfilename;
        } else {
            $downurl = "http://www.wondball.com/wanba/api/" . $zipfilename;
        }
        echo json_encode($downurl);
        break;
    case 'stoneState':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $stone1 = $stone2 = $stone3 = $stone4 = $stone5 = $stone6 = 0;
        //活动的宝石模式设置
        $stonemode = DB::result_first("select stone_mode from wf_wanba_act where aid=$aid");
        //宝石池情况
        $stones = DB::fetch_all("select `type` from wf_wanba_stone_list where aid=$aid");
        foreach ($stones as $k => $v) {
            if ($v['type'] == 1) {
                $stone1 += 1;
            }
            if ($v['type'] == 2) {
                $stone2 += 1;
            }
            if ($v['type'] == 3) {
                $stone3 += 1;
            }
            if ($v['type'] == 4) {
                $stone4 += 1;
            }
            if ($v['type'] == 5) {
                $stone5 += 1;
            }
            if ($v['type'] == 6) {
                $stone6 += 1;
            }
            if ($v['type'] == 7) {
                $stone7 += 1;
            }
        }
        $stonesleft = array('total' => count($stones), 'stone1' => $stone1, 'stone2' => $stone2, 'stone3' => $stone3, 'stone4' => $stone4, 'stone5' => $stone5, 'stone6' => $stone6, 'stone7' => $stone7);
        //宝石生成记录
        $stonesMadehistory = DB::fetch_all("select from_unixtime(date, '%H:%i:%s') date, event from wf_wanba_logs where aid=$aid and status=2 order by date desc");
        //各队的宝石情况
        $teamStones = DB::fetch_all("select displayorder,name,stone1,stone2,stone3,stone4,stone5,stone6,stone7 from wf_wanba_team_setting where aid=$aid");
        foreach ($teamStones as $k => $v) {
            $teamStones[$k]['unused'] = $v['stone1'] + $v['stone2'] + $v['stone3'] + $v['stone4'] + $v['stone5'] + $v['stone6'] + $v['stone7'];
            //$teamStones[$k]['detail']=DB::fetch_all("select * from `wf_wanba_logs`  where aid=$aid and ((teamid=$v[displayorder] and status=0) or status=1) and event like '%使用了__宝石%'");
            $teamStones[$k]['detail'] = DB::fetch_all("select from_unixtime(date, '%H:%i:%s') date, score, memo, event, id,status from `wf_wanba_logs`  where aid=$aid and ((teamid=$v[displayorder] and status=0) or status=1) and event like '%使用了__宝石%'");
        }
        $result = array('stonesleft' => $stonesleft, 'teamStones' => $teamStones, 'stonesMadehistory' => $stonesMadehistory, 'stonemode' => $stonemode);

        echo json_encode($result);
        break;
    case 'searchNick':
        $nick = $arr['nick'];
        $data = DB::fetch_all("select openid,nick from wf_wanba_user where nick like '%" . $nick . "%'");
        echo json_encode($data);
        break;
    case 'promoteManager':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $data = array('creator' => $openid);
        $hasUser = DB::fetch_first("select openid from wf_wanba_user where openid='" . $openid . "'");
        if ($hasUser) {
            $n = DB::update('wanba_act', $data, "aid=$aid");
            if ($n == 1) {
                $result = array("status" => true, "msg" => "恭喜你成为了管理员");
            } else {
                $result = array("status" => false, "msg" => "啊哦，出现了意外，你未获得管理员权限");
            }
        } else {
            $result = array("status" => false, "msg" => "啊哦，出现了意外，你未获得管理员权限");
        }

        echo json_encode($result);
        break;
    case 'shiftManager':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $data = array('creator' => $openid);
        DB::update('wanba_act', $data, "aid=$aid");
        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;
    case 'pcMyActData':
        $openid = $arr['openid'];

        $actNow = DB::fetch_all("SELECT from_unixtime(date, '%Y-%m-%d') date,aid,title,status,sharepic FROM `wf_wanba_act` where status<5 and template=0 and md5(creator)='" . $openid . "' order by aid desc");
        $actFinished = DB::fetch_all("SELECT  from_unixtime(date, '%Y-%m-%d') date,aid,title,status,sharepic FROM `wf_wanba_act` where status=5 and template=0 and creator='" . $openid . "' order by date desc");
        $result = array('swiper' => $swiper, 'actNow' => $actNow, 'actFinished' => $actFinished);
        echo json_encode($result);
        break;
    case 'myActData':
        $openid = $arr['openid'];
        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;

        $actNow = DB::fetch_all("SELECT from_unixtime(date, '%Y-%m-%d') date,aid,title,status,sharepic FROM `wf_wanba_act` where ((status<5 and mode=2) or mode<2) and template=0 and creator='" . $openid . "' order by aid desc" . $limit);
        foreach ($actNow as $k => $v) {
            $q = DB::fetch_first("select status  from wf_wanba_routeapply where aid=$v[aid] and applyopenid='" . $openid . "'");
            $actNow[$k]['applystatus'] = ($q) ? $q['status'] : -2;
        }
        $c = DB::fetch_first("SELECT count(aid) c FROM `wf_wanba_act` where status<5 and template=0 and creator='" . $openid . "' order by aid desc");
        $actFinished = DB::fetch_all("SELECT  from_unixtime(date, '%Y-%m-%d') date,aid,title,status,sharepic FROM `wf_wanba_act` where status=5 and mode=2 and template=0 and creator='" . $openid . "' order by date desc " . $limit);
        foreach ($actFinished as $k => $v) {
            $q = DB::fetch_first("select status  from wf_wanba_routeapply where aid=$v[aid] and applyopenid='" . $openid . "'");
            $actFinished[$k]['applystatus'] = ($q) ? $q['status'] : -2;
        }
        $cc = DB::fetch_first("SELECT  count(aid) c FROM `wf_wanba_act` where status=5 and template=0 and creator='" . $openid . "' order by date desc ");
        $result = array('swiper' => $swiper, 'actNow' => $actNow, 'actFinished' => $actFinished, 'actNowTotal' => $c['c'], 'actFinishedTotal' => $cc['c']);
        echo json_encode($result);
        break;
    case 'getMyQuestionlist':
        $openid = $arr['openid'];



        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;

        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;
        $list = DB::fetch_all("SELECT `questionid`, `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` FROM `wf_wanba_question` where sys=0 and creator= '" . $openid . "' order by questionid desc " . $limit);
        foreach ($list as $k => $v) {
            $q = DB::fetch_first("select status from wf_wanba_questionapply where qid=$v[questionid] and applyopenid= '" . $openid . "'");
            $list[$k]['applystatus'] = ($q) ? $q['status'] : -2;
        }
        $c = DB::fetch_first("SELECT count(questionid) c FROM `wf_wanba_question` where sys=0 and creator= '" . $openid . "'");
        $result = array('swiper' => $swiper, 'list' => $list,  'total' => $c['c']);
        echo json_encode($result);
        break;
    case 'myQuestionData':
        $openid = $arr['openid'];
        $where1 =  " WHERE (sys=1) ";
        $where2 =  " WHERE (sys=0  and creator='" . $openid . "') ";
        $order = ($arr['order']) ? $arr['order'] : 0;
        $order = ($order == 0) ? ' order by questionid desc ' : ' order by questionid desc ';


        $cat = $arr['cat'];
        if (count($cat) == 1) {
            $where1 .= " and (cat like '%" . $cat[0] . "%') ";
            $where2 .= " and (cat like '%" . $cat[0] . "%') ";
        } elseif (count($cat) > 1) {
            $before = " and  (";
            $s = ' ';
            foreach ($cat as $k => $v) {
                if ($k == 0) {
                    $s .= "cat like '%" . $v . "%'";
                } else {
                    $s .= " or cat like '%" . $v . "%'";
                }
            }
            $after = ") ";
            $where1 .= $before . $s . $after;
            $where2 .= $before . $s . $after;
        }

        $key = ($arr['key']) ? $arr['key'] : '';
        if ($key != '') {
            $where1 .= " and (memo like '%" . $key . "%') ";
            $where2 .= " and (memo like '%" . $key . "%') ";
        }


        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;
        //$pagesize = 1000;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;


        $list = DB::fetch_all("SELECT `questionid`, `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` FROM `wf_wanba_question` " . $where2 . $order . $limit);
        $c = DB::fetch_first("SELECT count(questionid) c FROM `wf_wanba_question` " . $where2);
        if ($c) {
            $mytotal = $c['c'];
        } else {
            $mytotal = 0;
        }

        foreach ($list as $k => $v) {
            $list[$k]['pics'] = DB::fetch_all("select * from wf_wanba_question_pic where questionid=$v[questionid]");
        }
        $syslist = DB::fetch_all("SELECT `questionid`, `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` FROM `wf_wanba_question` " . $where1 . $order . $limit);
        $c = DB::fetch_first("SELECT count(questionid) c FROM `wf_wanba_question` " . $where1);
        if ($c) {
            $systotal = $c['c'];
        } else {
            $systotal = 0;
        }
        foreach ($syslist as $k => $v) {
            $syslist[$k]['pics'] = DB::fetch_all("select * from wf_wanba_question_pic where questionid=$v[questionid]");
        }
        $catlist = DB::fetch_all("SELECT cat FROM `wf_wanba_question`  where `sys`=1 and cat<>''");
        $temp = array();
        foreach ($catlist as $k => $v) {
            $temp[] = $v['cat'];
        }
        $cat = implode('|', $temp);
        $catlist = explode('|', $cat);
        $catlist = array_count_values($catlist);
        arsort($catlist);
        $t = array();
        foreach ($catlist as $k => $v) {
            $t[] = $k;
        }
        $result = array('swiper' => $swiper, 'list' => $list, 'syslist' => $syslist, 'cats' => $t, 'systotal' => $systotal, 'mytotal' => $mytotal);
        echo json_encode($result);
        break;

    case 'buyRoute':
        $tid = $arr['tid'];
        $openid = $arr['openid'];
        $actdata = $arr['actdata'];
        $cat = $actdata['cat'];
        $teamnum = $actdata['teamNum'];
        $data = array('creator' => $openid, 'title' => $actdata['title'], 'date' => strtotime($actdata['date']), 'teamnum' => $teamnum, 'cat' => $actdata['cat'], 'bindtemplateid' => $tid);
        $id = DB::insert('wanba_act', $data, 'aid');
        $teamsetting = DB::fetch_all("SELECT `id`, `aid`, `displayorder`, `name`, `desc`, `pic`, `color`  from wf_wanba_team_setting where aid=1 order by displayorder");
        if ($id) {

            for ($i = 0; $i < $teamnum; $i++) {
                $dt = $teamsetting[$i];
                $d = array('aid' => $id, 'displayorder' => $dt['displayorder'], 'name' => $dt['name'], 'desc' => $dt['desc'], 'pic' => $dt['pic'], 'color' => $dt['color']);

                DB::insert('wanba_team_setting', $d);
            }
            //删除原有设置
            $oldtasks = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$id order by displayorder");
            foreach ($oldtasks as $k => $v) {
                DB::delete('wanba_pic', "taskid=$v[taskid]");
            }
            DB::delete('wanba_task', "aid=$id");
            //新设置
            $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$tid order by displayorder");
            foreach ($task as $k => $v) {
                $dt = array('aid' => $id, 'name' => $v['name'], 'memo' => $v['memo'],  'poi' => $v['poi'], 'pmemo' => $v['pmemo'], 'displayorder' => $v['displayorder'], 'qtype' => $v['qtype'], 'answer' => $v['answer'], 'ptype' => $v['ptype'],  'url' => $v['url'], 'media' => $v['media'], 'latlng' => $v['latlng']);
                $newtaskid = DB::insert('wanba_task', $dt, 'taskid');
                $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
                foreach ($task[$k]['pics'] as $ke => $va) {
                    $dp = array('taskid' => $newtaskid, 'url' => $va['url'], 'displayorder' => $va['displayorder']);
                    DB::insert('wanba_pic', $dp);
                }
            }
            //设置点位类型
            $cat = DB::fetch_first("select cat from wf_wanba_act where aid=$id");
            if ($cat) {
                if ($cat['cat'] == 1) {
                    DB::query("update wf_wanba_task set ptype=1 where aid=$id and displayorder<>25");
                    DB::query("update wf_wanba_task set ptype=2 where aid=$id and displayorder=25");
                } elseif ($cat['cat'] == 0) {
                    DB::query("update wf_wanba_task set ptype=2 where aid=$id and displayorder=25");
                    DB::query("update wf_wanba_task set ptype=0 where aid=$id and displayorder in(2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)");
                    DB::query("update wf_wanba_task set ptype=1 where aid=$id and displayorder in(1,7,9,13,17,19,31,33,37,41,43,49)");
                }
            }
            //写入订单数据
            $order = $arr['order'];
            if ($order) {
                $tid = $order[eventid];
                $route = DB::result_first("select title from wf_wanba_act where aid=$tid");
                $memo = "购买编号为" . $tid . "的线路" . $route;
                $data = array('orderno' => $order['orderno'], 'openid' => $openid, 'date' => time(), 'amount' => $order['amount'], 'eventid' => $id, 'eventtype' => $order['eventtype'], 'memo' => $memo);
                DB::insert('wanba_order', $data);
            }
            //给被购买的线路模板加上计数
            DB::query("update wf_wanba_act set buy_count=buy_count+1 where aid=$tid");
            $result = array('status' => true, 'msg' => '恭喜你成功创建了一个活动，你可以继续设置完善。',  'aid' => $id);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'newAct':
        $openid = $arr['openid'];
        $actdata = $arr['actdata'];
        $cat = $actdata['cat'];
        $teamnum = $actdata['teamNum'];
        if ($actdata['themeId']) {
            $themeid = intval($actdata['themeId']);
        } else {
            $themeid = 1;
        }
        $themid = ($themeid > 0) ? $themeid : 1;
        $data = array('creator' => $openid, 'title' => $actdata['title'], 'date' => strtotime($actdata['date']), 'teamnum' => $teamnum, 'teamThemeId' => $themeid, 'cat' => $actdata['cat']);
        $id = DB::insert('wanba_act', $data, 'aid');
        $teamsetting = DB::fetch_all("SELECT  `displayorder`, `name`, `desc`, `pic`, `color`  from wf_wanba_team_theme_list where themeid=$themeid order by displayorder");
        if ($id) {
            for ($i = 0; $i < $teamnum; $i++) {
                $dt = $teamsetting[$i];
                $d = array('aid' => $id, 'displayorder' => $dt['displayorder'], 'name' => $dt['name'], 'desc' => $dt['desc'], 'pic' => $dt['pic'], 'color' => $dt['color'], 'themeid' => $themeid);

                DB::insert('wanba_team_setting', $d);
            }
            //更新活动分享状态 ，更新用户账户点数,写日志
            $fromid = $arr['fromid'];
            $point = $arr['point'];
            $tradeno = $arr['tradeno'];
            $tel = $arr['tel'] ? $arr['tel'] : '';
            if ($fromid && $point && $tradeno && $tel) {
                DB::query("update wf_wanba_act set isshared=1 where aid=$id");
                DB::query("update wf_wanba_user set point=point+$point,tel='" . $tel . "' where openid='" . $openid . "'");
                DB::query("update wf_wanba_user set point=point+$point where openid='" . $fromid . "'");
                $log = array('openid' => $openid, 'fromid' => $fromid, 'date' => time(), 'tradeno' => $tradeno, 'event' => '使用99元折扣卡创建活动，获得999玩点赠送');
                DB::insert('wanba_cash_logs', $log);
                $log = array('openid' => $fromid, 'fromid' => $openid, 'date' => time(), 'event' => '您朋友使用您赠送的99元折扣卡创建活动，您因此获得999玩点赠送');
                DB::insert('wanba_cash_logs', $log);
            }
            $list = DB::fetch_all("SELECT * FROM `wf_wanba_act` where status<5 and creator='" . $openid . "' order by aid desc");
            $result = array('status' => true, 'msg' => '恭喜你成功创建了一个活动，你可以继续设置完善。', 'list' => $list, 'aid' => $id);
            //写入订单数据
            $order = $arr['order'];
            if ($order) {
                $memo = "使用99折扣卡创建活动";
                $data = array('orderno' => $order['orderno'], 'openid' => $openid, 'date' => time(), 'amount' => $order['amount'], 'eventid' => $id, 'eventtype' => $order['eventtype'], 'memo' => $memo);
                DB::insert('wanba_order', $data);
            }
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'editAct':

        $openid = $arr['openid'];
        $actdata = $arr['actdata'];
        $aid = $actdata['aid'];
        $teamnum = $actdata['teamNum'];
        $teamnum = ($teamnum <= 0) ? 1 : $teamnum;
        if ($actdata['themeId']) {
            $themeid = intval($actdata['themeId']);
        } else {
            $themeid = 1;
        }
        $themid = ($themeid > 0) ? $themeid : 1;
        $cat = $actdata['cat'];
        $pic = $actdata['pic'];
        $piclogo = $actdata['picLogo'];
        if ($pic) {
            $ft = strrpos($pic, '.', 0);
            $fp = strrpos($pic, '/', 0);
            $fm = substr($pic, $fp + 1, $ft);
            $fe = substr($pic, $ft);
            $a = explode('?', $fm);
            $sharepic = $a[0];
        } else {
            $sharepic = '1.jpg';
        }
        if ($piclogo) {
            $ft = strrpos($piclogo, '.', 0);
            $fp = strrpos($piclogo, '/', 0);
            $fm = substr($piclogo, $fp + 1, $ft);
            $fe = substr($piclogo, $ft);
            $a = explode('?', $fm);
            $piclogo = $a[0];
        } else {
            $logoepic = 'default.jpg';
        }

        $slogan = $actdata['text'];
        //$slogan=preg_replace('/&/','',$slogan);

        $data = array('title' => $actdata['title'], 'date' => strtotime($actdata['date']), 'teamnum' => $teamnum, 'teamThemeId' => $themeid, 'sharepic' => $sharepic, 'logopic' => $piclogo, 'slogan' => $slogan, 'cat' => $actdata['cat']);
        //更新活动
        DB::update('wanba_act', $data, "aid=$aid");

        //根据cat来更新点位类型
        if ($cat == 1) {
            DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder<>25");
            DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
        } elseif ($cat == 0) {
            DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
            DB::query("update wf_wanba_task set ptype=0 where aid=$aid and displayorder in(2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)");
            DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder in(1,7,9,13,17,19,31,33,37,41,43,49)");
        }

        $oldteamnum = DB::result_first("select teamNum from wf_wanba_act where aid=$aid");
        $teamnum = ($teamnum >= $oldteamnum) ? $teamnum : $oldteamnum;
        if ($aid > 1) {
            //删除旧队伍设置
            DB::delete('wanba_team_setting', "aid=$aid");
            //更新队伍设置
            $teamsetting = DB::fetch_all("SELECT  `displayorder`, `name`, `desc`, `pic`, `color`  from `wf_wanba_team_theme_list`  where themeid=$themeid order by displayorder");

            for ($i = 0; $i < $teamnum; $i++) {
                $dt = $teamsetting[$i];
                $d = array('aid' => $aid, 'displayorder' => $dt['displayorder'], 'name' => $dt['name'], 'desc' => $dt['desc'], 'pic' => $dt['pic'], 'color' => $dt['color'], 'themeid' => $themeid);

                DB::insert('wanba_team_setting', $d);
            }
        }
        $list = DB::fetch_all("SELECT * FROM `wf_wanba_act` where status<5 and  template=0 and creator='" . $openid . "' order by aid desc");
        $result = array('status' => true, 'msg' => '操作成功，你可以继续设置完善。', 'list' => $list, 'aid' => $aid);

        echo json_encode($result);
        break;

    case 'newQuestion':
        $openid = $arr['openid'];
        $questiondata = $arr['questiondata'];
        $qid = $questiondata['qid'];
        $index = $questiondata['index'];
        if ($qid == 0) {
            $data = array('creator' => $openid, 'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);
            $id = DB::insert('wanba_question', $data, 'questionid');
            if ($id) {
                $data = array('questionid' => $id, 'creator' => $openid, 'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);
                $result = array('status' => true, 'msg' => '操作成功', 'questionid' => $id);
            } else {
                $result = array('status' => false, 'msg' => '操作失败');
            }
        } else {
            $data = array('memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);
            // $data = array('creator' => $openid, 'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);
            $id = DB::update('wanba_question', $data, "questionid=$qid");
            if ($id) {
                $data = array('questionid' => $qid,  'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);

                $result = array('status' => true, 'msg' => '操作成功', 'questionid' => $qid);
            } else {
                $result = array('status' => false, 'msg' => '操作失败');
            }
        }

        echo json_encode($result);
        break;
    case 'delQustionPic':
        $index = $arr['index'];
        $openid = $arr['openid'];
        $picurl = $arr['picurl'];
        $questionid = $arr['questionid'];
        $n = DB::delete('wanba_question_pic', "questionid=$questionid and url='" . $picurl . "'");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'index' => $index);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'delPointPic':
        $index = $arr['index'];
        $openid = $arr['openid'];
        $picurl = $arr['picurl'];
        $pointid = $arr['pointid'];
        $n = DB::delete('wanba_point_pic', "pointid=$pointid and url='" . $picurl . "'");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'index' => $index);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'actSetting':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");

        foreach ($task as $k => $v) {
            $task[$k]['pics'] = DB::fetch_all("select DISTINCT url from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
        }
        $act = DB::fetch_first("SELECT `aid`, `title`, `date`, `creator`, `buy_count`, `coach`, `offset`, `gpsEnabled`, `ticket`, `map`, `uploadPhotoSetting`, `status`, `minenum`, `minevalue`, `pvalue`,`pvalue1`,`pvalue2`,`pvalue3`, `werun`, `werunvar`, `mode`, `teamThemeId`, `stonerandom`, `sharepic`, `canlook`, `mapkey`, `diskey`, `teamNum`, `template`, `isshared`, `logopic`, `redbagtotal`, `redbagrand`, `slogan`, `openarea`, `route_desc`, `city`, `tag`, `cat`, `bindtemplateid`, `ai_duration`, `ai_random`, `ai_lasttime`, `stone_mode`, `price`,  from_unixtime(endTime, '%Y-%m-%d %H:%i') endTime FROM  `wf_wanba_act`  where aid=$aid");
        $total = DB::fetch_first("select sum(score) score from wf_wanba_logs where taskid=-1 and aid=$aid");
        $act['redbagsum'] = ($total) ? $total[score] : -1;
        $data = array('task' => $task,  'act' => $act);
        echo json_encode($data);

        break;
        //保存点位任务设置
    case 'savePoi':
        $aid = $arr['aid'] ? $arr['aid'] : 0;
        $openid = $arr['openid'];
        $poiInfo = $arr['poiInfo'];
        $taskid = intval($poiInfo['taskid']);
        $pics = $poiInfo['pics'];
        $displayorder = $poiInfo['displayorder'];
        if ($aid > 0) {
            $cat = DB::result_first("select cat from wf_wanba_act where aid=$aid");
            if ($cat == 2) {
                $ptype = $poiInfo['ptype'];
            } else {
                $p = array(1, 7, 9, 13, 17, 19, 31, 33, 37, 41, 43, 49);
                if (in_array($displayorder, $p)) {
                    $ptype = 1;
                } else if ($displayorder == 25) {
                    $ptype = 2;
                } else {
                    $ptype = 0;
                }
                if ($ptype < 2) {
                    $ptype = $cat == 1 ? 1 : $ptype;
                }
            }
        }

        if ($taskid > 0) {
            //删除原有图片
            DB::delete('wanba_pic', "taskid=$taskid");
            $data = array('pvalue' => $poiInfo['pvalue'], 'tip1' => $poiInfo['tip1'], 'tip2' => $poiInfo['tip2'], 'ptype' => $ptype, 'name' => $poiInfo['name'], 'pmemo' => $poiInfo['pmemo'], 'latlng' => $poiInfo['latlng'], 'poi' => $poiInfo['poi'], 'memo' => $poiInfo['memo'], 'answer' => $poiInfo['answer'], 'qtype' => $poiInfo['qtype'], 'open' => $poiInfo['open'], 'gps' => $poiInfo['gps'], 'url' => $poiInfo['url'], 'media' => $poiInfo['media']);
            $id = DB::update('wanba_task', $data, "taskid=$taskid");
            foreach ($pics as $k => $v) {
                $new = array('url' => $v[url], 'taskid' => $taskid, 'displayorder' => $v[displayorder]);
                DB::insert('wanba_pic', $new);
            }

            $data = DB::fetch_first("SELECT * FROM  `wf_wanba_task`  where taskid=$taskid");
            $data['pics'] = DB::fetch_all("SELECT DISTINCT url FROM  `wf_wanba_pic`  where taskid=$taskid order by picid");
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } else {
            $data = array('pvalue' => $poiInfo['pvalue'], 'tip1' => $poiInfo['tip1'], 'tip2' => $poiInfo['tip2'], 'ptype' => $ptype, 'name' => $poiInfo['name'], 'aid' => $poiInfo['aid'], 'pmemo' => $poiInfo['pmemo'], 'displayorder' => $poiInfo['displayorder'], 'latlng' => $poiInfo['latlng'], 'poi' => $poiInfo['poi'], 'memo' => $poiInfo['memo'], 'answer' => $poiInfo['answer'], 'qtype' => $poiInfo['qtype'], 'url' => $poiInfo['url'], 'media' => $poiInfo['media']);
            $id = DB::insert('wanba_task', $data, 'taskid');
            if ($id) {
                //插入新图片
                foreach ($pics as $k => $v) {
                    $new = array('url' => $v[url], 'taskid' => $id, 'displayorder' => $v[displayorder]);
                    DB::insert('wanba_pic', $new);
                }

                $data = DB::fetch_first("SELECT * FROM  `wf_wanba_task`  where taskid=$id");
                $data['pics'] = DB::fetch_all("SELECT DISTINCT url FROM  `wf_wanba_pic`  where taskid=$id order by picid");
                $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
            } else {
                $result = array('status' => false, 'msg' => '操作失败');
            }
        }
        echo json_encode($result);
        break;
        //删除活动
    case 'delAct':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $n = DB::delete('wanba_act', "aid=$aid and creator='" . $openid . "'");
        $sql = "delete FROM `wf_wanba_team_setting`  where aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_pass` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_logs` WHERE aid=$aid";
        DB::query($sql);
        $sql = "delete FROM `wf_wanba_task`   where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'delQuestion':
        $qid = $arr['qid'];
        $openid = $arr['openid'];
        $n = DB::delete('wanba_question', "questionid=$qid and creator='" . $openid . "'");

        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'importTemplate':
        $aid = ($arr['aid']) ? $arr['aid'] : 0;
        $where = " WHERE (`template`=1) ";
        $bindtid = DB::fetch_first("select bindtemplateid from wf_wanba_act where aid=$aid");
        if ($bindtid) {
            if ($bindtid['bindtemplateid'] > 0) {
                $where .= " and ( aid <> " . $bindtid['bindtemplateid'] . ") ";
            }
        }

        $order = ($arr['order']) ? $arr['order'] : 0;
        $order = ($order == 0) ? ' order by price desc ' : ' order by aid desc ';
        $city = $arr['city'];
        if (count($city) == 1) {
            $where .= " and (city like '%" . $city[0] . "%') ";
        } elseif (count($city) > 1) {
            $before = " and  (";
            $s = ' ';
            foreach ($city as $k => $v) {
                if ($k == 0) {
                    $s .= "city like '%" . $v . "%'";
                } else {
                    $s .= " or city like '%" . $v . "%'";
                }
            }
            $after = ") ";
            $where .= $before . $s . $after;
        }
        $tag = $arr['tag'];
        if (count($tag) == 1) {
            $where .= " and (tag like '%" . $tag[0] . "%') ";
        } elseif (count($tag) > 1) {
            $before = " and  (";
            $s = ' ';
            foreach ($tag as $k => $v) {
                if ($k == 0) {
                    $s .= "tag like '%" . $v . "%'";
                } else {
                    $s .= " or tag like '%" . $v . "%'";
                }
            }
            $after = ") ";
            $where .= $before . $s . $after;
        }

        $key = ($arr['key']) ? $arr['key'] : '';
        if ($key != '') {
            $where .= " and (title like '%" . $key . "%') ";
        }

        $key = ($arr['key']) ? $arr['key'] : '';
        if ($key != '') {
            $where .= " and (title like '%" . $key . "%') ";
        }
        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;
        if ($server == "www.wondfun.com") {
            $url = 'https://www.wondfun.com/wanba/api/routepic/';

            $list = DB::fetch_all("SELECT `aid`, price,`title`,route_desc memo,concat('" . $url . "',sharepic) sharepic FROM `wf_wanba_act` " . $where . $order . $limit);
            $c = DB::fetch_first("SELECT count(aid) c FROM `wf_wanba_act` " . $where);
            if ($c) {
                $total = $c['c'];
            } else {
                $total = 0;
            }
        } else {
            $url = 'https://www.wondball.com/wanba/api/routepic/';
            $list = DB::fetch_all("SELECT `aid`,price, `title`,route_desc memo,concat('" . $url . "',sharepic) sharepic  FROM `wf_wanba_act`  " . $where . $order . $limit);
            $c = DB::fetch_first("SELECT count(aid) c FROM `wf_wanba_act` " . $where);
            if ($c) {
                $total = $c['c'];
            } else {
                $total = 0;
            }
        }
        foreach ($list as $k => $v) {
            $list[$k]['memo'] = unserialize($v['memo']);
        }


        $citylist = DB::fetch_all("SELECT city FROM `wf_wanba_act`  where `template`=1 and city<>''");
        $temp = array();
        foreach ($citylist as $k => $v) {
            $temp[] = $v['city'];
        }
        $city = implode('|', $temp);
        $citylist = explode('|', $city);
        $citylist = array_count_values($citylist);
        arsort($citylist);
        $t = array();
        foreach ($citylist as $k => $v) {
            $t[] = $k;
        }

        $taglist = DB::fetch_all("SELECT tag FROM `wf_wanba_act`  where `template`=1 and tag<>''");
        $temp = array();
        foreach ($taglist as $k => $v) {
            $temp[] = $v['tag'];
        }
        $tag = implode('|', $temp);
        $taglist = explode('|', $tag);
        $taglist = array_count_values($taglist);
        arsort($taglist);
        $tt = array();
        foreach ($taglist as $k => $v) {
            $tt[] = $k;
        }

        $result = array('swiper' => $swiper, 'list' => $list, 'cities' => array_slice($t, 0, 10), 'tags' => $tt, 'total' => $total);
        echo json_encode($result);
        break;
    case 'useTemplate':
        $templateid = $arr['templateid'];
        $aid = $arr['aid'];
        $mode = DB::result_first("select mode from wf_wanba_act where aid=$aid");
        if ($mode < 2) {
            //删除原有设置
            $oldtasks = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
            foreach ($oldtasks as $k => $v) {
                DB::delete('wanba_pic', "taskid=$v[taskid]");
            }
            DB::delete('wanba_task', "aid=$aid");
            //新设置
            $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$templateid order by displayorder");
            foreach ($task as $k => $v) {
                $dt = array('aid' => $aid, 'name' => $v['name'], 'memo' => $v['memo'],  'poi' => $v['poi'], 'pmemo' => $v['pmemo'], 'displayorder' => $v['displayorder'], 'qtype' => $v['qtype'], 'answer' => $v['answer'], 'ptype' => $v['ptype'],  'url' => $v['url'], 'media' => $v['media'], 'latlng' => $v['latlng']);
                $newtaskid = DB::insert('wanba_task', $dt, 'taskid');
                $task[$k]['pics'] = DB::fetch_all("select DISTINCT url from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
                foreach ($task[$k]['pics'] as $ke => $va) {
                    $d = array('taskid' => $newtaskid, 'url' => $va['url'], 'displayorder' => $va['displayorder']);
                    DB::insert('wanba_pic', $d);
                }
            }
            //设置点位类型
            $cat = DB::fetch_first("select cat from wf_wanba_act where aid=$aid");
            if ($cat) {
                if ($cat['cat'] == 1) {
                    DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder<>25");
                    DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
                } elseif ($cat['cat'] == 0) {
                    DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
                    DB::query("update wf_wanba_task set ptype=0 where aid=$aid and displayorder in(2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)");
                    DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder in(1,7,9,13,17,19,31,33,37,41,43,49)");
                }
            }
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败，活动已开始无法再套用线路');
        }
        echo json_encode($result);
        break;
    case 'addMyPos':
        $openid = $arr['openid'];
        $poiInfo = $arr['poiInfo'];

        $data = array('name' => $poiInfo['name'], 'cat' => $poiInfo['cat'], 'address' => $poiInfo['address'], 'pmemo' => $poiInfo['pmemo'], 'latlng' => $poiInfo['latlng'], 'lastpost' => time(), 'creator' => $arr['openid']);
        $id = DB::insert('wanba_point', $data, 'pointid');
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功', 'pointid' => $id);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }

        echo json_encode($result);
        break;
    case 'editMyPos':
        $openid = $arr['openid'];
        $poiInfo = $arr['poiInfo'];
        $pointid = $poiInfo['pointid'];
        $data = array('name' => $poiInfo['name'], 'cat' => $poiInfo['cat'], 'address' => $poiInfo['address'], 'pmemo' => $poiInfo['pmemo'], 'latlng' => $poiInfo['latlng'], 'lastpost' => time(), 'creator' => $arr['openid']);
        $id = DB::update('wanba_point', $data, "pointid=$pointid");
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }

        echo json_encode($result);
        break;
    case 'myPosList':
        $creator = $arr['openid'];
        $cat = DB::fetch_all("SELECT cat FROM `wf_wanba_point` where creator='" . $creator . "'  group by cat");
        foreach ($cat as $k => $v) {
            $q = DB::fetch_first("select  from_unixtime(lastpost, '%Y-%m-%d') lastpost from `wf_wanba_point` where creator='" . $creator . "'  and cat='" . $v['cat'] . "'");
            $cat[$k]['lastpost'] = $q['lastpost'];
            $c = DB::fetch_first("select  count(pointid) num from `wf_wanba_point` where creator='" . $creator . "'  and cat='" . $v['cat'] . "'");
            $cat[$k]['num'] = $c['num'];
            //$cat[$k]['catname']=(mb_strlen($v['cat'])>4) ? substr($v['cat'],0,4).'..':$v['cat'];
            $cat[$k]['catname'] = $v['cat'];
            $cat[$k]['posdata'] = DB::fetch_all("select * from `wf_wanba_point` where cat='" . $v['cat'] . "' and creator='" . $creator . "'");
        }
        foreach ($cat  as $k => $v) {
            $key_arrays[] = $v['lastpost'];
        }
        array_multisort($key_arrays, SORT_DESC, SORT_NUMERIC, $cat);

        $result = array('swiper' => $swiper, 'cat' => $cat);
        echo json_encode($result);
        break;
    case 'updateActWithNewRoute':
        $aid = $arr['aid'];
        $tid = $arr['tid'];
        $uid = $arr['uid'];
        $token = $arr['token'];
        DB::query("update wf_wanba_act set bindtemplateid=$tid where aid=$aid");
        //删除原有设置
        $oldtasks = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($oldtasks as $k => $v) {
            DB::delete('wanba_pic', "taskid=$v[taskid]");
        }
        DB::delete('wanba_task', "aid=$aid");
        //新设置
        $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$tid order by displayorder");
        foreach ($task as $k => $v) {
            $dt = array('aid' => $aid, 'name' => $v['name'], 'memo' => $v['memo'],  'poi' => $v['poi'], 'pmemo' => $v['pmemo'], 'displayorder' => $v['displayorder'], 'qtype' => $v['qtype'], 'answer' => $v['answer'], 'ptype' => $v['ptype'],  'url' => $v['url'], 'media' => $v['media'], 'latlng' => $v['latlng']);
            $newtaskid = DB::insert('wanba_task', $dt, 'taskid');
            $task[$k]['pics'] = DB::fetch_all("select DISTINCT url from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
            foreach ($task[$k]['pics'] as $ke => $va) {
                $d = array('taskid' => $newtaskid, 'url' => $va['url'], 'displayorder' => $va['displayorder']);
                DB::insert('wanba_pic', $d);
            }
        }
        //设置点位类型
        $cat = DB::fetch_first("select cat from wf_wanba_act where aid=$aid");
        if ($cat) {
            if ($cat['cat'] == 1) {
                DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder<>25");
                DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
            } elseif ($cat['cat'] == 0) {
                DB::query("update wf_wanba_task set ptype=2 where aid=$aid and displayorder=25");
                DB::query("update wf_wanba_task set ptype=0 where aid=$aid and displayorder in(2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)");
                DB::query("update wf_wanba_task set ptype=1 where aid=$aid and displayorder in(1,7,9,13,17,19,31,33,37,41,43,49)");
            }
        }
        //写入订单数据
        $order = $arr['order'];
        if ($order) {

            $route = DB::result_first("select title from wf_wanba_act where aid=$tid");
            $memo = "购买编号为" . $tid . "的线路" . $route;
            $data = array('orderno' => $order['orderno'], 'openid' => $order['openid'], 'date' => time(), 'amount' => $order['amount'], 'eventid' => $aid, 'eventtype' => $order['eventtype'], 'memo' => $memo);
            DB::insert('wanba_order', $data);
        }
        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;
    case 'getMyPos':
        $creator = $arr['openid'];
        $list = DB::fetch_all("SELECT `address`, `pmemo`, `poi`, `latlng`, `name`  FROM `wf_wanba_point` where creator='" . $creator . "' order by lastpost desc");

        $result = array('swiper' => $swiper, 'list' => $list);
        echo json_encode($result);
        break;

    case 'getCatList':
        $creator = $arr['openid'];
        $cat = $arr['cat'];
        $list = DB::fetch_all("SELECT `pointid`, `pmemo`, `poi`, `latlng`, `name`, `creator`, `type`, `cat`, `address`, from_unixtime(lastpost, '%Y-%m-%d') lastpost FROM `wf_wanba_point` p where creator='" . $creator . "'  and cat='" . $cat . "' order by pointid desc");
        foreach ($list as $k => $v) {
            $list[$k]['pics'] = DB::fetch_all("select url,picid from wf_wanba_point_pic where pointid=$v[pointid]");
        }
        $allcats = DB::fetch_all("SELECT cat FROM `wf_wanba_point` WHERE creator='" . $creator . "' group by cat");

        $result = array('swiper' => $swiper, 'list' => $list, 'allcats' => $allcats);
        echo json_encode($result);
        break;
    case 'getAllCats':
        $creator = $arr['openid'];
        $allcats = DB::fetch_all("SELECT cat FROM `wf_wanba_point` WHERE creator='" . $creator . "' group by cat");
        $result = array('allcats' => $allcats);
        echo json_encode($result);
        break;
    case 'delPos':
        $pointid = $arr['pointid'];
        $openid = $arr['openid'];
        $n = DB::delete('wanba_point', "pointid=$pointid and creator='" . $openid . "'");

        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'updateActPic':
        $aid = $arr['aid'];
        $data = array('sharepic' => $aid . '.jpg');
        $n = DB::update('wanba_act', $data, "aid=$aid");
        echo json_encode($n);
        break;
    case 'updateLogoPic':
        $aid = $arr['aid'];
        $data = array('logopic' => $aid . '.jpg');
        $n = DB::update('wanba_act', $data, "aid=$aid");
        echo json_encode($n);
        break;
    case 'getQuestionPics':
        $qid = $arr['qid'];
        $p = DB::fetch_all("select * from wf_wanba_question_pic where questionid=$qid");
        $v = DB::fetch_first("select * from wf_wanba_question_video where questionid=$qid order by picid desc");
        $result = array('pics' => $p, 'video' => $v);
        echo json_encode($result);
        break;
    case 'myPayInfo':
        $openid = $arr['openid'];
        $data = DB::fetch_first("select point,memberid from wf_wanba_user where openid='" . $openid . "'");
        //$data['partyrole'] = DB::result_first("select role from wf_tmall_party_config");
        echo json_encode($data);
        break;

    case 'updateMyPay':
        $openid = $arr['openid'];
        $point = $arr['point'];
        $oldpoint = DB::result_first("select point from wf_wanba_user where openid='" . $openid . "'");
        $newpoint = ($oldpoint) ?  $oldpoint + $point : $point;
        $dt = array('point' => $newpoint);
        DB::update('wanba_user', $dt, "openid='" . $openid . "'");
        echo json_encode($dt);
        break;
    case 'activateMember':
        $openid = $arr['openid'];

        $memberid = 'W' . time() . rand('100000', '999999');
        $dt = array('point' => 999, 'payfirst' => 0, 'memberid' => $memberid);
        DB::update('wanba_user', $dt, "openid='" . $openid . "'");
        echo json_encode($dt);
        break;
    case 'syncMypoint':
        $openid = $arr['openid'];
        $point = intval($arr['point']);
        $tel = $arr['tel'];
        $dt = array('point' => $point, 'tel' => $tel);
        //DB::update('wanba_user',$dt,"openid='".$openid."'");

        DB::query("update wf_wanba_user set point=point+$point,tel='" . $tel . "' where openid='" . $openid . "'");
        //写入订单数据
        $order = $arr['order'];
        if ($order) {
            $memo = '';
            if ($order['eventid'] == 3) {
                $memo = '购买9999特惠卡';
            }
            $data = array('orderno' => $order['orderno'], 'openid' => $openid, 'date' => time(), 'amount' => $order['amount'], 'eventid' => $order['eventid'], 'eventtype' => $order['eventtype'], 'memo' => $memo);
            DB::insert('wanba_order', $data);
        }
        echo json_encode($dt);
        break;

    case 'checkAct':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $actIsShared = DB::result_first("select isshared from wf_wanba_act where aid=$aid and creator='" . $openid . "'");
        $point = DB::result_first("select point from wf_wanba_user where openid='" . $openid . "'");
        $tasks = DB::fetch_all("select * from wf_wanba_task where aid=$aid");
        $flag = true;
        if ($tasks) {
            if (count($tasks) < 49) {
                $flag = false;
            } else {
                foreach ($tasks as $k => $v) {
                    if ($v['taskid'] == '') {
                        $flag = false;
                        exit();
                    } else if ($v['memo'] == '') {
                        $flag = false;
                        exit();
                    }
                }
            }
        } else {
            $flag = false;
        }
        $result = array('actIsShared' => $actIsShared, 'point' => $point, 'taskFlag' => $flag);
        echo json_encode($result);
        break;
    case 'updatePoint':
        $aid = $arr['aid'];
        $openid = $arr['openid'];
        $pay = DB::fetch_first("select payfirst from wf_wanba_user where openid='" . $openid . "'");
        $point = ($pay && $pay['payfirst'] == 0) ? 999 : 999;
        $n = DB::query("update wf_wanba_user set point=point-$point,payfirst=1 where openid='" . $openid . "'");

        if ($n > 0) {
            $data = array('isshared' => 1);
            DB::update('wanba_act', $data, "aid=$aid");
            $result = array('status' => true, 'msg' => '扣点成功');
        } else {
            $result = array('status' => false, 'msg' => '扣点失败');
        }
        echo json_encode($result);
        break;

    case 'updatePcLoginToken':
        $openid = $arr['uid'];
        $token = $arr['token'];
        //DB::query("update wf_wanba_user set token='" . $token . "' where openid='" . $openid . "'");
        $result = array('status' => true);
        echo json_encode($result);
        break;
    case 'listenPclogin':
        $openid = $arr['uid'];
        $token = $arr['token'];
        //echo serialize($arr['menu']);
        $data = DB::fetch_first("select openid,nick,avatar,token,adminrole from wf_wanba_user where token='" . $token . "' and md5(openid)='" . $openid . "'");

        $result = ($data) ? array('status' => true, 'msg' => '登录成功', 'nick' => $data['nick'], 'avatar' => $data['avatar'], 'adminrole' => $data['adminrole'], 'uid' => md5($data['openid']), 'token' => $token) : array('status' => false, 'msg' => '等待中');
        echo json_encode($result);
        break;
        //获得路由菜单项
    case 'getMyRouteMenu':
        $openid = $arr['uid'];
        $token = $arr['token'];
        $data = DB::fetch_first("select adminrole,menu from wf_wanba_user where token='" . $token . "' and md5(openid)='" . $openid . "'");
        if ($data) {
            $data['menu'] = unserialize($data['menu']);
        }
        echo json_encode($data);
        break;
    case 'pcWxLogin':
        $openid = $_GET['uid'];
        $token = ($_GET['token']) ? $_GET['token'] : 0;
        $data = DB::fetch_first("select openid user,token,nick,avatar from wf_wanba_user where  md5(openid)='" . $openid . "'");
        if ($token > 0) {
            //查到uid
            if ($data) {
                $now = time();
                if ($now - $token < 5 * 60) {
                    $dt = array('token' => $token);
                    DB::update("wanba_user", $dt, "md5(openid)='" . $openid . "'");
                    $return = array('nick' => $data['nick'], 'avatar' => $data['avatar']);
                    $result = array('status' => true, 'msg' => '登录成功', 'data' => $return);
                } else {
                    $result = array('status' => false, 'msg' => 'token已过期,请重新在玩霸江湖复制网址后再进行扫码登录');
                }
            } else {
                $result = array('status' => false, 'msg' => '登录失败');
            }
        }
        //无token 传递过来
        else {
            $result = array('status' => false, 'msg' => '参数错误');
        }
        echo json_encode($result);
        break;
    case 'replaceWithSysTasks':
        $aid = $arr['aid'];
        //删除原有设置
        $oldtasks = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($oldtasks as $k => $v) {
            DB::delete('wanba_pic', "taskid=$v[taskid]");
        }
        $sourcetasks = DB::fetch_all("select * from wf_wanba_task where aid=479 order by displayorder");
        foreach ($sourcetasks as $ke => $va) {
            $sourcetasks[$ke]['pics'] = DB::fetch_all("select DISTINCT url from wf_wanba_pic where taskid=$va[taskid]");
        }
        $newtasks = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        foreach ($newtasks as $k => $v) {
            $u = array('memo' => $sourcetasks[$k]['memo'], 'qtype' => $sourcetasks[$k]['qtype'], 'answer' => $sourcetasks[$k]['answer'], 'url' => $sourcetasks[$k]['url'], 'media' => $sourcetasks[$k]['media']);
            DB::update("wanba_task", $u, "taskid=$v[taskid]");
            //插入图片
            if ($sourcetasks[$k]['pics']) {
                foreach ($sourcetasks[$k]['pics'] as $kk => $vv) {
                    $insert = array('url' => $vv['url'], 'taskid' => $v[taskid]);
                    DB::insert("wanba_pic", $insert);
                }
            }
        }

        $newtasks = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        foreach ($newtasks as $k => $v) {
            $newtasks[$k]['pics'] = DB::fetch_all("selectDISTINCT url from wf_wanba_pic where taskid=$v[taskid]");
        }
        $result = array('status' => true, 'msg' => '操作成功', 'tasklist' => $newtasks);
        echo json_encode($result);
        break;


    case 'batchImportPos':
        $aid = $arr['aid'];
        $i = 0;
        //删除原有设置
        $oldtasks = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($oldtasks as $k => $v) {
            DB::delete('wanba_pic', "taskid=$v[taskid]");
        }
        DB::delete('wanba_task', "aid=$aid");
        //新设置
        $list = $arr['list'];
        $arr1 = array(1, 9, 17,  33, 41, 49, 7, 13, 19, 31, 37, 43);
        foreach ($list as $k => $v) {

            if (!$v['任务介绍'] && !$v['问题类型'] && !$v['答案']) {
                $i++;
                // if (strlen($v['任务介绍']) == 0 && strlen($v['问题类型']) == 0 && strlen($v['答案']) == 0) {
                //     $i++;
                // }
            }
            if (in_array($v['序号'], $arr1)) {
                $ptype = 1;
            } else if ($v['序号'] == 25) {
                $ptype = 2;
            } else {
                $ptype = 0;
            }
            $dt = array('aid' => $aid, 'name' => $v['点位名称'], 'memo' => $v['任务介绍'],  'poi' => $v['原始gps坐标'], 'pmemo' => $v['点位说明'], 'displayorder' => $v['序号'], 'qtype' => $v['问题类型'], 'answer' => $v['答案'], 'ptype' => $ptype,   'latlng' => $v['腾讯地图坐标']);
            $newtaskid = DB::insert('wanba_task', $dt, 'taskid');
            // $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by displayorder");
            // foreach($task[$k]['pics'] as $ke=>$va){
            //     $d=array('taskid'=>$newtaskid,'url'=>$va['url'],'displayorder'=>$va['displayorder']);
            //     DB::insert('wanba_pic',$d);
            // }
        }
        $newtasks = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        //将gps坐标转为腾讯地图坐标
        foreach ($newtasks as $k => $v) {
            if ($v['latlng'] == '' && $v['poi'] != '') {
                $latlng = translateTXmap($v['poi']);
                $item = $latlng['locations'][0];
                $tx = $item['lat'] . ',' . $item['lng'];
                $d = array('latlng' => $tx);
                DB::update('wanba_task', $d, "taskid=$v[taskid]");
                usleep(10000);
            }
        }


        //如果未设置任务
        if ($i == 49) {
            $sourcetasks = DB::fetch_all("select * from wf_wanba_task where aid=479 order by displayorder");
            foreach ($sourcetasks as $ke => $va) {
                $sourcetasks[$ke]['pics'] = DB::fetch_all("select DISTINCT url from wf_wanba_pic where taskid=$va[taskid]");
            }

            foreach ($newtasks as $k => $v) {
                $u = array('memo' => $sourcetasks[$k]['memo'], 'qtype' => $sourcetasks[$k]['qtype'], 'answer' => $sourcetasks[$k]['answer'], 'url' => $sourcetasks[$k]['url'], 'media' => $sourcetasks[$k]['media']);
                DB::update("wanba_task", $u, "taskid=$v[taskid]");
                //插入图片
                if ($sourcetasks[$k]['pics']) {
                    foreach ($sourcetasks[$k]['pics'] as $kk => $vv) {
                        $insert = array('url' => $vv['url'], 'taskid' => $v[taskid]);
                        DB::insert("wanba_pic", $insert);
                    }
                }
            }
        }
        $newtasks = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        foreach ($newtasks as $k => $v) {
            $newtasks[$k]['pics'] = DB::fetch_all("select DISTINCT url from wf_wanba_pic where taskid=$v[taskid]");
        }
        $result = array('status' => true, 'msg' => '操作成功', 'tasklist' => $newtasks, 'count' => $i);
        echo json_encode($result);
        break;
    case 'getTaskList':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($task as $k => $v) {
            $task[$k]['pics'] = DB::fetch_all("select DISTINCT url from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
        }

        echo json_encode($task);
        break;
    case 'delPicById':
        $uid = $arr['uid'];
        $picid = $arr['picid'];
        $n = DB::delete("wanba_pic", "picid=$picid");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'delMp3ById':
        $uid = $arr['uid'];
        $mediaurl = $arr['url'];
        $taskid = $arr['taskid'];
        $ft = strrpos($mediaurl, '/', 0);
        $fn = 'audio' . substr($mediaurl, $ft);
        @unlink($fn);
        $data = array('media' => 0, 'url' => '');
        $n = DB::update("wanba_task", $data, "taskid=$taskid");
        $result = array('status' => true, 'msg' => $fn);
        echo json_encode($result);
        break;
    case 'delQuestionMp3ById':
        $mediaurl = $arr['url'];
        $questionid = $arr['questionid'];
        $ft = strrpos($mediaurl, '/', 0);
        $fn = 'audio' . substr($mediaurl, $ft);
        @unlink($fn);
        $data = array('media' => 0, 'url' => '');
        $n = DB::update("wanba_question", $data, "questionid=$questionid");
        $result = array('status' => true, 'msg' => $fn);
        echo json_encode($result);
        break;
    case 'delQuestionMp4ById':
        $mediaurl = $arr['url'];
        $questionid = $arr['questionid'];
        $ft = strrpos($mediaurl, '/', 0);
        $fn = 'questionvideo' . substr($mediaurl, $ft);
        @unlink($fn);
        $data = array('media' => 0, 'url' => '');
        $n = DB::update("wanba_question", $data, "questionid=$questionid");
        $result = array('status' => true, 'msg' => $fn);
        echo json_encode($result);
        break;
    case 'delMp4ById':
        $uid = $arr['uid'];
        $mediaurl = $arr['url'];
        $taskid = $arr['taskid'];
        $ft = strrpos($mediaurl, '/', 0);
        $fn = 'video' . substr($mediaurl, $ft);
        @unlink($fn);
        $data = array('media' => 0, 'url' => '');
        $n = DB::update("wanba_task", $data, "taskid=$taskid");
        $result = array('status' => true, 'msg' => $fn);
        echo json_encode($result);
        break;
        //pc保存点位任务设置
    case 'pcEditPoi':
        $openid = $arr['uid'];
        $poiInfo = $arr['poiObj'];
        $taskid = intval($poiInfo['taskid']);
        $displayorder = $poiInfo['displayorder'];
        $p = array(1, 7, 9, 13, 17, 19, 31, 33, 37, 41, 43, 49);
        if (in_array($displayorder, $p)) {
            $ptype = 1;
        } else if ($displayorder == 25) {
            $ptype = 2;
        } else {
            $ptype = 0;
        }
        $data = array('ptype' => $ptype, 'name' => $poiInfo['name'], 'pmemo' => $poiInfo['pmemo'], 'latlng' => $poiInfo['latlng'], 'poi' => $poiInfo['poi'], 'memo' => $poiInfo['memo'], 'answer' => $poiInfo['answer'], 'qtype' => $poiInfo['qtype']);
        $id = DB::update('wanba_task', $data, "taskid=$taskid");

        $result = ($id > 0) ? array('status' => true, 'msg' => '操作成功', 'data' => $data) : array('status' => false, 'msg' => '操作失败');


        echo json_encode($result);
        break;
    case 'insertRedbagTodo':
        $data = $arr['data'];
        $taskid = DB::result_first("select taskid from wf_wanba_task where aid=$data[aid] and displayorder=$data[displayorder]");
        $data['taskid'] = $taskid;
        $n = DB::result_first("select count(id) num from wf_wanba_redbag_question");
        $rnd = rand(1, $n);
        $data['questionid'] = $rnd;
        $id = DB::insert("wanba_redbag_todo", $data, "id");
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'getRedbagTodo':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $actStatus = DB::result_first("select status from wf_wanba_act where aid=$aid");
        $r = DB::fetch_first("select * from wf_wanba_redbag_todo where status=0 and aid=$aid and openid='" . $openid . "'");
        $total = DB::fetch_first("select sum(score) score from wf_wanba_logs where taskid=-1 and aid=$aid");
        if ($actStatus == 0) {
            if ($total) {
                if ($total['score']) {
                    $score = intval($total['score']);
                } else {
                    $score = 0;
                }
            } else {
                $score = 0;
            }
            $limit = DB::result_first("select redbagtotal from wf_wanba_act where aid=$aid");
            if ($limit > 0) {

                $r['open'] = ($score >= $limit) ? false : true;
            } else {
                $r['open'] = false;
            }
        } else {
            $r['open'] = false;
        }

        echo json_encode($r);
        break;
    case 'checkPoiRedbagTask':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $taskid = $arr['taskid'];

        $r = DB::fetch_first("select * from wf_wanba_redbag_todo where status=0 and taskid=$taskid and aid=$aid and openid='" . $openid . "'");
        echo json_encode($r);
        break;
    case 'getRandomQuestion':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $taskid = $arr['taskid'];

        $r = DB::fetch_first("select q.question,q.id from  wf_wanba_redbag_todo r, wf_wanba_redbag_question q where r.status=0 and r.aid=$aid and r.taskid=$taskid and r.openid='" . $openid . "' and r.questionid=q.id order by r.id");
        $config = DB::fetch_first("select redbagtotal,redbagrand,slogan from wf_wanba_act where aid=$aid");

        echo json_encode(array('question' => $r, 'config' => $config));
        break;

    case 'checkRedbagAnswer':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $questionid = $arr['questionid'];
        $taskid = $arr['taskid'];
        $answer = $arr['answer'];
        $score = $arr['score'];
        $user = DB::fetch_first("select currentteamid teamid,nick from wf_wanba_user where openid='" . $openid . "'");

        $r = DB::fetch_first("select id from  wf_wanba_redbag_question where id=$questionid and answer like '%" . $answer . "%'");
        if ($r) {
            //修改状态
            $d = array('status' => 1);
            DB::update("wanba_redbag_todo", $d, "aid=$aid and status=0 and taskid=$taskid and questionid=$questionid and openid='" . $openid . "'");
            //插入log
            $data = array('aid' => $aid, 'teamid' => $user[teamid], 'taskid' => -1, 'creator' => $openid, 'date' => time(), 'score' => $score, 'memo' => $user[nick] . '赢得了' . $score . '红包');

            $id = DB::insert('wanba_logs', $data, 'id');
            $result = array('status' => true, 'msg' => '回答正确，获得了' . $score . '红包奖励');
        } else {
            $d = array('status' => 2);
            DB::update("wanba_redbag_todo", $d, "aid=$aid  and status=0 and taskid=$taskid and questionid=$questionid and openid='" . $openid . "'");
            $result = array('status' => false, 'msg' => '回答错误');
        }
        echo json_encode($result);
        break;
        //查询城市合伙人状态
    case 'queryAgentApply':
        $openid = $arr['openid'];
        $result = DB::fetch_first("select * from wf_wanba_agent where openid='" . $openid . "'");
        if (!$result) {
            $result = array('mode' => false);
        } else {
            $result['mode'] = true;
        }
        echo json_encode($result);
        break;
    case 'applyAgent':
        $company = $arr['companyName'];
        $openid = $arr['openid'];
        $orgCode = $arr['orgCode'];
        $corporate = $arr['corporate'];
        $tel = $arr['telephone'];
        $city = $arr['address'];
        $address = $arr['addressDetail'];
        $date = time();
        $n = DB::fetch_first("select id from wf_wanba_agent where openid='" . $openid . "'");
        if ($n) {
            $data = array('openid' => $openid, 'orgcode' => $orgCode, 'company' => $company, 'corporate' => $corporate, 'tel' => $tel, 'city' => $city, 'address' => $address, 'status' => 0, 'date' => $date);
            DB::update("wanba_agent", $data, "id=$n[id]");
            $data['id'] = $n['id'];
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } else {
            $data = array('openid' => $openid, 'orgcode' => $orgCode, 'company' => $company, 'corporate' => $corporate, 'tel' => $tel, 'city' => $city, 'address' => $address, 'date' => $date);

            $id = DB::insert('wanba_agent', $data, 'agentid');
            if ($id) {
                $data['id'] = $id;
                $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
            } else {
                $result = array('status' => false, 'msg' => '操作失败');
            }
        }

        echo json_encode($result);
        break;
    case 'getMyAgentStatistics':
        $openid = $arr['openid'];
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $season = ceil(date('m') / 3);
        $beginThisseason = mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date('Y'));

        $myClients = DB::fetch_all("select openid,nick,tel from wf_wanba_user where agent='" . $openid . "' order by date desc");
        foreach ($myClients as $k => $v) {
            $myClients[$k]['saleList'] = DB::fetch_all("select title,from_unixtime(date, '%Y-%m-%d') date from wf_wanba_act where creator='" . $v[openid] . "' order by date desc");
        }


        $seasonSales = DB::fetch_all("SELECT DISTINCT a.title title,from_unixtime(a.date, '%Y-%m-%d') date FROM `wf_wanba_act` a,`wf_wanba_user` u,`wf_wanba_agent` g where a.date>=$beginThisseason and a.creator=u.openid and u.agent='" . $openid . "'");
        $seasonClients = DB::fetch_all("SELECT openid,nick,tel from `wf_wanba_user`   where date>=$beginThisseason  and agent='" . $openid . "'");
        $monthSales = DB::fetch_all("SELECT DISTINCT a.title title,from_unixtime(a.date, '%Y-%m-%d') date FROM `wf_wanba_act` a,`wf_wanba_user` u,`wf_wanba_agent` g where a.date>=$beginThismonth and a.creator=u.openid and u.agent='" . $openid . "'");
        $monthClients = DB::fetch_all("SELECT openid,nick,tel from `wf_wanba_user`   where date>=$beginThismonth  and agent='" . $openid . "'");
        $seasonData = array("seasonSales" => $seasonSales, "seasonClients" => $seasonClients);
        $monthData = array("monthSales" => $monthSales, "monthClients" => $monthClients);
        $result = array("myClients" => $myClients, "seasonData" => $seasonData, "monthData" => $monthData, "swiper" => $swiper);
        echo json_encode($result);
        break;
    case 'getMyAccountBalance':
        $openid = $arr['openid'];
        $type = $arr['type'];
        $year = $arr['year'];
        $month = $arr['month'];
        $m = $year . "-" . $month;
        $month_start = mktime(0, 0, 0, $month, 1, $year); //指定月份月初时间戳  
        $month_end = strtotime(date('Y-m-t 23:59:59', strtotime($m)));
        $data = DB::fetch_first("select point,memberid from wf_wanba_user where openid='" . $openid . "'");
        if ($type == 0) {
            $list = DB::fetch_all("SELECT `point`,from_unixtime(date, '%Y-%m-%d') date,`event`,`tradeno`,fromid from `wf_wanba_cash_logs`   where  date>=$month_start and  date<=$month_end   and openid='" . $openid . "'");
        } else if ($type == 1) {

            $list = DB::fetch_all("SELECT `point`,from_unixtime(date, '%Y-%m-%d') date,`event`,`tradeno`,fromid from `wf_wanba_cash_logs`   where cat=0 and date>=$month_start and  date<=$month_end   and openid='" . $openid . "'");
        } else {

            $list = DB::fetch_all("SELECT `point`,from_unixtime(date, '%Y-%m-%d') date,`event`,`tradeno`,fromid from `wf_wanba_cash_logs`   where cat=1 and date>=$month_start and  date<=$month_end   and openid='" . $openid . "'");
        }
        $result = array('list' => $list, 'account' => $data);
        echo json_encode($result);

        break;
    case 'getMyAgentHistory':
        $openid = $arr['openid'];
        $type = $arr['type'];
        $year = $arr['year'];
        $month = $arr['month'];
        $m = $year . "-" . $month;
        $month_start = mktime(0, 0, 0, $month, 1, $year); //指定月份月初时间戳  
        $month_end = strtotime(date('Y-m-t 23:59:59', strtotime($m)));
        if ($type == 0) {
            $list = DB::fetch_all("SELECT nick,from_unixtime(date, '%Y-%m-%d') date,tel from `wf_wanba_user`   where  date>=$month_start and  date<=$month_end   and agent='" . $openid . "'");
        } else {

            $list = DB::fetch_all(" SELECT DISTINCT a.title title,from_unixtime(a.date, '%Y-%m-%d') date,u.nick nick FROM `wf_wanba_act` a,`wf_wanba_user` u,`wf_wanba_agent` g where a.date>=$month_start and a.date<=$month_end and a.creator=u.openid and u.agent='" . $openid . "'");
        }
        echo json_encode($list);
        break;
    case 'postSuggestion':
        $openid = $arr['openid'];
        $txt = addslashes($arr['txt']);
        $data = array('openid' => $openid, 'content' => $txt, 'date' => time());
        $id = DB::insert("wanba_suggestion", $data, 'id');
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'agreement':
        $openid = $arr['openid'];
        $from = $arr['from'];
        $data = array("agent" => $from);
        $n = DB::update("wanba_user", $data, "openid='" . $openid . "'");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '恭喜你成功加入');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'getAgentApplyList':
        $status = ($arr['status']) ? $arr['status'] : 0;
        $list = DB::fetch_all("select `company`, `orgcode`, `corporate`, `tel`, `pic`, `openid`, `status`, `city`, `address`, from_unixtime(date, '%Y-%m-%d') date,id,reason from wf_wanba_agent where status=$status");
        echo json_encode($list);
        break;
    case 'updateOpenAreaStatus':
        $aid = $arr['aid'];
        $dt = implode(',', $arr['data']);
        $data = array('openarea' => $dt);
        DB::update('wanba_act', $data, "aid=$aid");
        if ($arr['data']) {
            $sql = "update wf_wanba_task set open=1 where aid=$aid and displayorder in (2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)";
            DB::query($sql);
            for ($i == 1; $i <= 4; $i++) {

                foreach ($arr['data'] as $k => $v) {
                    if ($v == $i) {
                        switch ($v) {
                            case 1:
                                $ids = "2,3,4,5,6,10,11,12,18";
                                break;
                            case 2:
                                $ids = "14,21,28,35,42,20,27,34,26";
                                break;
                            case 3:
                                $ids = "44,45,46,47,48,38,39,40,32";
                                break;
                            case 4:
                                $ids = "8,15,22,29,36,16,23,30,24";
                                break;
                        }
                        $sql = "update wf_wanba_task set open=0 where aid=$aid and displayorder in (" . $ids . ")";
                        DB::query($sql);
                    }
                }
            }
        } else {
            $sql = "update wf_wanba_task set open=1 where aid=$aid and displayorder in (2,3,4,5,6,10,11,12,18,14,21,28,35,42,20,27,34,26,44,45,46,47,48,38,39,40,32,8,15,22,29,36,16,23,30,24)";
            DB::query($sql);
        }
        //echo $dt;

        break;
    case 'updateAgentApply':
        $id = $arr['id'];
        $reason = $arr['reason'];
        $status = $arr['status'];
        $data = array("status" => $status, "reason" => $reason);
        $n = DB::update("wanba_agent", $data, "id=$id");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'getSuggestionList':
        $list = DB::fetch_all("SELECT s.id id, u.nick nick, from_unixtime(s.date, '%Y-%m-%d') date, s.content  content FROM `wf_wanba_suggestion` s,wf_wanba_user u where s.openid=u.openid");
        echo json_encode($list);
        break;
    case 'getRouteApplyList':
        $list = DB::fetch_all("SELECT a.id id,a.aid aid, u.nick nick, from_unixtime(a.applydate, '%Y-%m-%d') date, a.title  title,a.memo  memo FROM `wf_wanba_routeapply` a,wf_wanba_user u where a.applyopenid=u.openid  and a.status=0 order by date");
        foreach ($list as $k => $v) {
            $list[$k]['memo'] = unserialize($v['memo']);
        }
        echo json_encode($list);
        break;
    case 'getRoutePoiList':
        $aid = $arr['aid'];

        $list = DB::fetch_all("select * from wf_wanba_task where aid=$aid");
        echo json_encode($list);
        break;
    case 'updateRoutePoiList':
        $aid = $arr['aid'];
        $poi = $arr['poiList'];
        foreach ($poi as $k => $v) {
            $u = array('latlng' => $v['latlng']);
            DB::update("wanba_task", $u, "aid=$aid and displayorder=$v[displayorder]");
        }
        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;
    case 'getRouteList':

        $list = DB::fetch_all("SELECT aid, title,route_desc,city,tag from wf_wanba_act where template=1 order by aid desc");
        foreach ($list as $k => $v) {
            $list[$k]['route_desc'] = unserialize($v['route_desc']);

            //$list[$k]['city'] = explode('|',$v['city']);
            //$list[$k]['tag'] = explode('|',$v['tag']);
        }
        //$result = array('count' => $total, 'list' => $list);
        echo json_encode($list);
        break;
    case 'delRouteByAid':
        $aid = $arr['aid'];
        $u = array("template" => -2, 'aid' => $aid);
        $n = DB::update("wanba_act", $u, "aid=$aid and template=1");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $u);
        } else {
            $result = array('status' => false, 'msg' => '操作失败', 'data' => $u);
        }
        echo json_encode($result);
        break;
    case 'editRouteList':
        $title = $arr['title'];
        $route_desc = serialize(array('memo1' => $arr['memo1'], 'memo2' => $arr['memo2'], 'memo3' => $arr['memo3']));
        $aid = $arr['aid'];
        $city = $arr['city'];
        $tag = $arr['tag'];
        $u = array("title" => $title, 'route_desc' => $route_desc, 'city' => $city, 'tag' => $tag);
        $n = DB::update('wanba_act', $u, "aid=$aid and template=1");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $u);
        } else {
            $result = array('status' => false, 'msg' => '操作失败', 'data' => $u);
        }
        echo json_encode($result);
        break;
        //线路收藏
    case 'routeFav':
        $catid = $arr['catid'];
        $cat = $arr['cat'];
        $uid = $arr['uid'];
        $token = $arr['token'];

        $d = DB::fetch_first("select id from wf_wanba_favorite where catid=$catid  and cat=$cat and openid='" . $uid . "'");
        if ($d) {
            //已收藏则删除   
            DB::delete("wanba_favorite", "id=$d[id]");
            $fav = 0;
        } else {
            //加入收藏
            $dt = array('cat' => $cat, 'catid' => $catid, 'openid' => $uid);
            DB::insert("wanba_favorite", $dt);
            $fav = 1;
        }
        echo json_encode(array('fav' => $fav, 'status' => true, 'msg' => '操作成功'));
        break;

        //线路详情
    case 'getRouteContent':
        $aid = $arr['aid'];
        $uid = $arr['uid'];
        $token = $arr['token'];
        $d = DB::fetch_first("select * from wf_wanba_act where aid=$aid");
        $d['route_desc'] = unserialize($d['route_desc']);
        $fav = DB::fetch_first("select id from wf_wanba_favorite where cat=0 and catid=$aid and openid='" . $uid . "'");
        $d['fav'] = ($fav) ? 1 : 0;
        $d['tasks'] = DB::fetch_all("select * from wf_wanba_task where aid=$aid");
        echo json_encode($d);
        break;

        //线路点位详情
    case 'getRouteDetail':
        $aid = $arr['aid'];
        $applyid = $arr['applyid'];
        $d = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        foreach ($d as $k => $v) {
            $d[$k]['pics'] = DB::fetch_all("select DISTINCT url from wf_wanba_pic where taskid=$v[taskid]");
        }
        $data = array('applyid' => $applyid, 'detail' => $d);
        echo json_encode($data);
        break;
    case  'postRouteApply':
        $aid = $arr['aid'];
        $title = $arr['title'];
        $route_desc = serialize($arr['route_desc']);
        $applyopenid = $arr['uid'];

        $data = array('aid' => $aid, 'title' => $title, 'memo' => $route_desc, 'applyopenid' => $applyopenid, 'applydate' => time());
        $id = DB::insert('wanba_routeapply', $data, "id");
        if ($id) {
            $result = array('routeid' => $id, 'status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'reviewRouteApply':
        $applyid = $arr['applyid'];
        $aid = $arr['aid'];
        $title = $arr['title'];
        $route_desc = serialize($arr['route_desc']);
        $status = $arr['status'];
        $reason = $arr['reason'];
        $uid = $arr['uid'];

        $data = array('status' => $status, 'reason' => $reason, 'checkopenid' => $uid, 'checkdate' => time());
        $n = DB::update('wanba_routeapply', $data, "id=$applyid");
        if ($status == 1) {
            $creator = DB::result_first("select creator from wf_wanba_act where aid=$aid");
            //给推荐者加点数，写日志
            DB::query("update wf_wanba_user set point=point+999 where openid='" . $creator . "'");
            $log = array('openid' => $creator, 'date' => time(), 'fromid' => $uid, 'event' => "恭喜您推荐的线路\"" . $title . "\"通过审核，获得999玩点奖励");
            DB::insert('wanba_cash_logs', $log);
            $d = array("title" => $title, "route_desc" => $route_desc, "date" => time(), "creator" => $creator, "template" => 1, "sharepic" => $applyid . '.jpg');
            $newaid = DB::insert("wanba_act", $d, "aid");
            $dt = array('bindtempalteid' => $newaid);
            // DB::update('wanba_routeapply', $dt, "id=$applyid");
            $tasks = DB::fetch_all("SELECT `taskid`, `name`, `displayorder`,`pmemo`, `memo`, `qtype`, `answer`, `poi`, `ptype`,pvalue, latlng,media,url FROM  `wf_wanba_task` where aid=$aid");
            foreach ($tasks as $k => $v) {
                $pics = DB::fetch_all("select DISTINCT url from wf_wanba_pic where taskid=$v[taskid]");
                $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => $v['answer'], "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $newaid, "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);

                $insert_task_id = DB::insert('wanba_task', $insert, 'taskid');
                foreach ($pics as $ke => $va) {
                    $u = array("url" => $va['url'], 'taskid' => $insert_task_id);
                    DB::insert('wanba_pic', $u);
                }
            }
        }
        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;
    case 'getTeamsByAid':
        $aid = $arr['aid'];
        $act = DB::fetch_first("SELECT * FROM  `wf_wanba_act`  where aid=$aid");
        $teams = DB::fetch_all("select name,displayorder from wf_wanba_team_setting where aid=$aid and displayorder<=$act[teamNum]");
        foreach ($teams as $k => $v) {
            $hasScore = DB::fetch_first("select sum(score) score from wf_wanba_logs where aid=$aid and teamid=$v[displayorder] and event like '%获得地块连线加分%'");

            $teams[$k]['total'] = ($hasScore) ? $hasScore['score'] : null;
        }
        echo json_encode($teams);
        break;
    case 'getSysQuestionList':
        $openid = $arr['uid'];
        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $where = ' where sys=1 ';
        $sql = "select count(questionid) from `wf_wanba_question` " . $where;
        $total = DB::result_first($sql);
        $syslist = DB::fetch_all("SELECT `questionid`, `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` FROM `wf_wanba_question` where  sys=1 order by questionid desc  limit " . $pageindex . "," . $pagesize);
        foreach ($syslist as $k => $v) {
            $syslist[$k]['pics'] = DB::fetch_all("select * from wf_wanba_question_pic where questionid=$v[questionid]");
        }
        $result = array('count' => $total, 'list' => $syslist);
        echo json_encode($result);
        break;
    case 'getSysQuestionApplyList':
        $list = DB::fetch_all("SELECT a.id id,a.qid qid, u.nick nick, from_unixtime(a.applydate, '%Y-%m-%d') date, q.memo  title,q.answer  answer FROM `wf_wanba_questionapply` a,`wf_wanba_question` q,wf_wanba_user u where a.applyopenid=u.openid  and a.status=0  and a.qid=q.questionid order by date");
        echo json_encode($list);
        break;
    case 'getQuestionDetail':
        $qid = $arr['qid'];
        $applyid = $arr['applyid'];
        $d = DB::fetch_first("select * from wf_wanba_question where questionid=$qid");

        $d['pics'] = DB::fetch_all("select url from wf_wanba_question_pic where questionid=$qid");

        $data = array('applyid' => $applyid, 'detail' => $d);
        echo json_encode($data);
        break;

    case  'postSysQuestionApply':
        $qid = $arr['qid'];
        $applyopenid = $arr['uid'];

        $data = array('qid' => $qid,   'applyopenid' => $applyopenid, 'applydate' => time());
        $id = DB::insert('wanba_questionapply', $data, "id");
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case  'delSysQuestionPicById':
        $picid = $arr['picid'];
        $n = DB::delete('wanba_question_pic', "picid=$picid");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'reviewSysQuestionApply':
        $applyid = $arr['applyid'];
        $qid = $arr['qid'];
        // $title=$arr['title'];
        // $route_desc=$arr['route_desc'];
        $status = $arr['status'];
        $reason = $arr['reason'];
        $data = array('status' => $status, 'reason' => $reason);
        $n = DB::update('wanba_questionapply', $data, "id=$applyid");
        if ($status == 1) {
            $question = DB::fetch_first("select `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` from wf_wanba_question where questionid=$qid");
            $question['sys'] = 1;
            $newqid = DB::insert("wanba_question", $question, "questionid");

            $pics = DB::fetch_all("select url from wf_wanba_question_pic where questionid=$qid");

            foreach ($pics as $ke => $va) {
                $u = array("url" => $va['url'], 'questionid' => $newqid);
                DB::insert('wanba_question_pic', $u);
            }
        }


        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;

    case 'getAdminList':
        $list = DB::fetch_all("select nick,adminrole,menu,openid from wf_wanba_user where adminrole ='admin' or adminrole='super'");
        foreach ($list as $k => $v) {
            $list[$k]['menu'] = unserialize($v['menu']);
        }
        echo json_encode($list);
        break;

    case 'postAuctionScore':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $total = $arr['total'];
        $name = $arr['name'];
        $event = $name . "获得地块连线加分" . $total;
        $data = array('aid' => $aid, 'teamid' => $teamid,  'status' => 1, 'score' => $total, 'date' => time(),  'event' => $event);
        $id = DB::insert('wanba_logs', $data, 'id');
        if ($id > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $arr);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    case 'getUserByNick':
        $nick = $arr['nick'];
        $list = DB::fetch_all("select nick,openid,adminrole,menu from wf_wanba_user where adminrole <>'admin' and  adminrole<>'super' and nick like '%" . $nick . "%'");
        echo json_encode($list);
        break;

    case 'assignUserAccess':
        $openid = $arr['openid'];
        $adminrole = $arr['adminrole'];
        $menu = serialize($arr['menu']);
        $data = array('adminrole' => $adminrole, 'menu' => $menu);
        $n = DB::update("wanba_user", $data, "openid='" . $openid . "'");
        if ($n > 0) {

            $result = array('status' => true, 'msg' => '操作成功', 'data' => $arr);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);

        break;

    case 'getAlbumTest':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'] ? $arr['teamid'] : 0;
        $pagesize = $arr['pagesize'] ? $arr['pagesize'] : 20;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $num = $pagesize;
        $limit = " limit " . $pageindex . "," . $num;
        $teams = DB::fetch_all("select name,displayorder from wf_wanba_team_setting where aid=$aid");
        if ($teamid >= 1) {
            $album = DB::fetch_all("select * from wf_wanba_album where aid=$aid and teamid=$teamid order by id desc" . $limit);
        } else {
            $album = DB::fetch_all("select * from wf_wanba_album where aid=$aid order by id desc" . $limit);
        }
        $click = DB::fetch_first("select sum(click) c from wf_wanba_album where aid=$aid");
        $click = ($click['c']) ? $click['c'] : 0;
        $count = DB::fetch_first("select count(id) c from wf_wanba_album where aid=$aid");
        $count = ($count) ? $count['c'] : 0;
        $act = DB::fetch_first("select sharepic c,title,uploadPhotoSetting bonus from wf_wanba_act where aid=$aid");
        $cover = ($act['c']) ? $act['c'] : '1.jpg';
        $result = array('album' => $album, 'teams' => $teams, 'total' => $count, 'click' => $click, 'cover' => $cover, 'title' => $act['title'], 'bonus' => $act['bonus']);
        echo json_encode($result);
        break;
    case 'getAlbum':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $pagesize = $arr['pagesize'] ? $arr['pagesize'] : 12;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;
        $teams = DB::fetch_all("select name,displayorder from wf_wanba_team_setting where aid=$aid");
        $album = DB::fetch_all("select * from wf_wanba_album where aid=$aid order by id desc");
        $click = DB::fetch_first("select sum(click) c from wf_wanba_album where aid=$aid");
        $click = ($click['c']) ? $click['c'] : 0;
        $count = DB::fetch_first("select count(id) c from wf_wanba_album where aid=$aid");
        $count = ($count) ? $count['c'] : 0;
        $act = DB::fetch_first("select sharepic c,title,uploadPhotoSetting bonus from wf_wanba_act where aid=$aid");
        $cover = ($act['c']) ? $act['c'] : '1.jpg';
        $result = array('album' => $album, 'teams' => $teams, 'total' => $count, 'click' => $click, 'cover' => $cover, 'title' => $act['title'], 'bonus' => $act['bonus']);
        echo json_encode($result);
        break;
    case 'updatePicFav':
        $id = $arr['id'];
        $cat = 3;
        $openid = $arr['openid'];
        $q = DB::fetch_first("select * from `wf_wanba_favorite` where catid=$id and cat=3 and openid='" . $openid . "'");
        if (!$q) {
            $i = array("openid" => $openid, 'catid' => $id, 'cat' => 3);
            DB::insert("wanba_favorite", $i);
            DB::query("update wf_wanba_album set fav=fav+1 where id=$id");
        }
        $n = DB::result_first("select fav from wf_wanba_album  where id=$id");
        $result = array("fav" => $n);
        echo json_encode($result);

        break;
    case 'updatePicView':
        $id = $arr['id'];
        DB::query("update wf_wanba_album set click=click+1 where id=$id");
        break;

    case 'updateTeamNum':
        $aid = $arr['aid'];
        $teamnum = $arr['teamnum'];
        DB::query("update wf_wanba_act set teamNum=$teamnum where aid=$aid and aid<>1");
        //写入订单
        $order = $arr['order'];
        if ($order) {
            $title = DB::result_first("select title from wf_wanba_act where aid=$aid");
            $memo = "购买了门派卡，将活动" . $title . "门派数量扩充至" . $teamnum;
            $data = array('orderno' => $order['orderno'], 'openid' => $order['openid'], 'date' => time(), 'amount' => $order['amount'], 'eventid' => $order['eventid'], 'eventtype' => $order['eventtype'], 'memo' => $memo);
            DB::insert('wanba_order', $data);
        }
        $result = array('status' => true, 'msg' => '操作成功', 'teamnum' => $teamnum);
        echo json_encode($result);
        break;

    case 'getMyRadarData':
        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $teams = DB::fetch_all("select displayorder teamid from wf_wanba_team_setting where aid=$aid");
        foreach ($teams as $k => $v) {
            $l = array();
            //计算占地情况，得出协同指数
            $my = DB::result_first("SELECT count(id) c FROM `wf_wanba_pass`  where aid=$aid and teamid=$v[teamid] and pass=2");
            if ($my) {
                $total = DB::result_first("SELECT count(id) c FROM `wf_wanba_pass`  where aid=$aid  and pass=2");
                $l1 = round($my / $total * 100);
            } else {
                $l1 = 0;
            }
            $l[] = array('name' => '协同', 'value' => $l1);
            //计算得分 得出脑力
            $money = DB::fetch_first("SELECT  sum(score) money FROM  `wf_wanba_logs`  where aid=$aid and  teamid=$v[teamid]");
            if ($money) {
                if ($money['money'] <= 0) {
                    $l2 = 0;
                } else {
                    $mymoney = $money['money'];
                    $allmoney = DB::fetch_first("SELECT  sum(score) money FROM  `wf_wanba_logs`  where aid=$aid");
                    $l2 = round($mymoney / $allmoney['money'] * 100);
                }
            } else {
                $l2 = 0;
            }

            $l[] = array('name' => '脑力', 'value' => $l2);
            //计算照片 得出颜值
            $allphotos = DB::result_first("SELECT count(id) c from wf_wanba_album where aid=$aid");
            if ($allphotos) {
                if ($allphotos == 0) {
                    $l3 = -1;
                } else {
                    $teamphotos = DB::result_first("SELECT count(id) c from wf_wanba_album where aid=$aid and teamid=$v[teamid]");
                    if ($teamphotos) {
                        if ($teamphotos > 0) {
                            $allphotos = DB::result_first("SELECT count(id) c from wf_wanba_album where aid=$aid");
                            $l3 = round($teamphotos / $allphotos * 100);
                        } else {
                            $l3 = 0;
                        }
                    } else {
                        $l3 = 0;
                    }
                }
            } else {
                $l3 = -1;
            }
            if ($l3 > -1) {
                $l[] = array('name' => '颜值', 'value' => $l3);
            }

            //统计步数，得出活力
            $allwerun = DB::result_first("SELECT sum(step) s FROM `wf_wanba_werun`  where aid=$aid and teamid>0 ");
            if ($allwerun) {
                if ($allwerun > 0) {
                    $mywerun = DB::fetch_first("SELECT sum(step) s FROM `wf_wanba_werun`  where aid=$aid and  teamid=$v[teamid]");
                    // $mywerun = DB::fetch_first("SELECT sum(step) s FROM `wf_wanba_werun`  where aid=$aid and  teamid=$v[teamid] and date=date_format(now(),'%Y-%m-%d')");
                    if ($mywerun) {
                        if ($mywerun['s']) {

                            //    $allwerun = DB::result_first("SELECT sum(step) s FROM `wf_wanba_werun`  where aid=$aid and teamid>0 and date=date_format(now(),'%Y-%m-%d')");
                            $l4 = round($mywerun['s'] / $allwerun * 100);
                        } else {
                            $l4 = 0;
                        }
                    } else {
                        $l4 = 0;
                    }
                } else {
                    $l4 = -1;
                }
            } else {
                $l4 = -1;
            }
            if ($l4 > -1) {
                $l[] = array('name' => '活力', 'value' => $l4);
            }
            //统计宝石+红包 得出玩力
            $allstone = DB::result_first("SELECT count(id)  c FROM `wf_wanba_logs` where aid=$aid  and event like '%获得了一颗%'");
            $allredbag = DB::result_first("SELECT count(id)  c FROM `wf_wanba_logs` where id=$aid  and  memo like '%红包%'");
            if ($allstone == 0 && $allredbag == 0) {
                $l5 = -1;
            } else {
                $mystone = DB::result_first("SELECT count(id)  c FROM `wf_wanba_logs` where aid=$aid and  teamid=$v[teamid] and event like '%获得了一颗%'");
                $myredbag = DB::result_first("SELECT count(id)  c FROM `wf_wanba_logs` where id=$aid  and  teamid=$v[teamid] and  memo like '%红包%'");
                $my = $mystone + $myredbag;
                $all = $allstone + $allredbag;
                $l5 = round($my / $all * 100);
            }
            if ($l5 > -1) {
                $l[] = array('name' => '玩力', 'value' => $l5);
            }

            if (count($l) == 5) {
                $avg = round(($l[0]['value'] + $l[1]['value'] + $l[2]['value'] + $l[3]['value'] + $l[4]['value']));
            } else if (count($l) == 4) {
                $avg = round(($l[0]['value'] + $l[1]['value'] + $l[2]['value'] + $l[3]['value']));
            } else if (count($l) == 3) {
                $avg = round(($l[0]['value'] + $l[1]['value'] + $l[2]['value']));
            } else if (count($l) == 2) {
                $avg = round(($l[0]['value'] + $l[1]['value']));
            } else if (count($l) == 1) {
                $avg = round(($l[0]['value']));
            } else {
                $avg = 0;
            }

            $up = array('avgscore' => $avg);
            DB::update('wanba_team_setting', $up, "aid=$aid and displayorder=$v[teamid]");
            $less = DB::result_first("select count(id) c from wf_wanba_team_setting where  avgscore<$avg and avgscore>=0");
            $all = DB::result_first("select count(id) c from wf_wanba_team_setting where avgscore>=0");
            $rate = round($less / $all * 100, 2);

            if (intval($teamid) == intval($v['teamid'])) {
                $result = array('l' => $l, 'avg' => $avg, 'rate' => $rate);
            }
        }
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode('fail');
        }
        break;
    case 'postAIPic':
        $data = $arr['data'];
        $id = DB::insert('wanba_ai_pic', $data, 'id');
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => array('pic' => $data['pic'], 'title' => $data['title'], 'id' => $id));
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'getaipics':
        $aid = $arr['aid'];
        $data = DB::fetch_all("select id,pic,title from wf_wanba_ai_pic where aid=$aid order by id desc");
        echo json_encode($data);
        break;
    case 'delaipic':
        $id = $arr['id'];
        $n = DB::delete('wanba_ai_pic', "id=$id");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'getAITips':
        $aid = $arr['aid'];
        $data = DB::fetch_all("select id,aid,tip from wf_wanba_ai_tip where aid=$aid order by id");
        echo json_encode($data);
        break;
    case 'delAITip':
        $aid = $arr['aid'];
        $tipid = $arr['tipid'];

        $n = DB::delete("wanba_ai_tip", "id=$tipid");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);

        break;
    case 'addAITip':
        $aid = $arr['aid'];
        $tip = $arr['tip'];

        $d = array('aid' => $aid, 'tip' => $tip);
        $n = DB::insert("wanba_ai_tip", $d, 'id');
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'item' => array('aid' => $aid, 'tip' => $tip, 'id' => $n));
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'postAIBaseSetting':
        $aid = $arr['aid'];
        $ai_duration = $arr['ai_duration'];
        $ai_random = $arr['ai_random'];
        $redbag = $arr['redBag'];
        $d = array('ai_duration' => $ai_duration, 'ai_random' => $ai_random, 'redbagrand' => $redbag);
        DB::update('wanba_act', $d, "aid=$aid");

        break;
    case 'getAIBaseSetting':
        $aid = $arr['aid'];
        $data = DB::fetch_first("select ai_duration,ai_random,redbagrand from wf_wanba_act where aid=$aid");
        echo json_encode($data);

        break;
    case 'isNewUser':
        $openid = $arr['openid'];
        $fromid = $arr['fromid'];
        if ($fromid) {
            $c = DB::result_first("SELECT count(aid) c FROM `wf_wanba_act`  where `creator`='" . $openid . "'  and isshared=1 and creator<>'" . $fromid . "'");
            $status = ($c > 0) ? false : true;
            if ($openid == $fromid) {
                $status = false;
            }
        } else {
            $c = DB::result_first("SELECT count(aid) c FROM `wf_wanba_act`  where `creator`='" . $openid . "'  and isshared=1");
            $status = ($c > 0) ? false : true;
        }
        echo json_encode(array("status" => $status));
        break;
    case 'getUserList':
        $key = $arr['keyword'];
        $where = ($key == '') ? " " : " where nick like '%" . $key . "%' or tel like '%" . $key . "%' ";
        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;
        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $column = $arr['column'];
        $order_col = $arr['column'] ? $arr['column'] : " date ";
        $orderby = $arr['orderby'] ? $arr['orderby'] : " desc ";
        $fullorder = " order by  " . $order_col . " " . $orderby;
        $fullorder = str_replace("normal", "", $fullorder);
        $limit = " limit " . $pageindex . "," . $pagesize;
        $c = DB::fetch_first("select count(openid) c from wf_wanba_user" . $where);
        $data = DB::fetch_all("select * from wf_wanba_user " . $where . $fullorder . $limit);
        $result = array('list' => $data, 'total' => $c['c']);
        echo json_encode($result);
        break;
    case 'getMyRouteList':
        $openid = $arr['openid'];
        $list = DB::fetch_all("select sharepic, aid, from_unixtime(date, '%Y.%m.%d') date,title,buy_count from wf_wanba_act where template=1 and creator='" . $openid . "' order by buy_count desc");
        $total = 0;
        foreach ($list as $k => $v) {
            $total += $v['buy_count'];
        }
        $inapplying = DB::fetch_first("SELECT sum( cash ) c FROM  `wf_wanba_withdrawapply` WHERE STATUS >=0 and openid='" . $openid . "'");
        if ($inapplying) {
            $num = $inapplying['c'];
        } else {
            $num = 0;
        }
        $amount = $total * 90 - $num;
        $amount = ($amount <= 0) ? 0 : $amount;
        $result = array('amount' => $amount, 'list' => $list);
        echo json_encode($result);
        break;
    case 'postWithDrawApply':
        $openid = $arr['openid'];
        $card = $arr['card'];
        $bank = $arr['bank'];
        $name = $arr['name'];
        $cash = $arr['cash'];
        $date = time();
        $data = array("openid" => $openid, "bank" => $bank, "card" => $card, "name" => $name, "cash" => $cash, 'date' => $date);
        $id = DB::insert("wanba_withdrawapply", $data, "id");
        if ($id > 0) {
            $data = array("openid" => $openid, "bank" => $bank, "card" => $card, "name" => $name, "cash" => $cash, 'date' => $date, 'id' => $id);
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
        // case 'addNewBankCard';
        //     $openid = $arr['openid'];
        //     $card = $arr['card'];
        //     $bank = $arr['bank'];
        //     $name = $arr['name'];
        //     $data = array("openid" => $openid, "bank" => $bank, "cardno" > $card, "name" => $name);
        //     $id = DB::insert("", $data, "id");

        //     if ($id > 0) {
        //         $data = array("openid" => $openid, "bank" => $bank, "cardno" > $card, "name" => $name, "id" => $id);
        //         $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        //     } else {
        //         $result = array('status' => false, 'msg' => '操作失败');
        //     }
        //     echo json_encode($result);
        //     break;
        //账户余额
    case 'getMyWithdrawAccount':
        $openid = $arr['openid'];
        $total = 0;
        $list = DB::fetch_all("select sharepic, aid, from_unixtime(date, '%Y.%m.%d') date,title,buy_count from wf_wanba_act where template=1 and creator='" . $openid . "'");
        $mywithdrawapplylist = DB::fetch_all("select from_unixtime(date, '%Y.%m.%d %T') date,cash,status from wf_wanba_withdrawapply where openid='" . $openid . "' order by date desc");
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $total += $v['buy_count'];
            }
            $inapplying = DB::fetch_first("SELECT sum( cash ) c FROM  `wf_wanba_withdrawapply` WHERE STATUS >=0 and openid='" . $openid . "'");
            if ($inapplying) {
                $num = $inapplying['c'];
            } else {
                $num = 0;
            }
            $amount = $total * 90 - $num;
        } else {
            $amount = count($list) * 90;
        }
        $amount = ($amount <= 0) ? 0 : $amount;
        echo json_encode($amount);
        break;
        //获取我的提现申请记录

    case 'getMyWithdrawApplyList':
        $openid = $arr['openid'];
        $total = 0;
        $list = DB::fetch_all("select sharepic, aid, from_unixtime(date, '%Y.%m.%d') date,title,buy_count from wf_wanba_act where template=1 and creator='" . $openid . "'");
        $mywithdrawapplylist = DB::fetch_all("select from_unixtime(date, '%Y.%m.%d %T') date,cash,status from wf_wanba_withdrawapply where openid='" . $openid . "' order by date desc");
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $total += $v['buy_count'];
            }
            $inapplying = DB::fetch_first("SELECT sum( cash ) c FROM  `wf_wanba_withdrawapply` WHERE STATUS >=0 and openid='" . $openid . "'");
            if ($inapplying) {

                $num = $inapplying['c'];
            } else {
                $num = 0;
            }
            $amount = $total * 90 - $num;
        } else {
            $amount = count($list) * 90;
        }
        $amount = ($amount <= 0) ? 0 : $amount;


        $result = array('amount' => $amount, 'list' => $mywithdrawapplylist);
        echo json_encode($result);
        break;
    case 'getMyBankCardList':
        $openid = $arr['openid'];
        $list = DB::fetch_all("select name,bank,CONCAT( '**** **** **** ' ,RIGHT(cardno,4)) cardno,cardno rawcardno from wf_wanba_bankcard where openid='" . $openid . "'");
        echo json_encode($list);
        break;

        //获取总线路月度售出账单
    case 'getAllMyRouteMonthBill':
        $openid = $arr['openid'];
        $flag = $arr['flag'];
        if ($flag == 0) {
            $list = DB::fetch_all("select concat(t.title,'(',a.title,')') title, from_unixtime(a.date, '%Y.%m.%d') date,u.nick nick  from `wf_wanba_act` a,`wf_wanba_user` u,`wf_wanba_act` t  where t.template=1 and t.creator='" . $openid . "' and a.bindtemplateid=t.aid  and a.creator=u.openid  and a.template=0 ORDER BY DATE DESC");
        } else {
            $year = $arr['year'];
            $month = $arr['month'];
            $m = $year . "-" . $month;
            $month_start = mktime(0, 0, 0, $month, 1, $year); //指定月份月初时间戳  
            $month_end = strtotime(date('Y-m-t 23:59:59', strtotime($m)));

            $list = DB::fetch_all("select concat(t.title,'(',a.title,')')  title, from_unixtime(a.date, '%Y.%m.%d') date,u.nick nick  from `wf_wanba_act` a,`wf_wanba_user` u,`wf_wanba_act` t  where t.template=1 and t.creator='" . $openid . "' and a.bindtemplateid=t.aid  and a.creator=u.openid  and a.template=0 and  a.date>=$month_start and a.date<=$month_end ORDER BY DATE DESC");
        }
        echo json_encode($list);
        break;
        //获取线路月度售出账单
    case 'getMyRouteMonthBill':
        $aid = $arr['aid'];
        $year = $arr['year'];
        $month = $arr['month'];
        $m = $year . "-" . $month;
        $month_start = mktime(0, 0, 0, $month, 1, $year); //指定月份月初时间戳  
        $month_end = strtotime(date('Y-m-t 23:59:59', strtotime($m)));
        $list = DB::fetch_all("select from_unixtime(a.date, '%Y.%m.%d') date,u.nick nick  from `wf_wanba_act` a,`wf_wanba_user` u where a.creator=u.openid and  a.bindtemplateid=$aid and a.template=0 and  a.date>=$month_start and a.date<=$month_end");

        echo json_encode($list);
        break;
    case 'checkBankCard':
        $card = $arr['card'];
        $name = $arr['name'];
        $openid = $arr['openid'];

        $url = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=' . $card .  '&cardBinCheck=true';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        $json_obj = json_decode($res, true);
        if ($json_obj['bank']) {
            $bank1 = $json_obj['bank'];
            $map = array(
                "SRCB" => "深圳农村商业银行",
                "BGB" => "广西北部湾银行",
                "SHRCB" => "上海农村商业银行",
                "BJBANK" => "北京银行",
                "WHCCB" => "威海市商业银行",
                "BOZK" => "周口银行",
                "KORLABANK" => "库尔勒市商业银行",
                "SPABANK" => "平安银行",
                "SDEB" => "顺德农商银行",
                "HURCB" => "湖北省农村信用社",
                "WRCB" => "无锡农村商业银行",
                "BOCY" => "朝阳银行",
                "CZBANK" => "浙商银行",
                "HDBANK" => "邯郸银行",
                "BOC" => "中国银行",
                "BOD" => "东莞银行",
                "CCB" => "中国建设银行",
                "ZYCBANK" => "遵义市商业银行",
                "SXCB" => "绍兴银行",
                "GZRCU" => "贵州省农村信用社",
                "ZJKCCB" => "张家口市商业银行",
                "BOJZ" => "锦州银行",
                "BOP" => "平顶山银行",
                "HKB" => "汉口银行",
                "SPDB" => "上海浦东发展银行",
                "NXRCU" => "宁夏黄河农村商业银行",
                "NYNB" => "广东南粤银行",
                "GRCB" => "广州农商银行",
                "BOSZ" => "苏州银行",
                "HZCB" => "杭州银行",
                "HSBK" => "衡水银行",
                "HBC" => "湖北银行",
                "JXBANK" => "嘉兴银行",
                "HRXJB" => "华融湘江银行",
                "BODD" => "丹东银行",
                "AYCB" => "安阳银行",
                "EGBANK" => "恒丰银行",
                "CDB" => "国家开发银行",
                "TCRCB" => "江苏太仓农村商业银行",
                "NJCB" => "南京银行",
                "ZZBANK" => "郑州银行",
                "DYCB" => "德阳商业银行",
                "YBCCB" => "宜宾市商业银行",
                "SCRCU" => "四川省农村信用",
                "KLB" => "昆仑银行",
                "LSBANK" => "莱商银行",
                "YDRCB" => "尧都农商行",
                "CCQTGB" => "重庆三峡银行",
                "FDB" => "富滇银行",
                "JSRCU" => "江苏省农村信用联合社",
                "JNBANK" => "济宁银行",
                "CMB" => "招商银行",
                "JINCHB" => "晋城银行JCBANK",
                "FXCB" => "阜新银行",
                "WHRCB" => "武汉农村商业银行",
                "HBYCBANK" => "湖北银行宜昌分行",
                "TZCB" => "台州银行",
                "TACCB" => "泰安市商业银行",
                "XCYH" => "许昌银行",
                "CEB" => "中国光大银行",
                "NXBANK" => "宁夏银行",
                "HSBANK" => "徽商银行",
                "JJBANK" => "九江银行",
                "NHQS" => "农信银清算中心",
                "MTBANK" => "浙江民泰商业银行",
                "LANGFB" => "廊坊银行",
                "ASCB" => "鞍山银行",
                "KSRB" => "昆山农村商业银行",
                "YXCCB" => "玉溪市商业银行",
                "DLB" => "大连银行",
                "DRCBCL" => "东莞农村商业银行",
                "GCB" => "广州银行",
                "NBBANK" => "宁波银行",
                "BOYK" => "营口银行",
                "SXRCCU" => "陕西信合",
                "GLBANK" => "桂林银行",
                "BOQH" => "青海银行",
                "CDRCB" => "成都农商银行",
                "QDCCB" => "青岛银行",
                "HKBEA" => "东亚银行",
                "HBHSBANK" => "湖北银行黄石分行",
                "WZCB" => "温州银行",
                "TRCB" => "天津农商银行",
                "QLBANK" => "齐鲁银行",
                "GDRCC" => "广东省农村信用社联合社",
                "ZJTLCB" => "浙江泰隆商业银行",
                "GZB" => "赣州银行",
                "GYCB" => "贵阳市商业银行",
                "CQBANK" => "重庆银行",
                "DAQINGB" => "龙江银行",
                "CGNB" => "南充市商业银行",
                "SCCB" => "三门峡银行",
                "CSRCB" => "常熟农村商业银行",
                "SHBANK" => "上海银行",
                "JLBANK" => "吉林银行",
                "CZRCB" => "常州农村信用联社",
                "BANKWF" => "潍坊银行",
                "ZRCBANK" => "张家港农村商业银行",
                "FJHXBC" => "福建海峡银行",
                "ZJNX" => "浙江省农村信用社联合社",
                "LZYH" => "兰州银行",
                "JSB" => "晋商银行",
                "BOHAIB" => "渤海银行",
                "CZCB" => "浙江稠州商业银行",
                "YQCCB" => "阳泉银行",
                "SJBANK" => "盛京银行",
                "XABANK" => "西安银行",
                "BSB" => "包商银行",
                "JSBANK" => "江苏银行",
                "FSCB" => "抚顺银行",
                "HNRCU" => "河南省农村信用",
                "COMM" => "交通银行",
                "XTB" => "邢台银行",
                "CITIC" => "中信银行",
                "HXBANK" => "华夏银行",
                "HNRCC" => "湖南省农村信用社",
                "DYCCB" => "东营市商业银行",
                "ORBANK" => "鄂尔多斯银行",
                "BJRCB" => "北京农村商业银行",
                "XYBANK" => "信阳银行",
                "ZGCCB" => "自贡市商业银行",
                "CDCB" => "成都银行",
                "HANABANK" => "韩亚银行",
                "CMBC" => "中国民生银行",
                "LYBANK" => "洛阳银行",
                "GDB" => "广东发展银行",
                "ZBCB" => "齐商银行",
                "CBKF" => "开封市商业银行",
                "H3CB" => "内蒙古银行",
                "CIB" => "兴业银行",
                "CRCBANK" => "重庆农村商业银行",
                "SZSBK" => "石嘴山银行",
                "DZBANK" => "德州银行",
                "SRBANK" => "上饶银行",
                "LSCCB" => "乐山市商业银行",
                "JXRCU" => "江西省农村信用",
                "ICBC" => "中国工商银行",
                "JZBANK" => "晋中市商业银行",
                "HZCCB" => "湖州市商业银行",
                "NHB" => "南海农村信用联社",
                "XXBANK" => "新乡银行",
                "JRCB" => "江苏江阴农村商业银行",
                "YNRCC" => "云南省农村信用社",
                "ABC" => "中国农业银行",
                "GXRCU" => "广西省农村信用",
                "PSBC" => "中国邮政储蓄银行",
                "BZMD" => "驻马店银行",
                "ARCU" => "安徽省农村信用社",
                "GSRCU" => "甘肃省农村信用",
                "LYCB" => "辽阳市商业银行",
                "JLRCU" => "吉林农信",
                "URMQCCB" => "乌鲁木齐市商业银行",
                "XLBANK" => "中山小榄村镇银行",
                "CSCB" => "长沙银行",
                "JHBANK" => "金华银行",
                "BHB" => "河北银行",
                "NBYZ" => "鄞州银行",
                "LSBC" => "临商银行",
                "BOCD" => "承德银行",
                "SDRCU" => "山东农信",
                "NCB" => "南昌银行",
                "TCCB" => "天津银行",
                "WJRCB" => "吴江农商银行",
                "CBBQS" => "城市商业银行资金清算中心",
                "HBRCU" => "河北省农村信用社"
            );
            $bank = $map[$bank1];
            if ($json_obj['cardType'] == 'DC') {
                $isexist = DB::fetch_first("select cardno from wf_wanba_bankcard where cardno='" . $card . "'");
                if ($isexist) {
                    $result = array('status' => false, "openid" => $openid, "bank" => $bank, "cardno" => $card, "name" => $name, 'msg' => '此卡号已存在');
                } else {
                    $data = array("openid" => $openid, "bank" => $bank, "cardno" => $card, "name" => $name);
                    DB::insert("wanba_bankcard", $data, "id");
                    $result = array('status' => true, "openid" => $openid, "bank" => $bank, "cardno" => $card, "name" => $name, 'msg' => '卡号校验真实有效');
                }
            } else {
                $result = array('status' => false, "openid" => $openid, "bank" => $bank, "cardno" => $card, "name" => $name, 'msg' => '不支持信用卡');
            }
        } else {
            $result = array('status' => false, "openid" => $openid, "bank" => $bank, "cardno" => $card, "name" => $name, 'msg' => '银行卡号无效');
        }
        echo json_encode($result);
        break;
    case 'getSysTeamThemesList':
        $list = DB::fetch_all("select * from wf_wanba_team_theme");
        $result = array('swiper' => $swiper, 'list' => $list);
        echo json_encode($result);
        break;

    case 'getThemeDetailById':
        $themeid = $arr['themeid'];
        $data = DB::fetch_first("select * from wf_wanba_team_theme where id=$themeid");
        $list = DB::fetch_all("select * from wf_wanba_team_theme_list where themeid=$themeid");
        $result = array('data' => $data, 'list' => $list);
        echo json_encode($result);
        break;
    case 'getDefinedTeamById':
        $aid = $arr['aid'];
        $list = DB::fetch_all("select * from wf_wanba_team_setting where aid=$aid order by displayorder");
        $d = DB::fetch_first("select teamNum from wf_wanba_act where aid=$aid");
        $actmode = DB::result_first("select mode from wf_wanba_act where aid=$aid");
        if ($d) {
            $teamNum = $d['teamNum'];
        } else {
            $teamNum = 1;
        }
        $result = array('actmode' => $actmode, 'teamNum' => $teamNum, 'list' => $list);
        echo json_encode($result);
        break;
        //修改自定义队伍
    case 'editTeamById':
        $obj = $arr['obj'];
        $objid = $obj['id'];
        $n = DB::update("wanba_team_setting", $obj, "id=$objid");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $obj);
        } else {
            $result = array('status' => false, 'msg' => '操作失败', 'data' => $obj);
        }
        echo json_encode($result);
        break;
        //新增自定义队伍
    case 'addDefinedTeam':
        $list = $arr['list'];
        $aid = $arr['aid'];
        $n = DB::delete("wanba_team_setting", "aid=$aid");
        if ($n > 0) {
            foreach ($list as $k => $v) {
                $d = array('aid' => $aid, 'themeid' => $v['themeid'], 'name' => $v['name'], 'desc' => $v['desc'], 'pic' => $v['pic'], 'color' => $v['color'], 'displayorder' => $k + 1);
                DB::insert("wanba_team_setting", $d);
            }
            $newlist = DB::fetch_all("select aid,themeid,name,`desc`,pic,color,displayorder,id from wf_wanba_team_setting where aid=$aid");

            $result = array('status' => true, 'msg' => '操作成功', 'list' => $newlist);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
        //删除自定义队伍
    case 'delTeamById':
        $teamid = $arr['id'];
        $list = $arr['list'];
        $aid = $arr['aid'];
        $n = DB::delete('wanba_team_setting', "id=$teamid");
        if ($n > 0) {
            DB::delete("wanba_team_setting", "aid=$aid");
            foreach ($list as $k => $v) {
                $d = array('aid' => $aid, 'themeid' => $v['themeid'], 'name' => $v['name'], 'desc' => $v['desc'], 'pic' => $v['pic'], 'color' => $v['color'], 'displayorder' => $k + 1);
                DB::insert("wanba_team_setting", $d);
            }
            $newlist = DB::fetch_all("select aid,themeid,name,`desc`,pic,color,displayorder,id from wf_wanba_team_setting where aid=$aid");

            $result = array('status' => true, 'msg' => '操作成功', 'list' => $newlist);
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case  'getRideOfSomeone':
        $openid = $arr['openid'];
        $update = array('currentrole' => 2, 'currentteamid' => 0);
        $n = DB::update('wanba_user', $update, "openid='" . $openid . "'");
        if ($n > 0) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;

    default:
        echo json_encode($arr);
}

function translateTXmap($gps)
{
    $url = 'https://apis.map.qq.com/ws/coord/v1/translate?locations=' . $gps . '&type=1&key=TP5BZ-Q4TWW-NMSRE-RR3OO-UXIJK-L2F2Q';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    $json_obj = json_decode($res, true);

    return $json_obj;
}
function getRandomString($length = 42)
{
    /*
	 * Use OpenSSL (if available)
	 */
    if (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
            throw new RuntimeException('Unable to generate a random string');

        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
    }

    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

function mkDirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkDirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}
function getDir($path)
{

    //判断目录是否为空
    if (!file_exists($path)) {
        return [];
    }

    $fileItem = [];

    //切换如当前目录
    chdir($path);

    foreach (glob('*.jpg') as $v) {
        $newPath = $path . DIRECTORY_SEPARATOR . $v;
        if (is_dir($newPath)) {
            $fileItem = array_merge($fileItem, getDir($newPath));
        } else if (is_file($newPath)) {

            $fileItem[] = $newPath;
        }
    }

    return $fileItem;
}

function deldir($path)
{
    //如果是目录则继续
    if (is_dir($path)) {
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $p = scandir($path);
        foreach ($p as $val) {
            //排除目录中的.和..
            if ($val != "." && $val != "..") {
                //如果是目录则递归子目录，继续操作
                if (is_dir($path . $val)) {
                    //子目录中操作删除文件夹和文件
                    deldir($path . $val . '/');
                    //目录清空后删除空文件夹
                    @rmdir($path . $val . '/');
                } else {
                    //如果是文件直接删除
                    unlink($path . $val);
                }
            }
        }
    }
}
