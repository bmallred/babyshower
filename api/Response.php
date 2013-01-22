<?php

require "Database.php";

//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");
$objData = json_decode($data);

$client = null;
$event = null;
$firstName = null;
$lastName = null;
$fields = array();
$returnValue = false;

// Iterate through all of our values.
foreach ($objData as $key => $value) {
	switch ($key) {
		case "client":
			$client = $value;
			break;

		case "event":
			$event = $value;
			break;

		case "firstName":
			$firstName = $value;
			break;

		case "lastName":
			$lastName = $value;
			break;
		
		default:
			$fields[$key] = $value;
			break;
	}
}

// Make sure we have our required fields.
if ($client && $event && $firstName && $lastName) {
	// Create a database connection.
	$pdo = new Database();

	$queryClient = null;

	try {
		// Prepare to find the client.
		$queryClient = $pdo->prepare("SELECT id FROM client WHERE name = :name");
		$queryClient->bindParam(":name", $client);
		$queryClient->execute();
		$results = $queryClient->fetch();

		if ($results) {
			$clientId = $results["id"];
			$queryEvent = null;

			try {
				// Prepare to find the event.
				$queryEvent = $pdo->prepare("SELECT id FROM event WHERE name = :name AND clientId = :clientId");
				$queryEvent->bindParam(":name", $event);
				$queryEvent->bindParam(":clientId", $clientId);
				$queryEvent->execute();
				$results = $queryEvent->fetch();

				if ($results) {
					$eventId = $results["id"];
					$queryResponse = null;

					try {
						// Prepare to add the initial response.
						$queryResponse = $pdo->prepare("INSERT INTO response (eventId, firstName, lastName, replyDate) VALUES (:eventId, :firstName, :lastName, :replyDate)");
						$queryResponse->bindParam(":eventId", $eventId);
						$queryResponse->bindParam(":firstName", $firstName);
						$queryResponse->bindParam(":lastName", $lastName);
						$queryResponse->bindParam(":replyDate", date("Y-m-d H:i:s"));
						$queryResponse->execute();
						$responseId = $pdo->lastInsertId();

						if ($responseId) {
							$queryResponseFields = null;

							try {
								// Prepare to add any additional fields.
								$queryResponseFields = $pdo->prepare("INSERT INTO response_field (responseId, fieldName, fieldValue) VALUES (:responseId, :fieldName, :fieldValue)");

								foreach ($fields as $key => $value) {
									$queryResponseFields->bindParam(":responseId", $responseId);
									$queryResponseFields->bindParam(":fieldName", $key);
									$queryResponseFields->bindParam(":fieldValue", $value);
									$queryResponseFields->execute();
									$results = $queryResponseFields->rowCount();
								}
							}
							catch (PDOException $e) {
								echo $e->getMessage();
								die();
							}
							
							if ($queryResponseFields) {
								$queryResponseFields->closeCursor();
							}
						}
						
						$returnValue = true;
					}
					catch (PDOException $e) {
						echo $e->getMessage();
						die();
					}

					if ($queryResponse) {
						$queryResponse->closeCursor();
					}
				}
			}
			catch (PDOException $e) {
				echo $e->getMessage();
				die();
			}

			if ($queryEvent) {
				$queryEvent->closeCursor();
			}
		}
	}
	catch (PDOException $e) {
		echo $e->getMessage();
		die();
	}

	if ($queryClient) {
		$queryClient->closeCursor();
	}

	// Close the connection.
	$pdo = null;
}

echo json_encode($returnValue);

if ($returnValue) {
	$headers = "From: info@nvite.us";
	$text = "You just received an RSVP from $firstName $lastName!\n\n";

	foreach ($fields as $key => $value) {
		$text = $text . ucfirst($key) . ": " . $value . "\n";
	}

	$text = str_replace("\n.", "\n..", $text);

	mail("me@somewhere.com", "You have an RSVP!", $text, $headers);
}