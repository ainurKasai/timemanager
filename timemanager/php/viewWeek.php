<?php

session_start();

include_once('../Beans/person_waqit.class.php');
include_once('../Interface/DaoUserInt.php');
include_once('../Dao/DaoUserImpl.php');
include '..\lib\Twig\AutoLoader.php';
Twig_Autoloader::register();



/* Соединяемся с базой данных */
$hostname = "localhost"; // название/путь сервера, с MySQL
$username = "root"; // имя пользователя (в Denwer`е по умолчанию "root")
$password = ""; // пароль пользователя (в Denwer`е по умолчанию пароль отсутствует, этот параметр можно оставить пустым)
$dbName = "waqit"; // название базы данных
 
/* Таблица MySQL, в которой будут храниться данные */
$table = "waqit_event";
 

mysql_connect($hostname, $username, $password) or die ("Не могу создать соединение");
 
/* Выбираем базу данных. Если произойдет ошибка - вывести ее */
mysql_select_db($dbName) or die (mysql_error());
  
$login = $_SESSION['login'];
$password = $_SESSION['password'];

$dao_user = new DaoUserImpl;

$user = $dao_user->getUserFromLoginAndPassword($login, $password);

$id = $user->getId();


///$query = "SELECT * FROM waqit_event where event_user_id = ".$id;
 

 //$res = mysql_query($query) or die(mysql_error());
 
$arr[] = array();

$i = 0;
while($i < 7){
$cur_day = date ("Y-m-d", time() - (date("N")-$i) * 24*60*60); 

$duration = 0;

	$sql = "SELECT * FROM waqit_event WHERE event_user_id = ".$id." AND
	DATE_FORMAT(event_start, '%Y-%m-%d')='".mysql_real_escape_string($cur_day)."' ";

$res1 = mysql_query($sql) or die(mysql_error());
		
	
	while($row = mysql_fetch_array($res1)){
	$duration = $duration + $row['event_duration'];
	}

	$start_date = getdate(time() - (date("N")-$i) * 24*60*60);
$number = $start_date['wday'];
$name = $start_date['weekday'];
$day = $start_date['mday'];
$month = $start_date['month'];
$year = $start_date['year'];
$free_time = 24 - $duration;
$arr[$i] = array('dat'=> $cur_day,'name'=> $name, 'day' => $day, 'month'=> $month, 'year'=> $year, 
'free_time' => $free_time);
$i++;
}
	


try{

$loader = new Twig_Loader_Filesystem('../html');

$twig = new Twig_Environment($loader);

$template = $twig->loadTemplate('viewWeek.htm');


echo $template->render(array(
'arr'=> $arr
));
}
catch(Exception $e){
die('ERROR'.$e->getMessage());
}


?>