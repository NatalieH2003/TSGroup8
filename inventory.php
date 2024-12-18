<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory</title>
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
      .main-button {
        display: flex;
        align-items: left;
        padding: 10px;
      }
      .shop-button{
        display: flex;
        align-items: left;
        padding: 10px;
      }
      .row{
        display:flex;
        flex-direction: row;
      }
      .Icon{
        font-size: 50px;
        margin: 10px;
        display:inline-block;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.2s ease;
      }
      .Icon:hover{
        transform: scale(1.5);
      }
    </style>
  </head>
  <?php
      session_start();
      require "db.php";
      if(isset($_SESSION["username"])){
        if(isset($_COOKIE["equip"])){
            setEquipped($_SESSION["username"], $_COOKIE["equip"]);
        }
      }
      ?>
  <body>
    <h1>Inventory</h1>
    <div class="balance" id="gBucksDisplay"></div>
    <div class="container">
      <div id="display"></div>
      <div class="row">
        <button class="main-button" onclick="goToMainPage()">
          Back to Main Page
        </button>
        <button class="shop-button" onclick="goToShop()">
          Go to Shop
        </button>
    </div>

    <script>
      let items = JSON.parse(localStorage.getItem("inventory")) || [];
      console.log(items);
      //Loop through inventory and display
      items.forEach(displayItem);

      function displayItem(item){
        const display = document.getElementById("display");
        const element = document.createElement("div");
        element.className = "Icon";
        element.textContent = item;

        element.onclick = function(){
            equip(item);
          }

        display.appendChild(element);
      }

      function equip(emoji) {
        localStorage.setItem("equippedEmoji", emoji);
        document.cookie = "equip="+ emoji +"; SameSite=None; Secure";
        location.href = "inventory.php";
      }

      // Go back to Main Page
      function goToMainPage() {
        location.href = "TSP.php"; // Redirects to TSP.html
      }

      function goToShop(){
        location.href = "shop.php";
      }

    

    </script>
  </body>
</html>
