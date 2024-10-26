<?php

function connectDB(){
	$config = parse_ini_file("db.ini");
	$dbh = new PDO($config['dsn'], $config['username'], $config['password']);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function authenticate($user, $pass){
	try{
		$dbh = connectDB();
		$statement = $dbh->prepare("SELECT count(*) FROM users ".
						"where name = :username and password = sha2(:password,256) ");
		$statement->bindParam(":username", $user);
		$statement->bindParam(":password", $pass);
		$result = $statement->execute();
		$row = $statement->fetch();
		$dbh = null;
		
		return $row[0];
		
	}catch(PDOException $e){
		print "Error!" . $e->getMessage(). "<br/>";
		die();
	}
}

function register($user,$pass){
	try{
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM users ".
        				"where name = :username ");
        $statement->bindParam(":username", $user);
        $result = $statement->execute();
        $row = $statement->fetch();
        if($row[0] != 1){
            $statement = $dbh->prepare("call create_user(:username, :pass)");
            $statement->bindParam(":username", $user);
            $statement->bindParam(":pass", $pass);
            $statement->execute();
            $dbh = null;
    
            return 1;
        }
        else{
            return 0;
        }
		
        }catch(PDOException $e){
                print "Error!" . $e->getMessage() . "<br/>";
                die();
        }
}



?>
