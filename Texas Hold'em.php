<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Texas Hold'em Poker Game</title>
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
    
        .button-group {
            display: flex;
            flex-flow: row wrap;
            position: absolute;
            left: 50%;
            transform: translate(-50%, 0%);
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
    <h1>Texas Hold'em Poker Game</h1>
    <div class="game-container">
        <h2>Player's Hole Cards</h2>
        <div class="cards" id="player-cards"></div>

        <h2>House's Hole Cards</h2>
        <div class="cards" id="house-cards"></div>

        <h2>Community Cards</h2>
        <div class="cards" id="community-cards"></div>

        <div class="results" id="result"></div>
        <div class="results" id="player-stats">Wins: 0 | Losses: 0</div>
        <div class="results" id="gbucks-stats">GBucks: 0 | Bet: 0</div>

        <div class="buttons">
            <div class="button-group">
            <button id="next-phase-btn" onclick="nextPhase()">Next Phase</button>
            <button id="increase-bet-btn" onclick="increaseBet()" style="display: none;">Increase Bet (10 GBucks)</button>
            <button id="fold-btn" onclick="fold()" style="display: none;">Fold</button>
            <form method="post" action="Texas Hold'em.php"><button type="submit" id="play-again-btn" style="display: none;" value="playAgain" name="playAgain" onclick="playAgain()">Play Again</button></form>
            <form method="post" action="TSP.php"><button type="submit" onclick="goToMainPage()" value="backMain" name="backMain">Back to Main Page</button></form>
            <form method="post" action="rules.html"><button type="submit" onclick="goToRulesPage()" value="backMain" name="backMain">Rules</button></form>
            <form method="post" action="betting.php"><button type="submit" onclick="goToBettingPage()" value="changeBet" name="changeBet">Go to Betting Page</button></form>
            </div><br><br>
        </div>
    </div>

    <script>
        let deck = [];
        let playerHand = [];
        let houseHand = [];
        let communityCards = [];
        let wins = parseInt(localStorage.getItem('pokerWins') || '0');
        let losses = parseInt(localStorage.getItem('pokerLosses') || '0');
        let balance = parseFloat(localStorage.getItem('shareCurrency'), 10);
        let bet = parseInt(localStorage.getItem('shareBet'), 10);
        let currentPhase = 0;
        updateCurrencyDisplay();

        function createDeck() {
            const suits = ['hearts', 'clubs', 'diamonds', 'spades'];
            const values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king', 'ace'];
            deck = [];

            for (let suit of suits) {
                for (let value of values) {
                    deck.push({ suit: suit, value: value });
                }
            }

            for (let i = deck.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [deck[i], deck[j]] = [deck[j], deck[i]];
            }
        }

        function drawCard() {
            return deck.pop();
        }

        function dealHands() {
            playerHand = [drawCard(), drawCard()];
            houseHand = [drawCard(), drawCard()];
            communityCards = [];
            updateHandsDisplay();
        }

        function updateHandsDisplay() {
            const playerContainer = document.getElementById('player-cards');
            const houseContainer = document.getElementById('house-cards');
            const communityContainer = document.getElementById('community-cards');

            playerContainer.innerHTML = playerHand.map(card =>
                `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}">`
            ).join('');

            houseContainer.innerHTML = houseHand.map(() =>
                `<img src="Playing Cards/card_back.png" alt="Card Back">`
            ).join('');

            communityContainer.innerHTML = communityCards.map(card =>
                `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}">`
            ).join('');
        }

        function nextPhase() {
            switch (currentPhase) {
                case 0: // Pre-flop
                    communityCards = [];
                    updateHandsDisplay();
                    break;
                case 1: // Flop
                    communityCards.push(drawCard(), drawCard(), drawCard());
                    updateHandsDisplay();
                    break;
                case 2: // Turn
                    communityCards.push(drawCard());
                    updateHandsDisplay();
                    break;
                case 3: // River
                    communityCards.push(drawCard());
                    updateHandsDisplay();
                    evaluateRound();
                    break;
            }

            currentPhase++;
            if (currentPhase > 3) {
                document.getElementById('next-phase-btn').style.display = "none";
                document.getElementById('play-again-btn').style.display = "inline-block";
                document.getElementById('increase-bet-btn').style.display = "none";
            } else {
                document.getElementById('increase-bet-btn').style.display = "inline-block";
                document.getElementById('fold-btn').style.display = "inline-block";
            }
        }

        function increaseBet() {
            const increaseAmount = 10; // Amount to increase the bet by
            if (balance >= increaseAmount) {
                bet += increaseAmount;
                balance -= increaseAmount;
                newBalance();
                updateCurrencyDisplay();
            } else {
                alert('Not enough GBucks to increase the bet!');
            }
        }

        function fold() {
            function fold() {
    losses++;
    balance -= bet;  // Deduct bet when player folds
    newBalance();
    updateCurrencyDisplay();
    saveData();
    
    document.getElementById('next-phase-btn').style.display = "none";
    document.getElementById('play-again-btn').style.display = "inline-block";
    document.getElementById('increase-bet-btn').style.display = "none";
    document.getElementById('fold-btn').style.display = "none";
    
    document.getElementById('result').textContent = 'You folded. You lost your bet.';
}
        }

        function evaluateRound() {
            const playerEvaluation = evaluateHand(playerHand.concat(communityCards));
        const houseEvaluation = evaluateHand(houseHand.concat(communityCards));

        let resultText = `Player has ${playerEvaluation.handType}. House has ${houseEvaluation.handType}. `;

        if (playerEvaluation.rank > houseEvaluation.rank) {
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
            resultText += 'It\'s a tie!';
        }

        // Show the house's cards
        const houseContainer = document.getElementById('house-cards');
        houseContainer.innerHTML = houseHand.map(card =>
            `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}">`
        ).join('');

        document.getElementById('result').textContent = resultText;
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

        function playAgain() {
            createDeck();
    dealHands();
    currentPhase = 0;
    document.getElementById('next-phase-btn').style.display = "inline-block";
    document.getElementById('play-again-btn').style.display = "none";
    document.getElementById('increase-bet-btn').style.display = "none";
    
    // Hide the house's cards until the end of the game
    const houseContainer = document.getElementById('house-cards');
    houseContainer.innerHTML = houseHand.map(() =>
        `<img src="Playing Cards/card_back.png" alt="Card Back">`
    ).join('');

    // Clear the end game result message
    document.getElementById('result').textContent = '';

    updateCurrencyDisplay();
        }

        function updateCurrencyDisplay() {
            document.getElementById("player-stats").textContent = `Wins: ${wins} | Losses: ${losses}`;
            document.getElementById("gbucks-stats").textContent = `GBucks: ${balance} | Bet: ${bet}`;
        }

        function saveData() {
            localStorage.setItem('pokerWins', wins);
            localStorage.setItem('pokerLosses', losses);
            localStorage.setItem('shareCurrency', balance);
            localStorage.setItem('shareBet', bet);
        }

        function goToMainPage() {
            // Redirect to the main page
            window.location.href = "TSP.php";
        }

        function goToBettingPage() {
            // Redirect to the betting page
            window.location.href = "betting.php";
        }
    	function goToRulesPage() {
            // Redirect to the rules page
            window.location.href = "rules.html";
        }
        // save balance value in a cookie to update the database
        function newBalance(){
            document.cookie = "newBal="+ Math.round(balance)+"; SameSite=None; Secure";
        }

        // Initialize the game
        createDeck();
        dealHands();
    </script>
</body>
</html>