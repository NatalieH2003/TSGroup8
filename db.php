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
    $statement = $dbh->prepare("SELECT balance FROM userdata where account = :account ");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    $row = $statement->fetch();
    $oldBal = $row[0];

    if($newBal != $oldBal){
    $dbh = connectDB();
    $statement = $dbh->prepare("call add_transaction(:account, :oldBal, :newBal);");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":oldBal", $oldBal);
    $statement->bindParam(":newBal", $newBal);
    $statement->execute();
    $row = $statement->fetch();

    $dbh = connectDB();
    $statement = $dbh->prepare("update userdata set balance = :balance where account = :account;");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":balance", $newBal);
    $statement->execute();
    $row = $statement->fetch();
    }
    
}

function getTasks($user){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];
    
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT id, task FROM tasks where account = :account and completed = false");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    
    return $statement->fetchAll();
    
}

function addTask($user, $taskDesc, $value){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT count(*) FROM tasks where account = :account and task = :taskDesc and completed = false");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskDesc", $taskDesc);
    $statement->execute();
    $row = $statement->fetch();

    if($row[0] != 1){
    $dbh = connectDB();
    $statement = $dbh->prepare("call add_task(:account, :taskDesc, :value);");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskDesc", $taskDesc);
    $statement->bindParam(":value", $value);
    $statement->execute();
    $row = $statement->fetch();
    }
}

function completeTask($user, $taskID){
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
    $balance = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT count(*) FROM tasks where account = :account and id = :taskID and completed = false");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskID", $taskID);
    $statement->execute();
    $row = $statement->fetch();

    if($row[0] == 1){
    $dbh = connectDB();
    $statement = $dbh->prepare("update tasks set completed = true where account = :account and id = :taskID;");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskID", $taskID);
    $statement->execute();
    $row = $statement->fetch();
    
    updateBalance($user, $balance+25);
    }
}

function removeTask($user, $taskID){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT count(*) FROM tasks where account = :account and id = :taskID and completed = false");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskID", $taskID);
    $statement->execute();
    $row = $statement->fetch();

    if($row[0] == 1){
    $dbh = connectDB();
    $statement = $dbh->prepare("delete from tasks where account = :account and id = :taskID;");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":taskID", $taskID);
    $statement->execute();
    $row = $statement->fetch();
    }
}

function buyItem($user, $group, $id){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];
    $cost = 0;
    
    switch((int)$group){
    case 0:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedSuburbs where account = :account and turtle = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 50;
            }
            
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedSuburbs set turtle = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedSuburbs where account = :account and cat = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 50;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedSuburbs set cat = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedSuburbs where account = :account and dog = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 50;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedSuburbs set dog = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedSuburbs where account = :account and hamster = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 50;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedSuburbs set hamster = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedSuburbs where account = :account and mouse = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 50;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedSuburbs set mouse = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    case 1:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedFarms where account = :account and cow = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 100;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedFarms set cow = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedFarms where account = :account and sheep = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 100;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedFarms set sheep = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedFarms where account = :account and rooster = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 100;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedFarms set rooster = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedFarms where account = :account and chick = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 100;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedFarms set chick = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedFarms where account = :account and pig = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 100;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedFarms set pig = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    case 2:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and chipmunk = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set chipmunk = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and rabbit = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set rabbit = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and bear = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set bear = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and wolf = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set wolf = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and fox = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set fox = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 5:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedForests where account = :account and deer = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 200;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedForests set deer = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    case 3:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOceans where account = :account and octopus = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 300;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOceans set octopus = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOceans where account = :account and fish = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 300;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOceans set fish = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOceans where account = :account and puffer = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 300;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOceans set puffer = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOceans where account = :account and dolphin = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 300;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOceans set dolphin = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOceans where account = :account and whale = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 300;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOceans set whale = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    case 4:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedDeserts where account = :account and camel = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 400;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedDeserts set camel = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedDeserts where account = :account and elephant = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 400;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedDeserts set elephant = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedDeserts where account = :account and lizard = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 400;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedDeserts set lizard = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedDeserts where account = :account and lion = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 400;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedDeserts set lion = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedDeserts where account = :account and rhino = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 400;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedDeserts set rhino = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    case 5:
        switch((int)$id){
        case 0:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOthers where account = :account and dodo = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 500;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOthers set dodo = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 1:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOthers where account = :account and trex = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 500;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOthers set trex = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 2:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOthers where account = :account and dragon = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 500;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOthers set dragon = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 3:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOthers where account = :account and dino = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 500;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOthers set dino = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        case 4:
            $dbh = connectDB();
            $statement = $dbh->prepare("SELECT count(*) FROM ownedOthers where account = :account and unicorn = true");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
            if($row[0] != 1){
                $cost = 500;
            }
            $dbh = connectDB();
            $statement = $dbh->prepare("update ownedOthers set unicorn = true where account = :account;");
            $statement->bindParam(":account", $userID);
            $statement->execute();
            $row = $statement->fetch();
        break;
        default:
        //for testing
        break;
        }
    break;
    default:
    //for testing
    break;
    }

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT balance FROM userdata where account = :account ");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    $row = $statement->fetch();
    $balance = $row[0];
    
    updateBalance($user, $balance-$cost);
}

function loadItems($user, $group){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];
    
    switch((int)$group){
    case 0:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedSuburbs where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    case 1:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedFarms where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    case 2:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedForests where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    case 3:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedOceans where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    case 4:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedDeserts where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    case 5:
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT * FROM ownedOthers where account = :account");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        
        return $statement->fetchAll();
    break;
    default:
    //for testing
    break;
    }

}

function createProfile($user){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];
    
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT count(*) FROM userProfile where account = :account");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    $row = $statement->fetch();
    
    if($row[0] != 1){
        $dbh = connectDB();
        $statement = $dbh->prepare("call create_user_profile(:account);");
        $statement->bindParam(":account", $userID);
        $statement->execute();
        $row = $statement->fetch();
    }
}

function setEquipped($user, $emoji){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("update userProfile set equipped = :emoji where account = :account;");
    $statement->bindParam(":account", $userID);
    $statement->bindParam(":emoji", $emoji);
    $statement->execute();
    $row = $statement->fetch();
}

function loadEquipped($user){
    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT account FROM users where name = :username ");
    $statement->bindParam(":username", $user);
    $statement->execute();
    $row = $statement->fetch();
    $userID = $row[0];

    $dbh = connectDB();
    $statement = $dbh->prepare("SELECT equipped FROM userProfile where account = :account ");
    $statement->bindParam(":account", $userID);
    $statement->execute();
    $row = $statement->fetch();
    
    return $row[0];
}

?>
