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
    }
    nav a {
      margin: 0 15px;
      text-decoration: none;
      color: #333;
    }
    section {
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .app-info {
      display: flex;
      justify-content: space-around;
    }
    .app-info div {
      width: 30%;
      padding: 10px;
    }
    footer {
      text-align: center;
      padding: 20px;
      background-color: #333;
      color: white;
    }
    .logout-btn {
      display: inline-block;
      padding: 5px 10px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .game-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin: 10px 5px;
    }
    .game-btn:hover, .logout-btn:hover {
      background-color: #218838;
    }
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
    }
    .row{
      display:flex;
      justify-content: center;
      flex-direction: row;
      text-align: center;
      align-items: center;
      
    }
    #displayEmoji{
      font-size: 40px;
    }
  </style>
</head>
<body>
  <header>
    <div class = "row">
    <div id="displayEmoji"></div>
    <h1>Gamble Smart</h1>
    </div>
    <p>Balance gambling with productivity and fun!</p>
    <?php
        session_start();
        require "db.php";
        if(isset($_SESSION["username"])){
            if(isset($_POST["backMain"])){
                updateBalance($_SESSION["username"], $_COOKIE["newBal"]);
            }
                      
        ?>
        <form method="post" action="login.php">
            <?php
            echo '<label> Welcome ' . $_SESSION["username"] . '!</label>';
            ?>
            <input type="submit" class="logout-btn" value="Logout" name="Logout">        
        </form>
        <?php
        }
        else{
        ?>
        <form method="post" action="login.php">
            <?php
            echo '<label> Welcome Guest!</label>';
            ?>
            <input type="submit" class="logout-btn" value="Login" name="toLogin">        
        </form>
        
        <?php
        }
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

  <section id="games">
    <h2>Play Games</h2>
    <p>Ready to test your luck? Choose a game below and enjoy responsibly.</p>
    <a href="todo.php" class="game-btn">To-Do List</a>
    <a href="shop.html" class="game-btn">Shop</a>
    <a href="inventory.html" class="game-btn">Inventory</a>
    <a href="rules.html" class="game-btn">Rules</a>
    <a href="betting.php" class="game-btn">Place a Bet</a>
    <a href="betting.php" class="game-btn">Play Blackjack</a>
    <a href="betting.php" class="game-btn">Play Slots</a>
    <a href="betting.php" class="game-btn">Play Euchre</a>
    <a href="betting.php" class="game-btn">Play Poker</a>
    <a href="betting.php" class="game-btn">Play Roulette</a>
  </section>

  <footer>
    <p>Gamble Smart - Gamble responsibly</p>
  </footer>

  <script>
    let balance = 500; // Default starting balance

    let emoji = localStorage.getItem("equippedEmoji");
    const display = document.getElementById("displayEmoji");
      if (emoji) {
        display.textContent = emoji; 
      } else {
        display.textContent = "\u{1F928}"; 
      }

    <?php
    if(isset($_SESSION["username"])){
    ?>
    
    balance = <?php echo json_encode(getbalance($_SESSION["username"])); ?>;
    localStorage.setItem("shareCurrency", balance);
    
    <?php
    }
    else{
    ?>

    // Check if 'shareCurrency' exists and is a valid number
    if (localStorage.getItem("shareCurrency") && !isNaN(localStorage.getItem("shareCurrency"))) {
      balance = parseFloat(localStorage.getItem("shareCurrency"));
    } else {
      localStorage.setItem("shareCurrency", balance);
    }
    <?php
    }
    ?>

    // Display the balance
    document.getElementById("gbucks-total").textContent = `GBucks: ${balance}`;
  </script>
</body>
</html>