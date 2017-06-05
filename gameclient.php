<?php
session_start();
require_once('inc/db.inc.php');

// if the user is logged in
if ($_SESSION["bLoggedIn"]) {

	//get the users id
	$iUserID = $_SESSION["sUserID"];

	//get the users status
	$sSQL = "SELECT status FROM `s_members` WHERE `id` = '".$iUserID."'";
	$_SESSION["sUserStatus"] = $GLOBALS['MySQL']->getOne($sSQL);
}
?>
<html>
    <head>
		<title>Game Client</title>
		<style>
		body {
		    background-color: #d0e4fe;
		    margin: 0;
		}

		h1 {
		    color: orange;
		    text-align: center;
		}

		p {
		    font-family: "Times New Roman";
		    font-size: 20px;
		}

		.clickables
		{
		    cursor: pointer;
		}

		canvas { width: 100%; height: 100% }
		</style>

	</head>
	<body>
		<script src='js/jquery-2.1.3.min.js'></script>

		<script>
//****************************************************************************************************************
//this loop will updates what the other player is doing
//****************************************************************************************************************
			function myTimer() {
				$.post("gameUpdates.php",
				{
					gameRequest: "getUpdates",
				},
				function(data, status){
					if(status == "success"){
						$("#playerUpdates").html(data);
					}else{
						$("#playerUpdates").html("failed...");
					}
				});
			}
			myTimer();
			var myVar = setInterval(function(){ myTimer() }, 5000);

			function myStopFunction() {
			    clearInterval(myVar);
			}

//*********************************************************************************************************
//this is the code for the start button
//*********************************************************************************************************

var startButton = document.createElement('div');
startButton.id = 'startButton';
startButton.innerHTML="<button type='button' onClick='startClicked()'>Start</button>";
document.getElementsByTagName('body')[0].appendChild(startButton);

//***********************************************************************************************
// if the user clicked the start button
//***********************************************************************************************
function startClicked(){
			$.post("cardgameclasses.php",
				    {
						gameRequest: "getCards",
				    },
				    function(data, status){
				        //alert("Data: " + data + "\nStatus: " + status);
				        if(status == "success"){
				        	//$.getScript("cardgameclasses.php");
				        	// this script will remove the start button and add the deck vissual
				        	$("#GameCommand").html(data);
				        }
				    });
}

//*******************************************************************************************************
// the user clicked the deck
//*******************************************************************************************************
function deckClicked(){
			$.post("cardgameclasses.php",
				    {
						gameRequest: "drawHand",
				    },
				    function(data, status){
				    	//alert("Data: " + data + "\nStatus: " + status);
				        if(status == "success"){
				        	//$.getScript("cardgameclasses.php");
				        	// this script will remove the start button and add the deck vissual
				        	$("#GameCommand").html(data);
				        }
				    });
}
//*********************************************************************************************************

		</script>
<div id="GameCommand"></div>
<div id="playerUpdates"></div>
	</body>
</html>
