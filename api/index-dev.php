<?php
/** Error reporting */
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
// date_default_timezone_set('PRC');
$server = "www.wondfun.com";
if ($server == "www.wondfun.com") {
    require '../../source/class/class_core.php';
} else {
    require '../../../default/d/source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz->init();
$arr = json_decode(file_get_contents("php://input"), true);
$server = "www.wondfun.com";
$act = $_GET['act'];
switch ($act) {
        //首页数据
    case 'indexData':
        $openid = $arr['openid'];

        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }

        $hot = DB::fetch_all("SELECT a.aid,a.sharepic,a.title,a.date FROM `wf_wanba_user` u,`wf_wanba_act` a where u.openid='" . $openid . "' and u.currentaid=a.aid and a.status<5");
        if ($hot) {
            if ($hot[0]['aid'] > 1) {
                //$demo=array('aid'=>1,'title'=>'玩霸江湖Demo','sharepic'=>'1.jpg','date'=>0);
                //$hot[]=$demo;
            }
        } else {
            //$demo=array('aid'=>1,'title'=>'玩霸江湖Demo','sharepic'=>'1.jpg','date'=>0);
            //$hot[]=$demo;
        }

        echo json_encode(array('swiper' => $swiper, 'hot' => $hot));
        break;
        //推荐列表
    case 'recommandData':
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
            $hot = DB::fetch_all("SELECT aid,pic,title,summary,`from`,`fromurl` FROM `wf_portal_article_title` where catid=105 and tag=4 order by aid desc");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
            $hot = DB::fetch_all("SELECT aid,pic,title,summary,`from`,`fromurl` FROM `wf_portal_article_title` where catid=1 and tag=4 order by aid desc");
        }
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
        $data = DB::fetch_first("select `aid`, `title`, from_unixtime(date, '%Y-%m-%d') date,  `sharepic`, `teamnum`,logopic,slogan from wf_wanba_act where aid=$aid and creator='" . $openid . "'");
        echo json_encode($data);
        break;
    case 'actInfo':
        $openid = $arr['openid'];

        $aid = $arr['aid'];
        $teamid = $arr['teamid'];
        $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($task as $k => $v) {
            $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
        }
        $act = DB::fetch_first("SELECT * FROM  `wf_wanba_act`  where aid=$aid");
        $teams = DB::fetch_all("select * from wf_wanba_team_setting where aid=$aid and displayorder<=$act[teamNum]");
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
        $myteam = DB::fetch_all("SELECT openid,nick,avatar FROM  `wf_wanba_user`  where currentaid=$aid and currentteamid=$teamid");
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
            $data = array('value' => $sessionvalue, 'name' => $sessionname);
            DB::insert('wanba_session', $data);
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
            $task = DB::fetch_first("select name,pvalue,owner,ptype,displayorder,mine,memo from wf_wanba_task where taskid=$v[taskid]");
            $data[$k]['pvalue'] = $task['pvalue'];
            $data[$k]['name'] = $task['name'];
            $data[$k]['owner'] = $task['owner'];
            $data[$k]['ptype'] = $task['ptype'];
            $data[$k]['displayorder'] = $task['displayorder'];
            $data[$k]['mine'] = $task['mine'];
            $data[$k]['memo'] = $task['memo'];
            $team = DB::fetch_first("select name,pic,color from wf_wanba_team_setting where aid=$aid and displayorder=$v[teamid]");
            $data[$k]['team'] = $team['name'];
            $data[$k]['flag'] = $team['pic'];
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
        $data['pic'] = DB::fetch_all("SELECT * FROM  `wf_wanba_pic` where taskid=$data[taskid]");

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
                    $money = 2 * $mine;
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => 0 - 2 * intval($mine), 'date' => time(), 'event' => '在' . $posid . '号地触雷，损失' . $money);
                    $id = DB::insert('wanba_logs', $data, 'id');
                    //布雷者加倍
                    $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $currentowner[owner], 'score' => 2 * intval($mine), 'date' => time(), 'event' => '有人踩了' . $posid . '号地雷，你获得了' . $money);
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
        $arr1 = array(1, 9, 17, 25, 33, 41, 49, 7, 13, 19, 31, 37, 43);

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
                        $money = 2 * $mine;
                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $teamid, 'score' => 0 - 2 * intval($mine), 'date' => time(), 'event' => '在' . $posid . '号地触雷，损失' . $money);
                        $id = DB::insert('wanba_logs', $data, 'id');
                        //布雷者加倍

                        $data = array('aid' => $aid, 'taskid' => $taskid, 'teamid' => $currentowner[owner], 'score' => 2 * intval($mine), 'date' => time(), 'event' => '有人踩了' . $posid . '号地雷，你获得了' . $money);
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
        $hasposted = ($posid == 25) ? false : DB::fetch_first(" select pass from wf_wanba_pass where pass >= 0  and and aid = $aid and  teamid = $teamid and taskid = $taskid ");
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
        $data = array('aid' => $aid, 'score' => $score, 'teamid' => $teamid, 'token' => $token, 'date' => time(), 'event' => '获得加分' . $score);
        $id = DB::insert('wanba_logs', $data, 'id');
        if ($id) {
            echo json_encode(array('status' => true, 'msg' => '操作成功', 'id' => $id));
        } else {
            echo json_encode(array('status' => false, 'msg' => '操作失败'));
        }
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
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0  where aid=$aid";
        DB::query($sql);
        $sql = "update  `wf_wanba_user` set `currentaid`=0,`currentteamid`=0,`currentrole`=2  where `currentaid`=$aid";
        DB::query($sql);
        $data = array('mode' => $mode, 'status' => -1);
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
        //$pvalue = 300;
        $gps = intval($arr['gps']);
        $offset = ($gps > 0) ? $gps : 0;
        $gps = ($gps > 0) ? 1 : 0;
        $mineNum = $arr['mineNum'];
        $mineMoney = $arr['mineMoney'];
        $data = array('minenum' => $mineNum, 'minevalue' => $mineMoney, 'gpsEnabled' => $gps, 'offset' => $offset);
        DB::update("wanba_act", $data, "aid=$aid");
        $data = array('pvalue' => $pvalue);
        DB::update("wanba_task", $data, "aid=$aid");
        if ($aid == 11 || $aid == 12) {
            $data = array('pvalue' => 0);
            DB::update("wanba_task", $data, "aid=$aid and displayorder=25");
        }
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
        $log = array('event' => $teamname . '获得了一颗' . $stonename, 'status' => 1, date => time(), 'aid' => $aid);
        DB::insert('wanba_logs', $log);
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
            $log = array('event' => $teamname . '获得了一颗' . $stoneinfo[name], 'status' => 1, date => time(), 'aid' => $aid);
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
        $sql = "update `wf_wanba_task`  set owner=null,gps=0,open=0,mine=0  where aid=$aid";
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
        $stones = DB::fetch_first("select stone1,stone2,stone3,stone4,stone5,stone6  from wf_wanba_team_setting where aid=$aid and displayorder=$myteam");
        $stone1 = $stones['stone1'] - 1;
        $stone2 = $stones['stone2'] - 1;
        $stone3 = $stones['stone3'] - 1;
        $stone4 = $stones['stone4'] - 1;
        $stone5 = $stones['stone5'] - 1;
        $stone6 = $stones['stone6'] - 1;
        $data = array('stone1' => $stone1, 'stone2' => $stone2, 'stone3' => $stone3, 'stone4' => $stone4, 'stone5' => $stone5, 'stone6' => $stone6);
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
        }
        $stonesleft = array('total' => count($stones), 'stone1' => $stone1, 'stone2' => $stone2, 'stone3' => $stone3, 'stone4' => $stone4, 'stone5' => $stone5, 'stone6' => $stone6);
        //宝石生成记录
        $stonesMadehistory = DB::fetch_all("select from_unixtime(date, '%H:%i:%s') date, event from wf_wanba_logs where aid=$aid and status=2 order by date desc");
        //各队的宝石情况
        $teamStones = DB::fetch_all("select displayorder,name,stone1,stone2,stone3,stone4,stone5,stone6 from wf_wanba_team_setting where aid=$aid");
        foreach ($teamStones as $k => $v) {
            $teamStones[$k]['unused'] = $v['stone1'] + $v['stone2'] + $v['stone3'] + $v['stone4'] + $v['stone5'] + $v['stone6'];
            //$teamStones[$k]['detail']=DB::fetch_all("select * from `wf_wanba_logs`  where aid=$aid and ((teamid=$v[displayorder] and status=0) or status=1) and event like '%使用了__宝石%'");
            $teamStones[$k]['detail'] = DB::fetch_all("select from_unixtime(date, '%H:%i:%s') date, score, memo, event, id,status from `wf_wanba_logs`  where aid=$aid and ((teamid=$v[displayorder] and status=0) or status=1) and event like '%使用了__宝石%'");
        }
        $result = array('stonesleft' => $stonesleft, 'teamStones' => $teamStones, 'stonesMadehistory' => $stonesMadehistory);

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
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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

        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }

        $pagesize = ($arr['pagesize']) ? $arr['pagesize'] : 10;

        $pageindex = ($arr['currentpage']) ? $arr['currentpage'] * $pagesize : 0;
        $limit = " limit " . $pageindex . "," . $pagesize;
        $list = DB::fetch_all("SELECT `questionid`, `memo`, `qtype`, `answer`, `creator`, `lastpost`, `cat`, `sys`, `url`, `media`, `tag` FROM `wf_wanba_question` where sys=0 and creator= '" . $openid . "' order by questionid " . $limit);
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

        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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
    case 'newAct':
        $openid = $arr['openid'];
        $actdata = $arr['actdata'];
        $teamnum = $actdata['teamNum'];
        $data = array('creator' => $openid, 'title' => $actdata['title'], 'date' => strtotime($actdata['date']), 'teamnum' => $teamnum);
        $id = DB::insert('wanba_act', $data, 'aid');
        $teamsetting = DB::fetch_all("SELECT `id`, `aid`, `displayorder`, `name`, `desc`, `pic`, `color`  from wf_wanba_team_setting where aid=1 order by displayorder");
        if ($id) {
            for ($i = 0; $i < $teamnum; $i++) {
                $dt = $teamsetting[$i];
                $d = array('aid' => $id, 'displayorder' => $dt['displayorder'], 'name' => $dt['name'], 'desc' => $dt['desc'], 'pic' => $dt['pic'], 'color' => $dt['color']);

                DB::insert('wanba_team_setting', $d);
            }
            $list = DB::fetch_all("SELECT * FROM `wf_wanba_act` where status<5 and creator='" . $openid . "' order by aid desc");
            $result = array('status' => true, 'msg' => '恭喜你成功创建了一个活动，你可以继续设置完善。', 'list' => $list, 'aid' => $id);
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

        $pic = $actdata['pic'];
        $piclogo = $actdata['picLogo'];
        if ($pic) {
            $ft = strrpos($pic, '.', 0);
            $fp = strrpos($pic, '/', 0);
            $fm = substr($pic, $fp + 1, $ft);
            $fe = substr($pic, $ft);
            $sharepic = $fm;
        } else {
            $sharepic = '1.jpg';
        }
        if ($piclogo) {
            $ft = strrpos($piclogo, '.', 0);
            $fp = strrpos($piclogo, '/', 0);
            $fm = substr($piclogo, $fp + 1, $ft);
            $fe = substr($piclogo, $ft);
            $piclogo = $fm;
        } else {
            $logoepic = 'default.jpg';
        }


        $data = array('title' => $actdata['title'], 'date' => strtotime($actdata['date']), 'teamnum' => $teamnum, 'sharepic' => $sharepic, 'logopic' => $piclogo, 'slogan' => $actdata['text']);
        //更新活动
        DB::update('wanba_act', $data, "aid=$aid");

        $oldteamnum = DB::result_first("select teamNum from wf_wanba_act where aid=$aid");
        $teamnum = ($teamnum >= $oldteamnum) ? $teamnum : $oldteamnum;
       // if ($oldteamnum != $teamnum) {
            //删除旧队伍设置
            DB::delete('wanba_team_setting', "aid=$aid");
            //更新队伍设置
            $teamsetting = DB::fetch_all("SELECT `id`, `aid`, `displayorder`, `name`, `desc`, `pic`, `color`  from wf_wanba_team_setting where aid=1 order by displayorder");

            for ($i = 0; $i < $teamnum; $i++) {
                $dt = $teamsetting[$i];
                $d = array('aid' => $aid, 'displayorder' => $dt['displayorder'], 'name' => $dt['name'], 'desc' => $dt['desc'], 'pic' => $dt['pic'], 'color' => $dt['color']);

                DB::insert('wanba_team_setting', $d);
            }
        //}
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
            $data = array('creator' => $openid, 'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);
            $id = DB::update('wanba_question', $data, "questionid=$qid");
            if ($id) {
                $data = array('questionid' => $qid, 'creator' => $openid, 'memo' => $questiondata['memo'], 'lastpost' => time(), 'qtype' => $questiondata['index'], 'answer' => $questiondata['answer']);

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
            $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
        }
        $act = DB::fetch_first("SELECT * FROM  `wf_wanba_act`  where aid=$aid");
        $total = DB::fetch_first("select sum(score) score from wf_wanba_logs where taskid=-1 and aid=$aid");
        $act['redbagsum'] = ($total) ? $total[score] : -1;
        $data = array('task' => $task,  'act' => $act);
        echo json_encode($data);

        break;
        //保存点位任务设置
    case 'savePoi':
        $openid = $arr['openid'];
        $poiInfo = $arr['poiInfo'];
        $taskid = intval($poiInfo['taskid']);
        $pics = $poiInfo['pics'];
        $displayorder = $poiInfo['displayorder'];
        $p = array(1, 7, 9, 13, 17, 19, 31, 33, 37, 41, 43, 49);
        if (in_array($displayorder, $p)) {
            $ptype = 1;
        } else if ($displayorder == 25) {
            $ptype = 2;
        } else {
            $ptype = 0;
        }
        if ($taskid > 0) {
            //删除原有图片
            DB::delete('wanba_pic', "taskid=$taskid");
            $data = array('ptype' => $ptype, 'name' => $poiInfo['name'], 'pmemo' => $poiInfo['pmemo'], 'latlng' => $poiInfo['latlng'], 'poi' => $poiInfo['poi'], 'memo' => $poiInfo['memo'], 'answer' => $poiInfo['answer'], 'qtype' => $poiInfo['qtype'], 'open' => $poiInfo['open'], 'gps' => $poiInfo['gps'], 'url' => $poiInfo['url'], 'media' => $poiInfo['media']);
            $id = DB::update('wanba_task', $data, "taskid=$taskid");


            foreach ($pics as $k => $v) {
                $new = array('url' => $v[url], 'taskid' => $taskid, 'displayorder' => $v[displayorder]);
                DB::insert('wanba_pic', $new);
            }

            $data = DB::fetch_first("SELECT * FROM  `wf_wanba_task`  where taskid=$taskid");
            $data['pics'] = DB::fetch_all("SELECT * FROM  `wf_wanba_pic`  where taskid=$taskid order by picid");
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } else {
            $data = array('ptype' => $ptype, 'name' => $poiInfo['name'], 'aid' => $poiInfo['aid'], 'pmemo' => $poiInfo['pmemo'], 'displayorder' => $poiInfo['displayorder'], 'latlng' => $poiInfo['latlng'], 'poi' => $poiInfo['poi'], 'memo' => $poiInfo['memo'], 'answer' => $poiInfo['answer'], 'qtype' => $poiInfo['qtype'], 'url' => $poiInfo['url'], 'media' => $poiInfo['media']);
            $id = DB::insert('wanba_task', $data, 'taskid');
            if ($id) {
                //插入新图片
                foreach ($pics as $k => $v) {
                    $new = array('url' => $v[url], 'taskid' => $id, 'displayorder' => $v[displayorder]);
                    DB::insert('wanba_pic', $new);
                }

                $data = DB::fetch_first("SELECT * FROM  `wf_wanba_task`  where taskid=$id");
                $data['pics'] = DB::fetch_all("SELECT * FROM  `wf_wanba_pic`  where taskid=$id order by picid");
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
        $where = " WHERE (`template`=1) ";
        $order = ($arr['order']) ? $arr['order'] : 0;
        $order = ($order == 0) ? ' order by aid desc ' : ' order by aid desc ';
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
            $url = 'https://www.wondfun.com/wanba/api/sharepic/';
            $list = DB::fetch_all("SELECT `aid`, `title`,route_desc,concat('" . $url . "',sharepic) sharepic,from_unixtime(date, '%y-%m-%d') date FROM `wf_wanba_act` " . $where . $order . $limit);
            $c = DB::fetch_first("SELECT count(aid) c FROM `wf_wanba_act` " . $where);
            if ($c) {
                $total = $c['c'];
            } else {
                $total = 0;
            }
        } else {
            $url = 'https://www.wondball.com/wanba/api/sharepic/';
            $list = DB::fetch_all("SELECT `aid`, `title`,route_desc,concat('" . $url . "',sharepic) sharepic,from_unixtime(date, '%y-%m-%d') date FROM `wf_wanba_act`  " . $where . $order . $limit);
            $c = DB::fetch_first("SELECT count(aid) c FROM `wf_wanba_act` " . $where);
            if ($c) {
                $total = $c['c'];
            } else {
                $total = 0;
            }
        }
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=104 and tag=2");
        } else {

            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
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

        $result = array('swiper' => $swiper, 'list' => $list, 'cities' => $t, 'tags' => $tt, 'total' => $total);
        echo json_encode($result);
        break;
    case 'useTemplate':
        $templateid = $arr['templateid'];
        $aid = $arr['aid'];
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
            $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
            foreach ($task[$k]['pics'] as $ke => $va) {
                $d = array('taskid' => $newtaskid, 'url' => $va['url'], 'displayorder' => $va['displayorder']);
                DB::insert('wanba_pic', $d);
            }
        }
        $result = array('status' => true, 'msg' => '操作成功');
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
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
        $result = array('swiper' => $swiper, 'cat' => $cat);
        echo json_encode($result);
        break;
    case 'getMyPos':
        $creator = $arr['openid'];
        $list = DB::fetch_all("SELECT `address`, `pmemo`, `poi`, `latlng`, `name`  FROM `wf_wanba_point` where creator='" . $creator . "' order by lastpost desc");
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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
        $data = DB::fetch_all("select * from wf_wanba_question_pic where questionid=$qid");
        echo json_encode($data);
        break;
    case 'myPayInfo':
        $openid = $arr['openid'];
        $data = DB::fetch_first("select point,memberid from wf_wanba_user where openid='" . $openid . "'");
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
        //$dt=array('point'=>$point,'tel'=>$tel);
        //DB::update('wanba_user',$dt,"openid='".$openid."'");
        DB::query("update wf_wanba_user set point=point+999,tel='" . $tel . "' where openid='" . $openid . "'");
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
                if ($now - $token < 2 * 60) {
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

    case 'batchImportPos':
        $aid = $arr['aid'];
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
        $result = array('status' => true, 'msg' => '操作成功');
        echo json_encode($result);
        break;
    case 'getTaskList':
        $openid = $arr['openid'];
        $aid = $arr['aid'];
        $task = DB::fetch_all("SELECT * FROM  `wf_wanba_task`  where aid=$aid order by displayorder");
        foreach ($task as $k => $v) {
            $task[$k]['pics'] = DB::fetch_all("select * from   `wf_wanba_pic` where taskid=$v[taskid]  order by picid");
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
        if ($server == "www.wondfun.com") {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where  catid=104 and tag=2");
        } else {
            $swiper = DB::fetch_all("SELECT aid,pic,title,summary FROM `wf_portal_article_title` where catid=1 and tag=2");
        }
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
        echo json_encode($list);
        break;
    case 'getRouteList':
        $list = DB::fetch_all("SELECT title,route_desc from wf_wanba_act where template=1 order by aid desc");
        echo json_encode($list);
        break;
    case 'getRouteDetail':
        $aid = $arr['aid'];
        $applyid = $arr['applyid'];
        $d = DB::fetch_all("select * from wf_wanba_task where aid=$aid order by displayorder");
        foreach ($d as $k => $v) {
            $d[$k]['pics'] = DB::fetch_all("select url from wf_wanba_pic where taskid=$v[taskid]");
        }
        $data = array('applyid' => $applyid, 'detail' => $d);
        echo json_encode($data);
        break;
    case  'postRouteApply':
        $aid = $arr['aid'];
        $title = $arr['title'];
        $route_desc = $arr['route_desc'];
        $applyopenid = $arr['uid'];

        $data = array('aid' => $aid, 'title' => $title, 'memo' => $route_desc, 'applyopenid' => $applyopenid, 'applydate' => time());
        $id = DB::insert('wanba_routeapply', $data, "id");
        if ($id) {
            $result = array('status' => true, 'msg' => '操作成功');
        } else {
            $result = array('status' => false, 'msg' => '操作失败');
        }
        echo json_encode($result);
        break;
    case 'reviewRouteApply':
        $applyid = $arr['applyid'];
        $aid = $arr['aid'];
        $title = $arr['title'];
        $route_desc = $arr['route_desc'];
        $status = $arr['status'];
        $reason = $arr['reason'];
        $data = array('status' => $status, 'reason' => $reason);
        $n = DB::update('wanba_routeapply', $data, "id=$applyid");
        if ($status == 1) {
            $creator = DB::result_first("select creator from wf_wanba_act where aid=$aid");
            $d = array("title" => $title, "route_desc" => $route_desc, "date" => time(), "creator" => $creator, "template" => 1);
            $newaid = DB::insert("wanba_act", $d, "aid");
            $tasks = DB::fetch_all("SELECT `taskid`, `name`, `displayorder`,`pmemo`, `memo`, `qtype`, `answer`, `poi`, `ptype`,pvalue, $newaid aid,latlng,media,url FROM  `wf_wanba_task` where aid=$aid");
            foreach ($tasks as $k => $v) {
                $pics = DB::fetch_all("select url from wf_wanba_pic where taskid=$v[taskid]");
                $insert = array("name" => $v['name'], "displayorder" => $v['displayorder'], "pmemo" => $v['pmemo'], "memo" => $v['memo'], "qtype" => $v['qtype'], "answer" => $v['answer'], "poi" => $v['poi'], "ptype" => $v['ptype'], "aid" => $v['aid'], "latlng" => $v['latlng'], "media" => $v['media'], "url" => $v['url'], "pvalue" => $v['pvalue']);

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

    default:
        echo json_encode($arr);
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
