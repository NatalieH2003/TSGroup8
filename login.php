<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login to Gamble Smart</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #2e8b57;
        color: white;
        text-align: center;
        margin: 0;
        padding: 0;
      }
      h1 {
        margin-top: 20px;
      }
      .game-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #444;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      }
      .buttons {
        margin-top: 20px;
      }
      button {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #28a745;
        color: white;
        border: none;
        margin: 5px;
        cursor: pointer;
        border-radius: 5px;
      }
      button:hover {
        background-color: #218838;
      }
      .results {
        margin-top: 20px;
      }
      input[type=text], input[type=password] {
        width: 75%;
        padding: 1em;
        margin: 15px;
        box-sizing: border-box;
        border: none;
      }
      img {
        width: 100px;
        height: auto;
      }
    </style>
  </head>
  <body>
<?php
require "db.php";
session_start();
if(isset($_POST["login"])){
	if(authenticate($_POST["username"], $_POST["password"]) == 1){
		/*if(strcmp($_SESSION["sesh_type"],"student") == 0){
			header("LOCATION:stu_main.php");
		}
		else{
			header("LOCATION:inst_main.php");
		}*/
		header("LOCATION:TSP.html");

		$_SESSION["username"]=$_POST["username"];
		return;
	}
	else{
		echo '<p style="color:red">Incorrect username and password</p>';
	}
}


if(isset($_POST["logout"])){
	session_destroy();
}

?>
</body>
</html>


    <h1>Login to Gamble Smart</h1>
    <div class="game-container">
    <form method="post" action"login.php">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <button class="buttons" type="submit" value="Login" name="login">Login</button>
        </form>
      </div>
    </div>
  </body>
</html>
