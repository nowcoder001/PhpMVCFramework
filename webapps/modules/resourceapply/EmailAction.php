<?php
// ***************************************************************
//  Copyright(c) Yeto
//  FileName	: EmailAction.php
//  Creator 	: John
//  Date		: 2016-08-20
//  Comment		: 邮件
// ***************************************************************
class EmailAction extends Action {
	function EmailAction() {
		$this->session = Session::getInstance();
		$this->dao = Model::getDao();
		$this->param = new Param();
	}

	public function getList()
	{
		$gid = $this->param->int_post('gid');
		if($gid <= 0)
			exit(json_encode(array("rows" => array(), "total" => 0)));
		
		$sdkid = $this->param->int_post('sdkid');
		if($sdkid <= 0)
			exit(json_encode(array("rows" => array(), "total" => 0)));
		
		$serverId = $this->param->int_post('serverid');
		if($serverId <= 0)
			exit(json_encode(array("rows" => array(), "total" => 0)));
		
		$startDate = $this->param->int_post('startDate');
		$endDate = $this->param->int_post('endDate');
			
		$sort = $this->param->sort_post();
		if($sort != 'id' && $sort != 'type' && $sort != 'create_time')
			$sort = 'id';
		$order = $this->param->order_post();
		if($sort && $order)
			$order = $sort . ' ' . $order;
		else
			$order = "id desc";
	
		$wh = " e_l.`gid` = " . $gid . " and e_l.`server_id` = " . $serverId . " and e_l.`sdkid` = " . $sdkid;
		if($startDate > 0)
			$wh .= " and `create_time` >= " . $startDate * 1000;
		if($endDate > 0 && $endDate > 0)
			$wh .= " and `create_time` < " . $endDate * 1000;
	
		$page = $this->param->page_int_post();
		$rows = $this->param->rows_int_post();
		
		$totalCount = $this->dao->getOne('SELECT COUNT(e_l.`id`) FROM ' . EMAIL_LIST . ' e_l WHERE ' . $wh);
		if($totalCount >= 0)
		{
			$select_sql = "SELECT e_l.*, u.username FROM " . EMAIL_LIST . " e_l LEFT JOIN " . USERS . " u ON e_l.operational_roles = u.uid "
				. ' WHERE ' . $wh
				. ' ORDER BY ' . $order 
				. ' LIMIT ' . ($page - 1) * $rows . ',' . $rows;
			$email_list = $this->dao->getlist($select_sql);
			exit(json_encode(array("rows" => $email_list, "total" => $totalCount)));
		}
		else 
		{
			exit(json_encode(array("rows" => array(), "total" => 0)));
		}
	}
	
	public function sendEmail()
	{
		$gid = $this->param->int_post('gid');
		if($gid <= 0)
			exit("1");
	
		$sdkid = $this->param->int_post('sdkid');
		if($sdkid <= 0)
			exit("1");
			
		$serverId = $this->param->int_post('serverid');
		if($serverId <= 0)
			exit("1");
		
		$type = $this->param->int_post('type');
		if($type < 0)
			exit("1");
		if($type != 1 && $type != 2 && $type != 3)
			exit("1");
		
		$receiverIds = "";
		if(isset($_POST['roleIds']))
			$receiverIds = $_POST['roleIds'];
		if($serverId == 1 && $receiverIds == "") // 指定角色
			exit("1");
			
		if(!isset($_POST['title']))
			exit("1");
		$title = $_POST['title'];

		if(!isset($_POST['content']))
			exit("1");
		$content = $_POST['content'];
		
		if(!isset($_POST['rewards']))
			exit("1");
		$rewards = $_POST['rewards'];

		if(!isset($_POST['rewards_desc']))
			exit("1");
		$rewards_desc = $_POST['rewards_desc'];

		if(!isset($_POST['overdue_time']))
			exit("1");
		$overdue_time = $_POST['overdue_time'];
		
		$wh = "`gid` = " . $gid . " AND `server_id` = " . $serverId . " AND `sdkid` = " . $sdkid;
		$gameInfo = $this->dao->table(GAME_SERVERS)->where($wh)->find();
		if(!$gameInfo)
			exit("2");
			
		$params = array();
		$params['type'] = $type;
		$params['title'] = $title;
		$params['content'] = $content;
		$params['rewards'] = $rewards;
		$receiverArray = explode(",", $receiverIds);
		$params['receiverIds'] = $receiverArray;

        $response = gameserver::send($gameInfo, ['a', 10006], gameserver::request_encode($params));
        
		if(!$response)
			exit("3");
		
		$pos = strpos($response, "|");
		if($pos)
		{
			$ret = json_decode(substr($response, $pos + 1, strlen($response) - 1),true);
			if($ret && $ret["result"] == 0)
			{
				$params['gid'] = $gid;
				$params['sdkid'] = $sdkid;
				$params['server_id'] = $serverId;
				$params['type'] = $type;
				$params['receiverIds'] = $receiverIds;
				$params['title'] = $title;
				$params['content'] = $content;
				$params['rewards'] = $rewards;
				$params['rewards_desc'] = $rewards_desc;
				$params['create_time'] = strtotime(date("Y-m-d H:i:s")) * 1000;
				$params['overdue_time'] = strtotime($overdue_time) * 1000;
				
				$this->dao->table(EMAIL_LIST)->insert($params);
				
// 				write_db_log("send emial  receiverIds: ".$receiverIds." rewards: ".$rewards);
				exit("0");
			}
		}
		exit("4");
	}
	
	public function delete()
	{
		$id = $this->param->int_post('id');
		if($id > 0)
		{
			$this->dao->table(EMAIL_LIST)->where("`id` = " . $id)->delete();
// 			write_db_log("delete email id: " . $id);
			exit("0");
		}
		exit("1");
	}

}
