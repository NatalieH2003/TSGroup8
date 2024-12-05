<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #7851a9;
        color: white;
        text-align: center;
        margin: 0;
        padding: 0;
      }
      h1 {
        margin-top: 20px;
      }
      .balance {
        text-align: center;
        font-size: 20px;
      }
      .container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #444;
        padding: 20px;
        border-radius: 10px;
        box-sizing: border-box;  
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      }
      .buttons {
        display: block;
        width: 100%;
        height: 100px;
        margin-top: 20px;
      }
      button {
        padding: 10px 20px;
        font-size: 25px;
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
      .emoji-section{
        padding: 10px 20px;
        font-size: 25px;
        margin: 5px;
        cursor: pointer;
      }
      .emoji{
        display: inline-block;
        font-size: 40px;
        margin: 10px;
        cursor: pointer;
        transition: transform 0.2s ease;
      }
      .emoji:hover{
        transform: scale(1.5);
      }
      h2 {
        font-size: 20px;
        margin: 10px;
        align-items: left;
      }
      .column {
        display:flex;
        flex-direction: column;
        align-items: center;
      }
      .row{
        display:flex;
        flex-direction: row;
      }
      .main-button {
        font-size: 16px;
        padding: 10px;
      }
      .inventory-button{
        font-size: 16px;
        padding: 10px;
      }
    </style>
  </head>
  <?php
        session_start();
        require "db.php";
        if(isset($_SESSION["username"])){
          if((!is_null($_COOKIE["itemGroup"])) && ($_COOKIE["itemGroup"] != -1)){
              buyItem($_SESSION["username"], $_COOKIE["itemGroup"], $_COOKIE["itemID"]);
          }
        }
        ?>
  <body>
    <h1>Shop</h1>
    <div class="balance" id="gBucksDisplay"></div>
    <div class="container">
      <div class="column">
        <div id = "suburb" class="emoji-section">
          <h2>Suburb Animals - 50</h2>
        </div>
        <div id = "farm" class="emoji-section">
          <h2>Farm Animals - 100</h2>
        </div>
        <div id = "forest" class="emoji-section">
          <h2>Forest Animals - 200</h2>
        </div>
        <div id = "ocean" class="emoji-section">
          <h2>Ocean Animals - 300</h2>
        </div>
        <div id = "desert" class="emoji-section">
          <h2>Desert Animals - 400</h2>
        </div>
        <div id = "mythical" class="emoji-section">
          <h2>Mythical/Extinct Animals - 500</h2>
        </div>
      <div class="row">
        <button class="main-button" onclick="goToMainPage()">
          Back to Main Page
        </button>
        <button class="inventory-button" onclick="goToInventory()">
          Go to Inventory
        </button>
      </div>
    </div>

    <script>
      let balance = parseFloat(localStorage.getItem("shareCurrency"), 10);
      let inventory = JSON.parse(localStorage.getItem("inventory")) || [];
      let purchased = JSON.parse(localStorage.getItem("purchased")) || [];
      //Index as ID
      const suburb = ["\u{1F422}", "\u{1F431}", "\u{1F436}", "\u{1F439}", "\u{1F42D}"];
      const farm = ["\u{1F404}", "\u{1F411}", "\u{1F413}", "\u{1F425}", "\u{1F416}"];
      const forest = ["\u{1F43F}", "\u{1F430}", "\u{1F43B}", "\u{1F43A}", "\u{1F98A}", "\u{1F98C}"];
      const ocean = ["\u{1F419}", "\u{1F420}", "\u{1F421}", "\u{1F42C}", "\u{1F433}"];
      const desert = ["\u{1F42B}", "\u{1F418}", "\u{1F98E}", "\u{1F981}", "\u{1F98F}"];
      const mythical = ["\u{1F9A4}", "\u{1F996}", "\u{1F409}", "\u{1F995}", "\u{1F984}"];
      
      displayItem("suburb", suburb, 50, 0);
      displayItem("farm", farm, 100, 1);
      displayItem("forest", forest, 200, 2);
      displayItem("ocean", ocean, 300, 3);
      displayItem("desert", desert, 400, 4);
      displayItem("mythical", mythical, 500, 5);

      //Retrieve variables
      document.getElementById("gBucksDisplay").textContent = `GBucks: ${balance}`;

      //Iterate through array and display
      function displayItem(sectionId, myArray, price, arrayNum){
        const section = document.getElementById(sectionId);
        let count = 0;

        //Array for purchased emojis
        const purchased = JSON.parse(localStorage.getItem("purchased")) || [];

        myArray.forEach((item) => {
          const emoji = document.createElement("div");  //Create emoji element
          emoji.classList.add("emoji");
          emoji.id = count;
          emoji.innerHTML = item; //Display emoji

          //Check if emoji is in purchased array
          if(purchased.includes(item)){
            emoji.style.pointerEvents = "none";
            emoji.style.opacity = .2;
          } 
          else if (balance < price){
            emoji.style.pointerEvents = "none";   
          }
          else{
            //Call buy function when emoji is clicked
            emoji.onclick = function(){
              buy(emoji, item, price, arrayNum, emoji.id);
            };
          }
          
          section.appendChild(emoji); //Add emoji to section
          count++;
        });
      }

      //Buy emoji and place in inventory
      function buy(element, emoji, price, group, id) {
        const purchased = JSON.parse(localStorage.getItem("purchased")) || [];

        if (balance >= price) {
          balance -= price;

          inventory.push(emoji);  //Push emoji to inventory
          document.getElementById("gBucksDisplay").textContent = `GBucks: ${balance}`;

          element.clicked = true;
          purchased.push(emoji);  //Push emoji to purchased list

          localStorage.setItem("purchased", JSON.stringify(purchased));
          saveData();
          element.style.pointerEvents = "none";
          document.cookie = "itemGroup="+ group +"; SameSite=None; Secure";
          document.cookie = "itemID="+ id +"; SameSite=None; Secure";
          location.href = "shop.php";
        } 
        else{
          alert("Not enough GBucks!");
        }
      }

      // Go back to Main Page
      function goToMainPage() {
        location.href = "TSP.php"; // Redirects to TSP.html
      }

      //Go to Inventory
      function goToInventory() {
        location.href = "inventory.html"; 
      }

      function saveData() {
        localStorage.setItem("shareCurrency", balance);
        localStorage.setItem("inventory",JSON.stringify(inventory));
      }

    </script>
    </div>
  </body>
</html>
