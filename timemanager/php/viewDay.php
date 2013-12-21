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

$dat1 = $_GET['dat'];

$dao_user = new DaoUserImpl;

$user = $dao_user->getUserFromLoginAndPassword($login, $password);

$id = $user->getId();


$query = "SELECT * FROM waqit_event where event_user_id = ".$id." ORDER BY event_start asc";
 

 $res = mysql_query($query) or die(mysql_error());
 
$arr[] = array();
$index = 0;
while ($row = mysql_fetch_array($res)) {

$dat = $row['event_start'];

if($dat1 != null){
$dat2 = strtotime($dat1);
$current_date = date("y-m-d", $dat2);
}
else{
$current_date = date("y-m-d");
}
$date_start = strtotime($dat);
$date_event = date("y-m-d", $date_start);

if($current_date == $date_event){


$time_start = date("h:i", $date_start);//05:04

$duration = $row['event_duration'];
$type = $row['event_type'];
$description = $row['event_description'];

$time_finish = date( "h:i", strtotime( $dat."+$duration hours" ));

$arr[$index] = array('dat'=>$current_date, 'time_start' => $time_start, 'time_finish'=> $time_finish, 'description'=> $description, 
'type' => $type, 'duration'=> $duration);
$index++;
}
}


try{

$loader = new Twig_Loader_Filesystem('../html');

$twig = new Twig_Environment($loader);

$template = $twig->loadTemplate('viewDay.htm');


echo $template->render(array(
'arr'=> $arr
));
}
catch(Exception $e){
die('ERROR'.$e->getMessage());
}




?>