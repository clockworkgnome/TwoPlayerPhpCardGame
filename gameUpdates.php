<?php
session_start();
require_once('inc/db.inc.php');

// if the user is logged in
if ($_SESSION["bLoggedIn"]) {

	//get the users id
	$iUserID = $_SESSION["sUserID"];
	//get the game id
	$daGameId=$_SESSION["gameID"];

	// gets the other users id and game info
	if($_SESSION["playerNum"]==2){

		//get the other users id
		$sSQL = "SELECT player1 FROM `cardGames` WHERE `id` = '".$daGameId."'";
		$_SESSION["otherPlayerID"] = $GLOBALS['MySQL']->getOne($sSQL);

	}elseif($_SESSION["playerNum"]==1){

		//get the other users id
		$sSQL = "SELECT player2 FROM `cardGames` WHERE `id` = '".$daGameId."'";
		$_SESSION["otherPlayerID"] = $GLOBALS['MySQL']->getOne($sSQL);

	}

	// try to get other players game status
	$sSQL = "SELECT status FROM `cardGameInfo` WHERE `playerId` = '".$_SESSION["otherPlayerID"]."' AND `gameId` = '".$daGameId."'";
	$_SESSION["otherPlayerStatus"] = $GLOBALS['MySQL']->getOne($sSQL);


	if($_SESSION["otherPlayerStatus"]!=false){

		$sSQL = "SELECT needsUpdate FROM `cardGameInfo` WHERE `playerId` = '".$_SESSION["otherPlayerID"]."'";
		$_SESSION["needsUpdate"] = $GLOBALS['MySQL']->getOne($sSQL);

		//display other players deck vissual after they start game
		if($_SESSION["otherPlayerStatus"]=="showDeck" && $_SESSION["needsUpdate"]=="true"){

			// set needs update to false
			$sql = "UPDATE `jujugameengine`.`cardGameInfo` SET `needsUpdate` = 'false' WHERE CONVERT(`cardGameInfo`.`playerId` USING utf8) = '".$_SESSION["otherPlayerID"]."' LIMIT 1;";
			$GLOBALS['MySQL']->res($sql);

			if($_SESSION["playerNum"]==1){
				// it is here we can display the number of mana and players health ect on the players deck
				$strDeckText ="Player 2";
			}elseif($_SESSION["playerNum"]==2){
				// it is here we can display the number of mana and players health ect on the players deck
				$strDeckText ="Player 1";
			}
			// the following makes the GUI deck vissual for player 2
			$strDeckVis2="
					<script>
					var player2deck = document.createElement('div');
					player2deck.id = 'deck2';
					player2deck.className = 'deck2';
					var deckFront = document.createElement('div');
					deckFront.id = 'front';
					deckFront.innerHTML ='player 2 deck';
					player2deck.appendChild(deckFront);
					document.getElementsByTagName('body')[0].appendChild(player2deck);
					</script>

					";
			echo $strDeckVis2;


		}

		//if first hand drawn or in draw phase
		if($_SESSION["otherPlayerStatus"]=="handDrawn" && $_SESSION["needsUpdate"]=="true"){

			// set needs update to false
			$sql = "UPDATE `jujugameengine`.`cardGameInfo` SET `needsUpdate` = 'false' WHERE CONVERT(`cardGameInfo`.`playerId` USING utf8) = '".$_SESSION["otherPlayerID"]."' LIMIT 1;";
			$GLOBALS['MySQL']->res($sql);


			//get the other players hand
			$sSQL = "SELECT hand FROM `cardGameInfo` WHERE `playerId` = '".$_SESSION["otherPlayerID"]."'";
			$opHand = $GLOBALS['MySQL']->getOne($sSQL);
			$opHand=json_decode ($opHand);

			// count the number of cards in other players hand
			//echo "the other player has ".count($opHand)." card in their hand.";
			//var_dump($opHand);
			//display amount of tokens equal to # of cards in player hand
			echo "<script>";
			$cardX =22;
			for($i=0;$i<count($opHand);$i++){
				$strHandTolken=$strHandTolken."
				var player2hand$i = document.createElement('div');
				player2hand$i.id = 'player2hand$i';
				player2hand$i.className = 'player2hand$i';
				var FrontPlayer2hand$i = document.createElement('div');
				FrontPlayer2hand$i.id = 'front';
				FrontPlayer2hand$i.innerHTML ='Player2hand$i';
				player2hand$i.appendChild(FrontPlayer2hand$i);
				document.getElementsByTagName('body')[0].appendChild(player2hand$i);

				";
				$cardX=$cardX+3;
			}
			echo $strHandTolken;
			echo "</script>";

		}

		//start the game if both players have drawn their hand
		if($_SESSION["otherPlayerStatus"]=="handDrawn" || $_SESSION["otherPlayerStatus"]=="drawPhase" || $_SESSION["otherPlayerStatus"]=="waiting" && $_SESSION["needsUpdate"]=="false"){
			//the other player drew their hand and it has been displayed

			//get this players status
			$sSQL = "SELECT status FROM `cardGameInfo` WHERE `playerId` = '".$_SESSION["sUserID"]."' AND `gameId` = '".$daGameId."'";
			$_SESSION["myStatus"] = $GLOBALS['MySQL']->getOne($sSQL);

			//get this players needs update
			$sSQL = "SELECT needsUpdate FROM `cardGameInfo` WHERE `playerId` = '".$_SESSION["sUserID"]."'";
			$_SESSION["myUpdate"] = $GLOBALS['MySQL']->getOne($sSQL);

			//this player drew their hand and its been displayed
			if($_SESSION["myStatus"]=="handDrawn" && $_SESSION["myUpdate"]=="false"){
				// check to see if this person is player 1 or two
				if($_SESSION["playerNum"]==1){
					// if player 1 set status to drawPhase
					$sql = 'UPDATE `jujugameengine`.`cardGameInfo` SET `status` = \'drawPhase\', `needsUpdate` = \'false\' WHERE `cardGameInfo`.`playerId` = \''.$_SESSION["sUserID"].'\'';
					$GLOBALS['MySQL']->res($sql);
					//update deckText to say draw card
					echo"
						dynamicTexture.clear('cyan')
							.drawText('Draw Card', undefined, 256, 'gold')";
				}elseif($_SESSION["playerNum"]==2){
					$sql = 'UPDATE `jujugameengine`.`cardGameInfo` SET `status` = \'waiting\', `needsUpdate` = \'false\' WHERE `cardGameInfo`.`playerId` = \''.$_SESSION["sUserID"].'\'';
					$GLOBALS['MySQL']->res($sql);
					//echo"deckText.drawText(\"Player 1 Draws Card\", 40, 60, 'white');";
				}
				$_SESSION["round"]=1;
			}
		}
		// get the players grave yard
		// count the number of cards in the grave yard
		// make grave yard token with a count of number of cards in grave yard

		// get the players in play cards and display them

	}
}
?>
