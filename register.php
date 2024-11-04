<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register for Gamble Smart</title>
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
      .error-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #444;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 20px;
        padding-right: 20px;
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
if(isset($_POST["register"])){
	if(register($_POST["username"], $_POST["password"]) == 1){
		/*if(strcmp($_SESSION["sesh_type"],"student") == 0){
			header("LOCATION:stu_main.php");
		}
		else{
			header("LOCATION:inst_main.php");
		}*/
		header("LOCATION:login.php");

		return;
	}
	else{
	    echo '<div class="error-container">';
		echo '<p style="color:red">Invalid username or password</p>';
		echo '</div>';
	}
}
else if(isset($_POST["login"])){
    header("LOCATION:login.php");
    return;
}


if(isset($_POST["logout"])){
	session_destroy();
}

?>
</body>
</html>
    <h1>Register for Gamble Smart</h1>
    <div class="game-container">
    <form method="post" action"register.php">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <button class="buttons" type="submit" value="Register" name="register">Register</button>
    </form>
    </div>
    <div class="game-container">
            <h2>Already have an account? Login now!</h2>
            <form method="post" action"login.php">
            <button class="buttons" type="submit" value="Login" name="login">Login</button>
            </form>
        </div>
  </body>
</html>