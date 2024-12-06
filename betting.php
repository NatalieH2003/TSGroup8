<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Place your Bets</title>
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
        display: flex;
        flex-direction: column;
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
        margin-top: 40px;
      }
      .chips-container {
        display: inline-block;
        text-align: center;
        margin: 20px auto;
      }
      .chips {
        padding: 10px 20px;
        font-size: 20px;
        color: white;
        border: none;
        margin: 5px;
        cursor: pointer;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        text-align: center;
        line-height: 40px;
      }
      .chip1 {
        background-image: url("Chips/Chip1.png");
      }
      .chip5 {
        background-image: url("Chips/Chip5.png");
      }
      .chip25 {
        background-image: url("Chips/Chip25.png");
      }
      .chip50 {
        background-image: url("Chips/Chip50.png");
      }
      .chip100 {
        background-image: url("Chips/Chip100.png");
      }
      .chip500 {
        background-image: url("Chips/Chip500.png");
      }
      .chips:hover {
        background-color: #2f2929;
      }
      .chip1,
      .chip5,
      .chip25,
      .chip50,
      .chip100,
      .chip500 {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
      }
    </style>
  </head>
  <body>
    <?php
      session_start();
      require "db.php";
      if (isset($_SESSION["username"])) {
        if (isset($_POST["changeBet"])) {
          updateBalance($_SESSION["username"], $_COOKIE["newBal"]);
        }
      }
    ?>

    <h1>Place your Bets</h1>
    <div class="game-container">
      <div class="buttons">
        <div class="chips-container">
          <button class="chips chip1" onclick="updateBet(1)"></button>
          <button class="chips chip5" onclick="updateBet(5)"></button>
          <button class="chips chip25" onclick="updateBet(25)"></button>
          <button class="chips chip50" onclick="updateBet(50)"></button>
          <button class="chips chip100" onclick="updateBet(100)"></button>
          <button class="chips chip500" onclick="updateBet(500)"></button>
        </div>
        <button onclick="reset()">Reset</button>
        <div>
          <button onclick="startGame('blackjack')">Start Blackjack</button>
          <button onclick="startGame('slots')">Start Slots</button>
          <button onclick="startGame('poker')">Start Poker</button>
          <button onclick="startGame('roulette')">Start Roulette</button>
          <button onclick="startGame('euchre')">Start Euchre</button>
          <button onclick="startGame('texasHoldem')">Start Texas Hold'em</button>
          <button onclick="startGame('horseBetting')">Start HorseBetting</button>
        </div>
        <form method="post" action="TSP.php">
          <button type="submit" class="back-button" onclick="goToMainPage()" value="backMain" name="backMain">Back to Main Page</button>
        </form>
      </div>
      <div class="results" id="gBucksDisplay"></div>
    </div>

    <script>
      let balance = parseFloat(localStorage.getItem("shareCurrency"), 10);
      let bet = 0;
      document.getElementById(
        "gBucksDisplay"
      ).textContent = `GBucks: ${balance} | Bet: ${bet}`;

      function updateBet(chipValue) {
        if (bet + chipValue <= balance) {
          bet += chipValue;
        }
        document.getElementById(
          "gBucksDisplay"
        ).textContent = `GBucks: ${balance} | Bet: ${bet}`;
        saveData();
      }

      function reset() {
        bet = 0;
        document.getElementById(
          "gBucksDisplay"
        ).textContent = `GBucks: ${balance} | Bet: ${bet}`;
      }

      function saveData() {
        localStorage.setItem("shareBet", bet);
        localStorage.setItem("shareCurrency", balance);
      }

      function startGame(game) {
        saveData();
        if (game === "blackjack") {
          location.href = "blackjack.php";
        } else if (game === "slots") {
          location.href = "slots.html";
        } else if (game === "poker") {
          location.href = "poker.php";
        } else if (game === "roulette") {
          location.href = "roulette.html";
        } else if (game === "euchre") {
          location.href = "euchre.php";
        } else if (game === "texasHoldem") {
          location.href = "Texas Hold'em.php";
        } else if (game === "horseBetting") {
          location.href = "horseBetting.php";
        }
      }

      function goToMainPage() {
        location.href = "TSP.php";
      }
    </script>
  </body>
</html>
