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
if(isset($_POST["login"])){
	if(authenticate($_POST["username"], $_POST["password"]) == 1){
		$_SESSION["username"]=$_POST["username"];
		createProfile($_SESSION["username"]);
		header("LOCATION:TSP.php");
		return;
	}
	else{
	    echo '<div class="error-container">';
		echo '<p style="color:red">Incorrect username or password</p>';
		echo '</div>';
	}
}
else if(isset($_POST["register"])){
     header("LOCATION:register.php");
     return;
}
else if(isset($_POST["continue"])){
     header("LOCATION:TSP.php");
     return;
}


if(isset($_POST["Logout"])){
	session_destroy();
	?>
	<script>
	localStorage.setItem("shareCurrency", 500);
	</script>
	<?php
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
    <div class="game-container">
        <h2>Don't have an account? Register now!</h2>
        <form method="post" action"register.php">
        <button class="buttons" type="submit" value="Register" name="register">Register</button>
        </form>
    </div>
    <div class="game-container">
        <h2>Continue as a Guest</h2>
        <form method="post" action"register.php">
        <button class="buttons" type="submit" value="Continue" name="continue">Continue</button>
        </form>
    </div>
  </body>
</html>
