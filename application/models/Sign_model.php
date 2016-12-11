<?php 
class Sign_model extends CI_Model{
	public function __construct(){
		$this->load->database();
		$this->load->model('Archives_model');
		$id=0;
	}
	public function test(){
		$id = 2;
		//$sql = "UPDATE news SET ID = ".$this->db->escape($id);
		$sql = "SELECT * FROM news";
		$results = $this->db->query($sql);
		$d=array();
		echo gettype($d);
	}
	//增
	public function add($tablename,$data){
		$this->db->insert($tablename,$data);
	}

	//删
	public function delete($tablename,$key){
		$this->db->delete($tablename,$key);
	}

	//查
	public function select($selectsql){
		$query = $this->db->query($selectsql);
		return $query;
	}

	//改
	public function update($tablename,$data,$updatepair){
		$this->db->update($tablename,$data,$updatepair);
	}



	/**
	*  检查账号：传入账号，返回数据库中是否已经存在该账号
	*
	*	@param account varcahr(40) 
	*	@return true->账号已经存在， false->账号不存在
	*/
	public function AccountExist($account=0){
		$query = $this->db->get_where("e0_user",array("Account" => $account));
		//数据库中有记录表示该账号已经存在
		if($query->num_rows()){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	*	添加文档：传入账号邮箱，在数据库中插入信息
	*	 @param account varchar(40)
	*	 @param password varchar(20)
	*/
	public function InsertAccount($account,$password){
		$signuptime=date("Y-m-d H:i:s");//注册时间，放到profile中去
		$id = md5($signuptime);
		$data = array(
			'id' => $id,
			'account' => $account,
			'password' => $password,
			'profile' => $signuptime,
			'logintime' => $signuptime,
			'permission' => 0
		);
		$this->db->insert('e0_user',$data);
	}

	/**
	*	检查文档：登陆时查找数据库中是否有该账号和密码，以及检查该账号是否可用
	*	Permission: 0->未激活，1->激活， 2->账号被封
	*	@param account varchar(40)
	*	@param password varchar(20)
	*	@return 1->未激活，2->激活， 3->账号被封， 4->密码错误， 5->账号不存在
	*/
	public function CheckAccount($account,$password){	
		//账号存在,检查密码是否正确
		$query = $this->db->query("select * from e0_user where Account = '$account' ");
		echo $query->num_rows();
		if($query->num_rows() <> 0){
			//有该账号，检查密码是否正确
			foreach($query->result() as $row){
				//密码正确
				if($row->password === $password){
					//检查该账号状态
					if($row->Permission === "0"){
						return 1;//未激活
					}
					else if($row->Permission === "1"){
						return 2;//激活状态
					}
					else if($row->Permission === "2"){
						return 3;//账号被封
					}
				}
			}
			return 4;//密码不正确
		}
		//账号不存在
		else{
			return 5;
		}
	}

	/**
	*	检查账号是否已激活文档
	*	@param account varchar(40)
	*	@return 已激活返回1，未激活先激活然后返回0
	*/
	public function CheckActivate($account=0){
		$query = $this->db->get_where('e0_user',array("Account" => $account));
		//检查是否已经被激活
		//如果之前未激活先激活再返回
		$row = $query->row();
		if($row->Permission === "0"){
			$this->db->update('e0_user',array("Permission" => "1"),array("Account" => $account));
			return 0;//
		}
		else{
			return 1;//已激活
		}
	}
}
	



 ?>