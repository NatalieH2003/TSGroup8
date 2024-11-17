<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blackjack Game</title>
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
      .buttons .back-button{
        margin-top: 40px;
      }
      .button-group {
        display: flex;
        flex-flow: row wrap;
          position: absolute;
          left: 50%;
          transform: translate(-50%, 0%);
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
      .cards {
        display: flex;
        justify-content: center;
        margin-top: 20px;
      }
      .cards div {
        margin: 10px;
        padding: 20px;
        background-color: #333;
        border-radius: 5px;
        width: 100px;
        text-align: center;
      }
      img {
        width: 100px;
        height: auto;
      }
    </style>
  </head>
  <body>
      
      <?php
      session_start();
      require "db.php";
      if(isset($_SESSION["username"])){
        if(isset($_POST["playAgain"])){
            updateBalance($_SESSION["username"], $_COOKIE["newBal"]);
        }
      }
      ?>
      
    <h1>Blackjack Game</h1>
    <div class="game-container">
      <h2>Your Cards</h2>
      <div class="cards" id="player-cards"></div>
      <h2>Dealer's Cards</h2>
      <div class="cards" id="dealer-cards"></div>

      <div class="results" id="result"></div>
      <div class="results" id="player-stats">
        Player Wins: 0 | Dealer Wins: 0
      </div>
      <div class="results" id="gBucks-stats">
        GBucks: 0 | Bet: 0
      </div>

      <div class="buttons">
        <div class="button-group">
          <button onclick="hit()">Hit</button>
          <button onclick="stand()">Stand</button>
          <form method="post" action="blackjack.php"><button type="submit" id="play-again-btn" style="display: none;" value="playAgain" name="playAgain">Play Again</button></form>
          <form method="post" action="betting.php"><button type="submit" id="reset-bet-btn" style="display: none;" onclick="resetBet()" value="changeBet" name="changeBet">Change Bet</button></form>
        </div>
        <br>
        <form method="post" action="TSP.php"><button type="submit" class="back-button" onclick="goToMainPage()" value="backMain" name="backMain">Back to Main Page</button></form>
      </div>
    </div>

    <script>
      let playerCards = [];
      let dealerCards = [];
      let deck = [];
      let gameOver = false;
      let playerWins = 0;
      let dealerWins = 0;
      
      let balance = parseFloat(localStorage.getItem('shareCurrency'), 10);
      let bet = parseInt(localStorage.getItem('shareBet'), 10);
      updateCurrency();
      

      // Create and shuffle deck
      function createDeck() {
        const suits = ["hearts", "clubs", "diamonds", "spades"];
        const values = [
          "2", "3", "4", "5", "6", "7", "8", "9", "10", "jack", "queen", "king", "ace"
        ];
        deck = [];

        for (let suit of suits) {
          for (let value of values) {
            deck.push({ suit: suit, value: value });
          }
        }

        // Shuffle the deck
        for (let i = deck.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [deck[i], deck[j]] = [deck[j], deck[i]];
        }
      }

      // Start game
      function startGame() {
        createDeck();
        playerCards = [drawCard(), drawCard()];
        dealerCards = [drawCard(), drawCard()];
        gameOver = false;
        document.getElementById("result").textContent = "";
        document.getElementById("play-again-btn").style.display = "none";
        document.getElementById("reset-bet-btn").style.display = "none";
        updateCards();
        pullBlackjack(); // Check for Blackjack immediately after dealing cards
      }

      // Draw card
      function drawCard() {
        return deck.pop();
      }

      // Calculate card value
      function calculateHand(cards) {
        let total = 0;
        let aceCount = 0;
        for (let card of cards) {
          if (["king", "queen", "jack"].includes(card.value)) {
            total += 10;
          } else if (card.value === "ace") {
            total += 11;
            aceCount++;
          } else {
            total += parseInt(card.value);
          }
        }
        while (total > 21 && aceCount > 0) {
          total -= 10;
          aceCount--;
        }
        return total;
      }

      // Hit action
      function hit() {
        if (!gameOver) {
          playerCards.push(drawCard());
          updateCards();
          if (calculateHand(playerCards) > 21) {
            document.getElementById("result").textContent = "You Bust! Dealer Wins!";
            dealerWins++;
            updateStats();
            if (balance > 0) {
              balance -= bet;
              if (balance <= 0) balance = 0;
              if (balance === 0) bet = 0;
              saveData();
              newBalance();
              updateCurrency();
            }
            gameOver = true;
            showPlayAgainButton();
          }
        }
      }

      // Stand action
      function stand() {
        if (!gameOver) {
          while (calculateHand(dealerCards) < 17) {
            dealerCards.push(drawCard());
          }
          updateCards(true);

          let playerScore = calculateHand(playerCards);
          let dealerScore = calculateHand(dealerCards);

          if (dealerScore > 21) {
            document.getElementById("result").textContent = "Dealer Busts! You Win!";
            playerWins++;
            balance += bet;
            newBalance();
          } else if (playerScore > dealerScore) {
            document.getElementById("result").textContent = "You Win!";
            playerWins++;
            balance += bet;
            newBalance();
          } else if (playerScore === dealerScore) {
            document.getElementById("result").textContent = "It's a Tie!";
          } else {
            document.getElementById("result").textContent = "Dealer Wins!";
            dealerWins++;
            balance -= bet;
            newBalance();
          }
          updateStats();
          updateCurrency();
          gameOver = true;
          showPlayAgainButton();
        }
      }

      // Update cards display
      function updateCards(showDealerSecondCard = false) {
        document.getElementById("player-cards").innerHTML = playerCards
          .map(
            (card) =>
              `<div><img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}" style="width:100px;"></div>`
          )
          .join("");

        document.getElementById("dealer-cards").innerHTML = dealerCards
          .map((card, index) => {
            if (index === 1 && !showDealerSecondCard) {
              return `<div><img src="Playing Cards/card_back.png" alt="Hidden Card" style="width:100px;"></div>`;
            } else {
              return `<div><img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}" style="width:100px;"></div>`;
            }
          })
          .join("");
      }

      // Handle Blackjack
      function pullBlackjack() {
        const player = calculateHand(playerCards);
        const dealer = calculateHand(dealerCards);

        // Player gets priority for blackjack win
        if (player === 21) {
          document.getElementById("result").textContent = "BlackJack! You Win!";
          playerWins++;
          balance += (bet * 1.5);
          saveData();
          newBalance();
          updateCurrency();
          gameOver = true;
          showPlayAgainButton(); // End game after player Blackjack win
          return;
        }

        // If dealer has blackjack and player does not, dealer wins
        if (dealer === 21) {
          document.getElementById("result").textContent = "BlackJack! Dealer Wins!";
          dealerWins++;
          balance -= (bet * 1.5);
          saveData();
          newBalance();
          updateCurrency();
          gameOver = true;
          showPlayAgainButton();
          return;
        }

        // Tie if both have blackjack (added here for completeness)
        if (player === 21 && dealer === 21) {
          document.getElementById("result").textContent = "BlackJack! It's a Tie!";
          gameOver = true;
          showPlayAgainButton();
          return;
        }
      }

      // Show Play Again button
      function showPlayAgainButton() {
        document.getElementById("play-again-btn").style.display = "inline-block";
        document.getElementById("reset-bet-btn").style.display = "inline-block";
      }

      // Update stats
      function updateStats() {
        document.getElementById("player-stats").textContent = `Player Wins: ${playerWins} | Dealer Wins: ${dealerWins}`;
      }

      // Update Currency
      function updateCurrency() {
        document.getElementById("gBucks-stats").textContent = `GBucks: ${balance} | Bet: ${bet}`;
      }

      // Save Data
      function saveData() {
        localStorage.setItem('shareBet', bet);
        localStorage.setItem('shareCurrency', balance);
      }

      // Reset game
      function resetGame() {
        startGame();
      }

      // Reset bet
      function resetBet() {
        saveData();
        location.href="betting.php";
      }

      // Go to main page
      function goToMainPage() {
        location.href = "TSP.php";
      }
  
      // save balance value in a cookie to update the database
      function newBalance(){
        document.cookie = "newBal="+ Math.round(balance)+"; SameSite=None; Secure";
      }

      window.onload = function() {
        startGame();
        updateCards();
      };
    </script>
  </body>
</html>
