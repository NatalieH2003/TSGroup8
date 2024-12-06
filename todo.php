<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do-List</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #852928;
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
      .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
      }
      .main-button {
        display: flex;
        align-items: left;
        padding: 10px;
      }
      ul {
        list-style: none;
        padding: 0;
      }
      /*List items */
      ul li {
        cursor: pointer;
        position: relative;
        padding: 12px 8px 12px 40px;
        font-size: 18px;
        margin: 5px 0;
        background-color: #555;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      #input {
        width: 100%;
        height: 50px;
        padding: 10px;
        box-sizing: border-box;
      }
      .taskInput {
        width: 85%;
        height: 50px;
        padding: 10px;
        box-sizing: border-box;
      }
      .addTasks{
        width: 600px;  
        display: flex;
        flex-flow: row wrap;
        align-items: center;
      }
      .displayTask{
        width: 600px;  
        display: flex;
      }
      .taskLabel {
        width: 85%;
        padding-top: 15px;
        text-align: left;
        box-sizing: border-box;
      }
    </style>
  </head>
  
  <?php
  session_start();
  require "db.php";
  if(isset($_SESSION["username"])){
    if(isset($_POST["add"])){
        addTask($_SESSION["username"], $_POST["task"], 25);
    }
    if(isset($_POST["complete"])){
        completeTask($_SESSION["username"], $_POST["id"]);
    }
    if(isset($_POST["remove"])){
        removeTask($_SESSION["username"], $_POST["id"]);
    }
  }
  ?>
  <body>
    <h1>To-Do-List</h1>
    <div class="balance" id="gBucksDisplay"></div>
    <div class="container">
      <div class="row">
        <?php
        if(isset($_SESSION["username"])){
        ?>
        <form class="addTasks" method="post" action="todo.php">
        <input required type="text" class="taskInput" name="task" placeholder="Enter a task">
        <button type="submit" onclick="addTask()" value="add" name="add">Add</button>
        </form>
        <?php     
        }
        else{
        ?>
        <input type="text" id="input" placeholder="Enter a task" />
        <button class="Add" onclick="addTask()">Add</button>
        <?php
        }
        ?>
      </div>
      <ul id="list">
          <?php
          if(isset($_SESSION["username"])){
            $remainingTasks = getTasks($_SESSION["username"]);
            foreach($remainingTasks as $task){
            ?>
            <li>
                <form class="displayTask" method="post" action="todo.php">
                <label name="task" class="taskLabel"><?php echo $task[1]; ?></label>
                <input name="id" value= <?php echo $task[0]; ?> style="display: none;"></input>
                <div style="display: flex; gap: 5px;">
                    <button class="button-check" type="submit" value="complete" name="complete">✔</button>
                    <button class="button-remove" type="submit" value="remove" name="remove">X</button>
                </div>
                </form>
            </li>
            <?php
            }
          }
          ?>
          </ul>
      <button class="main-button" onclick="goToMainPage()">
        Back to Main Page
      </button>
    </div>

    <script>
        <?php 
        if(isset($_SESSION["username"])){
            if(isset($_POST["complete"])){
        ?>
            localStorage.setItem("shareCurrency", parseFloat(localStorage.getItem("shareCurrency"), 10)+25);
        <?php
            }
        }
        ?>
        
      //Variables
      let balance = parseFloat(localStorage.getItem("shareCurrency"), 10);
      document.getElementById(
        "gBucksDisplay"
      ).textContent = `Balance: ${balance}`;
      const input = document.getElementById("input");
      const lists = document.getElementById("list");

      function addTask() {
        const text = input.value.trim();
        //Message when nothing is typed
        if (text === "") {
          alert("You must type something!");
          return;
        }

        //Create task and add to list
        const newTask = document.createElement("li");
        newTask.textContent = text;
        lists.appendChild(newTask);

        //Create remove button
        const remove = document.createElement("button");
        remove.textContent = "X";
        remove.className = "button-remove";
        remove.addEventListener("click", removeTask);

        //Create check button
        const complete = document.createElement("button");
        complete.textContent = "✔";
        complete.className = "button-check";
        complete.addEventListener("click", checkTask);

        //Group buttons
        const buttonContainer = document.createElement("div");
        buttonContainer.style.display = "flex";
        buttonContainer.style.gap = "5px";
        newTask.appendChild(buttonContainer);
        buttonContainer.appendChild(complete);
        buttonContainer.appendChild(remove);

        //Reset textbox
        input.value = "";
        saveData();
      }
      //Function to remove task
      function removeTask(event) {
        const task = event.target.closest("li");
        if (task) {
          task.remove();
          saveData();
        }
      }
      //Function to check task
      function checkTask(event) {
        const task = event.target.closest("li");
        if (task) {
          task.remove();
          balance += 25;
          document.getElementById(
            "gBucksDisplay"
          ).textContent = `Balance: ${balance}`;
          saveData();
        }
      }
      // Go back to Main Page
      function goToMainPage() {
        location.href = "TSP.php"; // Redirects to TSP.html
      }
      function saveData() {
        localStorage.setItem("tasks", lists.innerHTML);
        localStorage.setItem("shareCurrency", balance);
      }
                          
    </script>
    </script>
  </body>
</html>
