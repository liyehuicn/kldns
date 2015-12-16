<?php
@header('Content-Type: text/html; charset=UTF-8');
$step=isset($_GET['step'])?$_GET['step']:1;
$action=isset($_POST['action'])?$_POST['action']:null;
$finish=1;
if($action=='install'){
	$host=isset($_POST['host'])?$_POST['host']:null;
	$port=isset($_POST['port'])?$_POST['port']:null;
	$user=isset($_POST['user'])?$_POST['user']:null;
	$pwd=isset($_POST['pwd'])?$_POST['pwd']:null;
	$database=isset($_POST['database'])?$_POST['database']:null;
	if(empty($host) || empty($port) || empty($user) || empty($pwd) || empty($database)){
		$errorMsg='请填完所有数据库信息';
	}else{
		$mysql['host']=$host;
		$mysql['port']=$port;
		$mysql['database']=$database;
		$mysql['username']=$user;
		$mysql['password']=$pwd;
		try{
			$db=new PDO("mysql:host=".$mysql['host'].";dbname=".$mysql['database'].";port=".$mysql['port'],$mysql['username'],$mysql['password']);
		}catch(Exception $e){
			$errorMsg='链接数据库失败:'.$e->getMessage();
		}
		if(empty($errorMsg)){
			$config['db']=$mysql;
			@file_put_contents('../inc/config.php','<?php'.PHP_EOL.'return '.var_export($config,true).';'.PHP_EOL.PHP_EOL.'?>');
			$db->exec("set names utf8");
			$sqls=file_get_contents('install.sql');
			$sqls=explode(';', $sqls);
			$success=0;$error=0;$errorMsg=null;
			foreach ($sqls as $value) {
				if(!is_null(trim($value))){
					if($db->exec($value)===false){
						$error++;
						$errorMsg.=$db->errorInfo()[2]."<br>";
					}else{
						$success++;
					}
				}
			}
			$step=3;

		}
	}

}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">     
		<meta content="yes" name="apple-mobile-web-app-capable">     
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>快乐二级域名分发系统-安装程序</title>
		<link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
		<link href="/assets/css/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="container"><br>
			<div class="row">
				<div class="col-xs-12">
					<pre><h4>快速安装程序</h4></pre>
				</div>
				<div class="col-xs-12">
					<div class="panel panel-warning">
						<?php
						if(isset($errorMsg)){
							echo '<div class="alert alert-danger text-center" role="alert">'.$errorMsg.'</div>';
						}
						if($step==2){
						?>

						<div class="panel-heading text-center">MYSQL数据库信息配置</div>
						<div class="panel-body">
							<div class="list-group text-success">
								<form class="form-horizontal" action="#" method="post">
									<input type="hidden" name="action" class="form-control" value="install">
									<div class="form-group">
										<label class="col-sm-2 control-label">数据库地址</label>
										<div class="col-sm-10">
											<input type="text" name="host" class="form-control" value="127.0.0.1">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">数据库端口</label>
										<div class="col-sm-10">
											<input type="text" name="port" class="form-control" value="3306">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">数据库库名</label>
										<div class="col-sm-10">
											<input type="text" name="database" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">数据库用户名</label>
										<div class="col-sm-10">
											<input type="text" name="user" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">数据库密码</label>
										<div class="col-sm-10">
											<input type="password" name="pwd" class="form-control">
										</div>
									</div>
									<div class="form-group">	
  										<div class="col-sm-offset-2 col-sm-10">
											<button type="submit" class="btn btn-success btn-block">确认无误，下一步</button>
										</div>
									</div>
									
			                    </form>
							</div>

						</div>
						<?php }elseif($step==3){ ?>
						<div class="panel-heading text-center">程序安装完成</div>
						<div class="panel-body">
							<ul class="list-group">
							  <li class="list-group-item">成功执行SQL语句<?php echo $success;?>条，失败<?php echo $error;?>条！</li>
							  <li class="list-group-item">1、后台地址为域名/admin  默认管理员密码为123456</li>
							  <li class="list-group-item">2、程序目前支持dnspod、aliyun、cloudxns平台的域名的解析！其他平台后面陆续添加！</li>
							  <li class="list-group-item ">3、程序使用过程中有任何问题或者BUG反馈，可以发送邮件至：815856515@qq.com。</li>
							  <li class="list-group-item">4、本程序为完全开源程序，程序作者：快乐是福</li>
							  <a href="/" class="btn list-group-item">进入网站首页</a>
							  <a href="/admin" class="btn list-group-item">进入管理后台</a>
							</ul>
						</div>
						<?php }else{ ?>
						<div class="panel-heading text-center">安装环境检测</div>
						<div class="panel-body">
							<?php
							$install=true;
							if(!file_exists('../inc/config.php')){
								$check[2]='<span class="badge">未锁定</span>';
							}else{
								$check[2]='<span class="badge">已锁定</span>';
								$install=false;
							}
							if(class_exists("PDO")){
								$check[0]='<span class="badge">支持</span>';
							}else{
								$check[0]='<span class="badge">不支持</span>';
								$install=false;
							}
							if($fp = @fopen("../inc/test.txt", 'w')) {
					            @fclose($fp);
					            @unlink("../inc/test.txt");
					           $check[1]='<span class="badge">支持</span>';
					        }else{
								$check[1]='<span class="badge">不支持</span>';
								$install=false;
							}

							?>
							<ul class="list-group">
							  <li class="list-group-item">检测安装是否锁定 <?php echo $check[2];?></li>
							  <li class="list-group-item">PDO_MYSQL组件 <?php echo $check[0];?></li>
							  <li class="list-group-item">INC目录写入权限 <?php echo $check[1];?></li>
							  <li class="list-group-item">成功安装后安装文件就会锁定，如需重新安装，请手动删除inc目录下的config.php配置文件！</li>
							  <?php
							  if($install) echo'<a href="?step=2" class="btn list-group-item">检测通过，下一步</a>';
							  ?>
							</ul>
						</div>
						<?php } ?>
						
					</div>
				</div>
                
            </div>

			<footer class="footer">
				<pre><center>Powered by KuaiLeShiFu ! QQ:815856515</center></pre>
            </footer>
        </div>
    </body>
</html>