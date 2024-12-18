<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euchre Game</title>
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
        .player-area, .buttons, .results {
            margin-top: 20px;
        }
        .player-hand {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        img {
            width: 80px;
            height: auto;
            cursor: pointer;
        }
        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .diamond-layout {
            position: relative;
            width: 400px;
            height: 400px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .diamond-layout div {
            position: absolute;
        }
        #player-played { bottom: 0; left: 50%; transform: translateX(-50%); }
        #computer1-played { left: 0; top: 50%; transform: translateY(-50%); }
        #computer2-played { top: 0; left: 50%; transform: translateX(-50%); }
        #computer3-played { right: 0; top: 50%; transform: translateY(-50%); }
        #trump-display {
            margin-top: 20px;
            font-size: 1.2em;
            color: #ffcc00;
        }
        .highlight {
            border: 2px solid yellow;
        }
        .disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <h1>Euchre Game</h1>
    <div class="game-container">
        <div id="bet-info"></div>
        <h2>Your Hand</h2>
        <div class="player-hand" id="player-hand"></div>

        <h3>Played Cards</h3>
        <div class="diamond-layout" id="played-cards">
            <div class="played-card" id="player-played">You</div>
            <div class="played-card" id="computer1-played">Computer 1</div>
            <div class="played-card" id="computer2-played">Computer 2</div>
            <div class="played-card" id="computer3-played">Computer 3</div>
        </div>

        <div class="results" id="result"></div>
        <div id="trump-display"></div>
        <div id="team-scores">
            <p>Player's Team Points: <span id="player-team-points">0</span></p>
            <p>Opposing Team Points: <span id="opposing-team-points">0</span></p>
        </div>
        <div class="buttons">
            <button onclick="goToHome()">Home</button>
	    <button onclick="goToRules()">Rules</button>
            <button onclick="goToBetting()">Back to Betting</button>
            <button id="reset-button" onclick="resetGame()" disabled>Reset Game</button>
        </div>
    </div>

    <script>
        let bet = parseFloat(localStorage.getItem('shareBet')) || 0;
        let balance = parseFloat(localStorage.getItem('shareCurrency')) || 0;

        document.getElementById("bet-info").textContent = `Current Bet: ${bet} | Balance: ${balance}`;

        function goToHome() {
            location.href = "TSP.php";
        }
	function goToRules() {
            location.href = "rules.html";
        }

        function goToBetting() {
            bet = 0;
            localStorage.setItem('shareBet', bet);
            location.href = "betting.php";
        }

        let deck = [];
        let playerHand = [];
        let computerHands = [[], [], []];
        let trumpSuit;
        let currentTrick = [];
        let leadingSuit = null;
        let currentPlayer = 0;
        let trickWinner = 0;
        let playerTeamPoints = 0;
        let opposingTeamPoints = 0;
        const totalTricks = 5;

        function createDeck() {
            const suits = ["hearts", "clubs", "diamonds", "spades"];
            const values = ["9", "10", "jack", "queen", "king", "ace"];
            deck = [];

            for (let suit of suits) {
                for (let value of values) {
                    deck.push({ suit, value });
                }
            }

            for (let i = deck.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [deck[i], deck[j]] = [deck[j], deck[i]];
            }
        }

        function dealHands() {
            playerHand = deck.splice(0, 5);
            for (let i = 0; i < 3; i++) {
                computerHands[i] = deck.splice(0, 5);
            }
        }

        function displayPlayerHand() {
            const handDiv = document.getElementById("player-hand");
            const canFollowSuit = playerHand.some(card => card.suit === leadingSuit);

            handDiv.innerHTML = playerHand.map((card, index) => {
                const followsSuit = card.suit === leadingSuit || !canFollowSuit;
                const highlightClass = followsSuit ? "highlight" : "disabled";

                return `<img src="Playing Cards/${card.value}_of_${card.suit}.png" alt="${card.value} of ${card.suit}" onclick="playCard(${index})" class="${highlightClass}" style="${followsSuit ? '' : 'pointer-events: none; opacity: 0.5;'}">`;
            }).join("");
        }

        function setTrumpSuit() {
            trumpSuit = deck[Math.floor(Math.random() * deck.length)].suit;
            document.getElementById("trump-display").textContent = `Trump Suit: ${trumpSuit}`;
        }

        function resetPlayedCards() {
            document.getElementById("player-played").innerHTML = "You";
            document.getElementById("computer1-played").innerHTML = "Computer 1";
            document.getElementById("computer2-played").innerHTML = "Computer 2";
            document.getElementById("computer3-played").innerHTML = "Computer 3";
            currentTrick = [];
            leadingSuit = null;
        }

        function startGame() {
            createDeck();
            dealHands();
            setTrumpSuit();
            displayPlayerHand();
            resetPlayedCards();
            currentPlayer = (trickWinner + 1) % 4;
            document.getElementById("reset-button").disabled = true;
            playTurn();
        }

        function playTurn() {
            if (currentTrick.length >= 4) {
                checkTrickWinner();
            } else if (currentPlayer === 0) {
                document.getElementById("result").textContent = "Your turn! Choose a card to play.";
                displayPlayerHand();
            } else {
                playComputerCard(currentPlayer - 1);
            }
        }

        function playCard(index) {
            if (currentPlayer !== 0) return;

            const selectedCard = playerHand[index];

            if (currentTrick.length === 0) {
                leadingSuit = selectedCard.suit;
            } else if (leadingSuit && playerHand.some(card => card.suit === leadingSuit) && selectedCard.suit !== leadingSuit) {
                document.getElementById("result").textContent = "You must follow suit!";
                return;
            }

            const playedCard = playerHand.splice(index, 1)[0];
            currentTrick.push({ player: "You", card: playedCard });
            document.getElementById("player-played").innerHTML = `<img src="Playing Cards/${playedCard.value}_of_${playedCard.suit}.png" alt="${playedCard.value} of ${playedCard.suit}">`;

            currentPlayer = (currentPlayer + 1) % 4;
            displayPlayerHand();
            playTurn();
        }

        function playComputerCard(computerIndex) {
            const computerHand = computerHands[computerIndex];
            let cardToPlay = computerHand.find(card => card.suit === leadingSuit);

            if (!cardToPlay) {
                cardToPlay = computerHand[0];
            }

            if (currentTrick.length === 0) {
                leadingSuit = cardToPlay.suit;
            }

            currentTrick.push({ player: `Computer ${computerIndex + 1}`, card: cardToPlay });
            computerHand.splice(computerHand.indexOf(cardToPlay), 1);
            document.getElementById(`computer${computerIndex + 1}-played`).innerHTML = `<img src="Playing Cards/${cardToPlay.value}_of_${cardToPlay.suit}.png" alt="${cardToPlay.value} of ${cardToPlay.suit}">`;

            currentPlayer = (currentPlayer + 1) % 4;
            setTimeout(playTurn, 1000);
        }

        function checkTrickWinner() {
            const trumpCards = currentTrick.filter(entry => entry.card.suit === trumpSuit);
            let winningEntry;

            if (trumpCards.length > 0) {
                winningEntry = trumpCards.reduce((max, entry) => cardValue(entry.card) > cardValue(max.card) ? entry : max);
            } else {
                winningEntry = currentTrick.filter(entry => entry.card.suit === leadingSuit)
                                           .reduce((max, entry) => cardValue(entry.card) > cardValue(max.card) ? entry : max);
            }

            document.getElementById("result").textContent = `Winner of the trick: ${winningEntry.player} with ${winningEntry.card.value} of ${winningEntry.card.suit}`;

            if (winningEntry.player === "You" || winningEntry.player === "Computer 2") {
                playerTeamPoints++;
                document.getElementById("player-team-points").textContent = playerTeamPoints;
            } else {
                opposingTeamPoints++;
                document.getElementById("opposing-team-points").textContent = opposingTeamPoints;
            }

            trickWinner = currentTrick.indexOf(winningEntry);
            resetPlayedCards();
            currentPlayer = trickWinner;

            if (playerHand.length === 0) {
                endGame();
            } else {
                playTurn();
            }
        }

        function cardValue(card) {
            const values = ["9", "10", "jack", "queen", "king", "ace"];
            return values.indexOf(card.value);
        }

        function endGame() {
            if (playerTeamPoints > opposingTeamPoints) {
                document.getElementById("result").textContent = "Player's team wins the game!";
                balance += bet;
            } else if (opposingTeamPoints > playerTeamPoints) {
                document.getElementById("result").textContent = "Opposing team wins the game!";
                balance -= bet;
            } else {
                document.getElementById("result").textContent = "It's a tie!";
            }

            localStorage.setItem('shareCurrency', balance);
            document.getElementById("bet-info").textContent = `Current Bet: ${bet} | Balance: ${balance}`;

            document.getElementById("reset-button").disabled = false;
        }

        function resetGame() {
            playerTeamPoints = 0;
            opposingTeamPoints = 0;
            document.getElementById("player-team-points").textContent = playerTeamPoints;
            document.getElementById("opposing-team-points").textContent = opposingTeamPoints;
            startGame();
        }

        window.onload = startGame;
    </script>
</body>
</html>
