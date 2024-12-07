<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Horse Betting Game</title>
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
    .bar-container {
      margin-top: 30px; 
    }
    .bar {
      width: 90%;
      height: 40px; 
      background-color: lightgray;
      border-radius: 5px;
      margin: 20px auto; 
      position: relative;
    }
    .bar-inner {
      height: 100%;
      position: relative;
      border-radius: 5px;
      transition: width 0.5s ease-in-out;
    }
    .horse-1 .bar-inner { background-color: red; }
    .horse-2 .bar-inner { background-color: blue; }
    .horse-3 .bar-inner { background-color: green; }
    .horse-4 .bar-inner { background-color: yellow; }
    .horse-image {
      position: absolute;
      top: 50%;
      right: -30px; 
      transform: translateY(-50%);
      width: 100px;
      height: auto;
    }
    .buttons .back-button {
      margin-top: 20px;
    }
    .button-group {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
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
    .status {
      margin-top: 20px;
      font-size: 18px;
    }
  </style>
</head>
<?php
      session_start();
      require "db.php";
      if(isset($_SESSION["username"])){
        if(isset($_POST["playAgain"])){
            updateBalance($_SESSION["username"], $_COOKIE["newBal"]);
        }
      }
      ?>
<body>
  <h1>Horse Betting Game</h1>
  <div class="game-container">
    <div class="status" id="status">Select a horse to bet on.</div>
        <div class="results" id="gbucks-stats">GBucks: 0 | Bet: 0</div>
    <div class="buttons">
      <div class="button-group">
        <button onclick="selectHorse(1)">Horse 1</button>
        <button onclick="selectHorse(2)">Horse 2</button>
        <button onclick="selectHorse(3)">Horse 3</button>
        <button onclick="selectHorse(4)">Horse 4</button>
      </div>
      <button id="start-race" onclick="startRace()" disabled>Start Race</button>
    </div>
    <div class="bar-container">
      <div class="bar horse-1">
        <div class="bar-inner">
          <img src="Horses/horse1.png" alt="Horse 1" class="horse-image">
        </div>
      </div>
      <div class="bar horse-2">
        <div class="bar-inner">
          <img src="Horses/horse2.png" alt="Horse 2" class="horse-image">
        </div>
      </div>
      <div class="bar horse-3">
        <div class="bar-inner">
          <img src="Horses/horse3.png" alt="Horse 3" class="horse-image">
        </div>
      </div>
      <div class="bar horse-4">
        <div class="bar-inner">
          <img src="Horses/horse4.png" alt="Horse 4" class="horse-image">
        </div>
      </div>
    </div>
    <div class="buttons">
      <form method="post" action="betting.php">
        <button type="submit" class="back-button" value="changeBet" name="changeBet">Change Bet</button>
      </form>
      <form method="post" action="TSP.php">
        <button type="submit" class="back-button" value="backMain" name="backMain">Back to Main Page</button>
      </form>
    </div>
  </div>

  <script>
    let selectedHorse = null;
    let raceInterval;
    const bars = document.querySelectorAll('.bar-inner');
    const horseButtons = document.querySelectorAll('.button-group button');
    const startRaceButton = document.getElementById('start-race');
    const statusDiv = document.getElementById('status');
        let balance = parseFloat(localStorage.getItem('shareCurrency'), 10);
        let bet = parseInt(localStorage.getItem('shareBet'), 10);
        updateCurrencyDisplay();

    const raceAudio = new Audio('Horses/horse sounds.mp3');
  
    // Start bars at empty
    function loadBars() {
      bars.forEach(bar => {
        bar.style.width = '0%'; // Reset all bars to 0% width
      });
    }
  
    function selectHorse(horseNumber) {
      selectedHorse = horseNumber;
      statusDiv.textContent = `You selected Horse ${horseNumber}. Press Start to begin the race.`;
      startRaceButton.disabled = false;
    }
  
    function startRace() {
      if (!selectedHorse) return;

      // Reset bars to empty at the start of the race
      loadBars();
  
      // Disable buttons during the race
      disableButtons(true);
  
      statusDiv.textContent = 'Race is on!';
      startRaceButton.disabled = true;

      // Play race audio
      raceAudio.play();
  
      // Animate bars randomly with large jumps
      raceInterval = setInterval(() => {
        bars.forEach(bar => {
          let newWidth = Math.max(0, Math.min(100, parseFloat(bar.style.width || 0) + (Math.random() * 60 - 30))); // Larger jumps
          bar.style.width = `${newWidth}%`;
        });
      }, 500);
  
      // Determine winner after 5 seconds
      setTimeout(() => {
        clearInterval(raceInterval);
        declareWinner();
        // Stop race audio after the race
        raceAudio.pause();
        raceAudio.currentTime = 0;  // Reset audio to the beginning
        // Re-enable buttons after the race
        disableButtons(false);
      }, 5000);
    }
  
    function declareWinner() {
      const winner = Math.floor(Math.random() * 4) + 1;
      bars.forEach((bar, index) => {
        bar.style.width = (index + 1 === winner) ? '100%' : `${Math.random() * 90}%`;
      });
  
      const result = selectedHorse === winner ? 'You win!' : 'You lose!';
      statusDiv.textContent = `Horse ${winner} wins! ${result}`;

      // Update balance based on bet
      if (result === 'You win!') {
        balance += bet;
        newBalance();
      } else {
        balance -= bet;
        newBalance();
      }
      updateCurrencyDisplay();
      saveData();
    }

      function updateCurrencyDisplay() {
        document.getElementById('gbucks-stats').textContent = `GBucks: ${balance.toFixed(0)} | Bet: ${bet}`;
        }

      function saveData() {
            localStorage.setItem('gbucksBalance', balance.toFixed(2));
            localStorage.setItem('currentBet', bet);
            localStorage.setItem('shareBet', bet);
            localStorage.setItem('shareCurrency', balance);
        }
  
    function disableButtons(disabled) {
      horseButtons.forEach(button => button.disabled = disabled);
      startRaceButton.disabled = disabled;
    }

    // save balance value in a cookie to update the database
    function newBalance(){
      document.cookie = "newBal="+ Math.round(balance)+"; SameSite=None; Secure";
    }

    // Load bars when the page loads
    window.onload = loadBars;
  </script>
</body>
</html>