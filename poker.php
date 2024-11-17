<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poker Game</title>
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
            max-width: 800px;
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
            background-color: #28a745;
        }

        .results {
            margin-top: 20px;
        }

        .cards {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .cards img {
            margin: 10px;
            width: 100px;
            height: 150px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
    <h1>Poker Game</h1>
    <div class="game-container">
        <h2>Player's Hand</h2>
        <div class="cards" id="player-cards"></div>

        <h2>House's Hand</h2>
        <div class="cards" id="house-cards"></div>

        <div class="results" id="result"></div>
        <div class="results" id="player-stats">Wins: 0 | Losses: 0</div>
        <div class="results" id="gbucks-stats">GBucks: 0 | Bet: 0</div>

        <div class="buttons">
            <div class="button-group">
            <button id="play-hand-btn" onclick="playHand()">Play Hand</button>
            <button onclick="foldHand()">Fold</button>
            <form method="post" action="poker.php"><button type="submit" id="play-again-btn" style="display: none;" value="playAgain" name="playAgain" onclick="playAgain()">Play Again</button></form>
            <form method="post" action="TSP.php"><button type="submit" onclick="goToMainPage()" value="backMain" name="backMain">Back to Main Page</button></form>
            <form method="post" action="betting.php"><button type="submit" onclick="goToBettingPage()" value="changeBet" name="changeBet">Go to Betting Page</button></form>
            </div><br><br><br>
        </div>
    </div>

    <script>
        let deck = [];
        let playerHand = [];
        let houseHand = [];
        let houseCardsRevealed = false;
        let wins = parseInt(localStorage.getItem('pokerWins') || '0');
        let losses = parseInt(localStorage.getItem('pokerLosses') || '0');
        let balance = parseFloat(localStorage.getItem('shareCurrency'), 10);
        let bet = parseInt(localStorage.getItem('shareBet'), 10);
        updateCurrencyDisplay();

        // Create deck
        function createDeck() {
            const suits = ['hearts', 'clubs', 'diamonds', 'spades'];
            const values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king', 'ace'];
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

        // Draw a card
        function drawCard() {
            return deck.pop();
        }

        // Deal hands to player and house
        function dealHands() {
            if (deck.length < 10) {
                document.getElementById('result').textContent = 'Deck is low! Please reset the game.';
                return;

            }

            playerHand = [];
            houseHand = [];
            houseCardsRevealed = false;

            for (let i = 0; i < 7; i++) {
                playerHand.push(drawCard());
                houseHand.push(drawCard());
            }

            updateHandsDisplay();
            document.getElementById('result').textContent = '';
            document.getElementById('play-again-btn').style.display = "none";
        }

        // Show the player and house hands
        function updateHandsDisplay() {
            const playerContainer = document.getElementById('player-cards');
            const houseContainer = document.getElementById('house-cards');

            // Sort hands by rank
            const valueMap = {
                '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, '10': 10,
                'jack': 11, 'queen': 12, 'king': 13, 'ace': 14
            };
            const sortHand = (hand) => hand.sort((a, b) => valueMap[b.value] - valueMap[a.value]);

            playerContainer.innerHTML = sortHand(playerHand).map(card =>
                `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}">`
            ).join('');

            if (houseCardsRevealed) {
                houseContainer.innerHTML = sortHand(houseHand).map(card =>
                    `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}">`
                ).join('');
            } else {
                houseContainer.innerHTML = houseHand.map(() =>
                    `<img src="Playing Cards/card_back.png" alt="Card Back">`
                ).join('');
            }
        }

        function playHand() {
            houseCardsRevealed = true;
            updateHandsDisplay();

            document.getElementById('play-hand-btn').style.display = "none";

            const playerEvaluation = evaluateHand(playerHand);
            const houseEvaluation = evaluateHand(houseHand);

            let resultText = `Player has ${playerEvaluation.handType}. House has ${houseEvaluation.handType}. `;

            if (playerEvaluation.handType === 'Royal Flush') {
                resultText += 'Congratulations! You hit a Royal Flush!';
            } else if (houseEvaluation.handType === 'Royal Flush') {
                resultText += 'Oh no! The House hit a Royal Flush!';
            } else if (playerEvaluation.rank > houseEvaluation.rank) {
                resultText += 'You won!';
                wins++;
                balance += bet;
                newBalance();
            } else if (playerEvaluation.rank < houseEvaluation.rank) {
                resultText += 'You lost!';
                losses++;
                balance -= bet;
                newBalance();
            } else {
                // Handle tie by comparing sorted values of both hands
                const playerHighCards = playerEvaluation.sortedValues;
                const houseHighCards = houseEvaluation.sortedValues;

                let tieResult = 'It\'s a tie!'; // Default to tie unless we find a winner

                for (let i = 0; i < Math.min(playerHighCards.length, houseHighCards.length); i++) {
                    if (playerHighCards[i] > houseHighCards[i]) {
                        tieResult = 'You won!';
                        break; // Player wins
                    } else if (playerHighCards[i] < houseHighCards[i]) {
                        tieResult = 'You lost!';
                        break; // House wins
                    }
                }
                resultText += tieResult; // Add the tie result to the main result text
            }

            document.getElementById('result').textContent = resultText;
            document.getElementById('play-again-btn').style.display = "inline-block";
            updateCurrencyDisplay();
            saveData();
        }

        function evaluateHand(hand) {
            const counts = {};
            const suits = {};
            const valueMap = {
                '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, '10': 10,
                'jack': 11, 'queen': 12, 'king': 13, 'ace': 14
            };
            const values = hand.map(card => valueMap[card.value]).sort((a, b) => a - b);

            // Count occurrences of each card value and suit
            hand.forEach(card => {
                counts[card.value] = (counts[card.value] || 0) + 1;
                suits[card.suit] = (suits[card.suit] || 0) + 1;
            });

            // Check for flush (five cards of the same suit)
            const flushSuit = Object.keys(suits).find(suit => suits[suit] >= 5);
            const isFlush = !!flushSuit;

            // Check for straight (five cards in ranking order)
            let isStraight = false;
            for (let i = 0; i <= values.length - 5; i++) {
                if (values[i] + 4 === values[i + 4] && new Set(values.slice(i, i + 5)).size === 5) {
                    isStraight = true;
                    break;
                }
            }

            // Determine hand rank based on the poker hand hierarchy
            const valuesCount = Object.values(counts);
            const maxCount = Math.max(...valuesCount);
            const pairs = valuesCount.filter(count => count === 2).length;

            // Check for Royal Flush
            if (isFlush && isStraight && values.includes(valueMap['10']) && values.includes(valueMap['jack']) && values.includes(valueMap['queen']) && values.includes(valueMap['king']) && values.includes(valueMap['ace'])) {
                return { rank: 9, handType: 'Royal Flush', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Straight Flush
            if (isFlush && isStraight) {
                return { rank: 8, handType: 'Straight Flush', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Four of a Kind
            if (maxCount === 4) {
                return { rank: 7, handType: 'Four of a Kind', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Full House
            if (maxCount === 3 && pairs === 1) {
                return { rank: 6, handType: 'Full House', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Flush
            if (isFlush) {
                return { rank: 5, handType: 'Flush', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Straight
            if (isStraight) {
                return { rank: 4, handType: 'Straight', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Three of a Kind
            if (maxCount === 3) {
                return { rank: 3, handType: 'Three of a Kind', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for Two Pair
            if (pairs === 2) {
                return { rank: 2, handType: 'Two Pair', sortedValues: values.sort((a, b) => b - a) };
            }

            // Check for One Pair
            if (pairs === 1) {
                return { rank: 1, handType: 'One Pair', sortedValues: values.sort((a, b) => b - a) };
            }

            // High Card
            return { rank: 0, handType: 'High Card', sortedValues: values.sort((a, b) => b - a) };
        }

        function foldHand() {
            alert("You folded the hand.");
            losses++;
            balance -= bet;
            newBalance();
            updateCurrencyDisplay();
            saveData();
            playAgain();
        }
        function playAgain() {
            createDeck();
            dealHands();
            document.getElementById('play-hand-btn').style.display = "inline-block";
        }

        function goToMainPage() {
            window.location.href = "TSP.php"; // replace with actual path
        }

        function goToBettingPage() {
            window.location.href = "betting.php"; // redirect to the betting page
        }

        function updateCurrencyDisplay() {
            document.getElementById('gbucks-stats').textContent = `GBucks: ${balance.toFixed(2)} | Bet: ${bet}`;
            document.getElementById('player-stats').textContent = `Wins: ${wins} | Losses: ${losses}`;
        }

        function saveData() {
            localStorage.setItem('gbucksBalance', balance.toFixed(2));
            localStorage.setItem('pokerWins', wins);
            localStorage.setItem('pokerLosses', losses);
            localStorage.setItem('currentBet', bet);
            localStorage.setItem('shareBet', bet);
            localStorage.setItem('shareCurrency', balance);
        }
    
        // save balance value in a cookie to update the database
        function newBalance(){
            document.cookie = "newBal="+ Math.round(balance)+"; SameSite=None; Secure";
        }

        createDeck();
        dealHands();
    </script>
</body>

</html>