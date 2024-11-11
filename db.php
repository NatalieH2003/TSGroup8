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
            
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
            $statement->bindParam(":username", $user);
            $statement->execute();
            $row = $statement->fetch();
            $userID = $row[0];
            
            $dbh = connectDB();
            $statement = $dbh->prepare("call add_userdata(:account, 500, 0, 0, 0)");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            
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

function getBalance($user){
    
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT balance FROM userdata where account = :account ");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    $row = $statement->fetch();
    
    return $row[0];
}

function updateBalance($user, $newBal){
    
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];
     
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT balance FROM userdata where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $oldBal = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("call add_transaction(:account, :oldBal, :newBal);");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":newBal", $newBal);
    $statement->execute();
    $row = $statement->fetch();

    $dbh = connectDB();
    $statement = $dbh->prepare("update userdata set balance = :balance where account = :account;");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":balance", $newBal);
    $statement->execute();
    $row = $statement->fetch();
    
    return $row[0];
}

?>
