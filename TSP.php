<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gamble Smart - Productivity and Games</title>
  <style>
   body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
}

header {
  background-color: #333;
  color: white;
  text-align: center;
  padding: 1rem;
  border: 1px solid black; /* Black border for consistency */
}

h1 {
  margin: 0;
}

h2 {
  font-size: 36px;
  margin: 10px;
}

nav {
  margin-top: 10px;
  text-align: center;
  border: 1px solid black; /* Black border for consistency */
  padding: 10px;
  border-radius: 8px;
  background-color: white;
}

nav a {
  margin: 0 15px;
  text-decoration: none;
  color: #333;
}

/* General section style with black border */
section {
  max-width: 1200px;
  margin: 20px auto;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border: 1px solid black; /* Black border for consistency */
}

/* Specific styling for app-info boxes */
.app-info {
  display: flex;
  justify-content: space-around;
}

.app-info div {
  width: 30%;
  padding: 10px;
  border: 1px solid black; /* Black border for consistency */
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

/* Rules and Games sections */
#rules-container, #games-container {
  border: 1px solid black; /* Black border for consistency */
  padding: 20px;
  margin: 20px auto;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* GBucks total display */
.gbucks-container {
  text-align: center;
  margin-top: 20px;
}

.gbucks-total {
  font-size: 48px;
  font-weight: bold;
  color: #28a745;
  background-color: #f0f0f0;
  padding: 20px;
  border-radius: 10px;
  display: inline-block;
  width: 100%;
  max-width: 300px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border: 1px solid black; /* Black border for consistency */
}

/* Buttons */
.logout-btn, .game-btn {
  display: inline-block;
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  margin: 10px 5px;
}

.logout-btn:hover, .game-btn:hover {
  background-color: #218838;
}

/* Row display for header elements */
.row {
  display: flex;
  justify-content: center;
  flex-direction: row;
  text-align: center;
  align-items: center;
}

/* Emoji display */
#displayEmoji {
  font-size: 40px;
}

footer {
  text-align: center;
  padding: 20px;
  background-color: #333;
  color: white;
  border: 1px solid black; /* Black border for consistency */
}
  </style>
</head>
<body>
  <header>
    <div class="row">
      <div id="displayEmoji"></div>
      <h1>Gamble Smart</h1>
    </div>
    <p>Balance gambling with productivity and fun!</p>
    <?php
      session_start();
      require "db.php";
      if (isset($_SESSION["username"])) {
          if (isset($_POST["backMain"])) {
              updateBalance($_SESSION["username"], $_COOKIE["newBal"]);
          }
    ?>
    <form method="post" action="login.php">
      <?php echo '<label> Welcome ' . $_SESSION["username"] . '!</label>'; ?>
      <input type="submit" class="logout-btn" value="Logout" name="Logout">
    </form>
    <?php
      } else {
    ?>
    <form method="post" action="login.php">
      <label> Welcome Guest!</label>
      <input type="submit" class="logout-btn" value="Login" name="toLogin">
    </form>
    <?php } 
    
    
    
    ?>
  </header>

  <nav>
    <a href="#about">About the App</a>
    <a href="#features">Features</a>
    <a href="#games">Play Games</a>
  </nav>

  <section id="about">
    <h2>About the App</h2>
    <p>
      Gamble Smart is a productivity-focused gambling app. Use your time
      wisely while enjoying a few games. Manage your gambling habits and take
      breaks to improve your focus.
    </p>
  </section>

  <section id="features">
    <h2>App Features</h2>
    <div class="app-info">
      <div>
        <h3>Productivity Tools</h3>
        <p>
          Track your gambling time and take scheduled breaks. Set limits and
          reminders to manage your gaming and stay productive.
        </p>
      </div>
      <div>
        <h3>Task Tracker</h3>
        <p>
          Use the built-in task tracking to figure out how many g-bucks you
          win.
        </p>
      </div>
      <div>
        <h3>Play Games</h3>
        <p>
          Try Blackjack or Slots when you’re ready to spend the coins you've
          won by being productive. Keep your skills sharp while keeping
          gambling fun and balanced.
        </p>
      </div>
    </div>
  </section>

  <section class="gbucks-container">
    <div id="gbucks-total" class="gbucks-total"></div>
  </section>

  <!-- Rules / Shop / To-Do / Inventory Container -->
  <section id="rules-container" style="border: 1px solid black; padding: 20px; margin: 20px auto; background: white; border-radius: 8px; max-width: 1200px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <h2>Rules / Shop / To-Do List / Inventory</h2>
    <a href="rules.html" class="game-btn">Rules</a>
    <a href="shop.php" class="game-btn">Shop</a>
    <a href="todo.php" class="game-btn">To-Do List</a>
    <a href="inventory.php" class="game-btn">Inventory</a>
  </section>

  <!-- Games Container -->
  <section id="games-container" style="border: 1px solid black; padding: 20px; margin: 20px auto; background: white; border-radius: 8px; max-width: 1200px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <h2>Play Games</h2>
    <p>Ready to test your luck? Choose a game below and enjoy responsibly.</p>
    <a href="betting.php" class="game-btn">Place a Bet</a>
    <a href="betting.php" class="game-btn">Play Blackjack</a>
    <a href="betting.php" class="game-btn">Play Slots</a>
    <a href="betting.php" class="game-btn">Play Euchre</a>
    <a href="betting.php" class="game-btn">Play Poker</a>
    <a href="betting.php" class="game-btn">Play Horse Betting</a>
    <a href="betting.php" class="game-btn">Play Texas Hold'Em</a>
    <a href="betting.php" class="game-btn">Play Roulette</a>
  </section>

  <footer>
    <p>Gamble Smart - Gamble responsibly</p>
  </footer>

  <script>
const emojis = ["\u{1F422}", "\u{1F431}", "\u{1F436}", "\u{1F439}", "\u{1F42D}", "\u{1F404}", "\u{1F411}", "\u{1F413}", "\u{1F425}", "\u{1F416}", 
"\u{1F43F}", "\u{1F430}", "\u{1F43B}", "\u{1F43A}", "\u{1F98A}", "\u{1F98C}", "\u{1F419}", "\u{1F420}", "\u{1F421}", "\u{1F42C}", "\u{1F433}", 
"\u{1F42B}", "\u{1F418}", "\u{1F98E}", "\u{1F981}", "\u{1F98F}", "\u{1F9A4}", "\u{1F996}", "\u{1F409}", "\u{1F995}", "\u{1F984}"];
let inventory = JSON.parse(localStorage.getItem("inventory")) || [];
let purchased = JSON.parse(localStorage.getItem("purchased")) || [];
    <?php
    if (isset($_SESSION["username"]) && ($_SESSION["loaded"] == 0)) {
        createProfile($_SESSION["username"]);
        for($i = 0; $i < 6; $i++){
            $num = 5;
            $index = 0;
            $emojiList = loadItems($_SESSION["username"], $i);
            foreach($emojiList as $emojiInfo){
                if($i == 2){
                $num = 6;
                }
                for($j = 0; $j < $num; $j++){
                    if($i > 2){
                    $index = (5*$i) + $j + 1;
                    }
                    else{
                    $index = (5*$i) + $j;
                    }
                    if($emojiInfo[$j+2] == 1){
                    ?>
                    inventory.push(emojis[<?php echo $index?>]);
                    purchased.push(emojis[<?php echo $index?>]);
                    localStorage.setItem("purchased", JSON.stringify(purchased));
                    localStorage.setItem("inventory",JSON.stringify(inventory));
                    
                    <?php
                    }
                }
            }
        }
        $_SESSION["loaded"]=1;
    }
    ?>
      
    let balance = 500; // Default starting balance

    <?php
    if (isset($_SESSION["username"])){
        $equipped = loadEquipped($_SESSION["username"]);
        ?>
        localStorage.setItem("equippedEmoji", <?php echo "\"$equipped\"" ?>);
        <?php
    }
    ?>
    let emoji = localStorage.getItem("equippedEmoji");
    const display = document.getElementById("displayEmoji");
    if (emoji) {
      display.textContent = emoji; 
    } else {
      display.textContent = "\u{1F928}"; 
    }

    <?php if (isset($_SESSION["username"])) { ?>
    balance = <?php echo json_encode(getbalance($_SESSION["username"])); ?>;
    localStorage.setItem("shareCurrency", balance);
    <?php } else { ?>
    if (localStorage.getItem("shareCurrency") && !isNaN(localStorage.getItem("shareCurrency"))) {
      balance = parseFloat(localStorage.getItem("shareCurrency"));
    } else {
      localStorage.setItem("shareCurrency", balance);
    }
    <?php } ?>

    document.getElementById("gbucks-total").textContent = `GBucks: ${balance}`;
  </script>
</body>
</html>