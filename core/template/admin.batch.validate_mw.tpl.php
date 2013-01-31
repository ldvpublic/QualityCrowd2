<?php

require_once(__DIR__ . "/../3p/parsecsv/parsecsv.lib.php");

function parseMicroworkersData($data) {

        $csv_head = "ROW,ID_TASK,ID_WORKER,TASK_RATING,FINISHED,IP,COUNTRY_CODE,PROOF,EMPLOYER_COMMENT";

        $result = strpos($data, $csv_head);

        if ($result === false) {

                die("Couldn't find the CSV head in your pasted stuff. <br/>I was looking for: '" . $csv_head . "'<br/>");
        }

        $mw_result = substr($data, $result);

        $csv = new parseCSV();

        $csv->parseCSV($mw_result);

        $return = array();

        $token_list = array();

	// Reverse the passed data (we want increasing time)
	$csv->data = array_reverse($csv->data);

        foreach ($csv->data AS $row) {

                $linedata = array();

                $linedata["worker_id"] = $row["ID_WORKER"];
                $linedata["proof_raw"] = $row["PROOF"];

	
                if (!preg_match('/xy([a-f0-9]{12})yx/', $row["PROOF"], $matches)) {
 
			$linedata["token_valid"] = "0";
                        $linedata["token_invalid_reason"] = "Counld not find a valid token!";
 
                } else {
        
                	// token found

                        if (in_array($matches[1], $token_list)) {

                        	// Token was used before
                                $linedata["token_valid"] = "0";
                                $linedata["token_invalid_reason"] = "Token $matches[1] was used before!";
                                        
                        } else {

                                $linedata["token_valid"] = "1";
                                $linedata["token"] = $matches[1];

                                $token_list[] = $matches[1];
                        }
                }
		
                $linedata["worker_ip"]      = $row["IP"];
                $linedata["worker_country"] = $row["COUNTRY_CODE"];
		$linedata["timestamp_mw"]   = $row["FINISHED"];

                $return[] = $linedata;

        }

        return $return;
}

if ($mw_csvexport <> '') {
	
	$return = parseMicroworkersData($mw_csvexport);

	foreach ($return as & $mw_worker) {

                $workerId = preg_replace("/[^a-zA-Z0-9-]/", "", $mw_worker["worker_id"]);

                $result = $batch->getWorker($workerId);


		if (!$result) {
		
			// Worker did not even open the homepage		

			$mw_worker["comment"] = "User did not do the test!";
			$mw_worker['finished'] = 0;
			continue;
		}

		// Check if user has finished the test
		if ($result['finished']) {

			// Check if token matches (if the worker specified a token)

			if ($mw_worker['token_valid'])
		                if ($mw_worker['token'] == $result['token'])
        		                $mw_worker['token_match'] = "1";
                		else {
                        		$mw_worker['token_match']   = "0";
					$mw_worker['token_should_be'] = $result['token'];
		                }
		}

		$mw_worker['timestamp'] = $result['timestamp'];
		$mw_worker['finished'] = $result['finished'];
	}

//	print_r($return);
}

?>

<form action="?" method="post">
	<fieldset>
		<legend>Batch Taken Evaluation</legend>
		<label for="mw_csvexport">Token List (one per line)</label>
		<textarea name="mw_csvexport" rows="10" cols="10"></textarea>
	</fieldset>
	
	<button>Search</button>
</form>


<?php if ($mw_csvexport <> ''): ?>
<h3>Result</h3>
<table class="meta">
	<?php if (is_array($return)): ?>

	<tr>
		<th>MW Worker ID</th>
		<th>Token Submitted</th>
		<th>Finished Batch?</th>
		<th>Token matches?</th>
		<th>Comment</th>
	</tr>

<?php
	foreach ($return AS $mw_worker) {
	
		echo("<tr>");
		echo("<td>".  $mw_worker["worker_id"] . "</td>");
	
                if ($mw_worker['token_valid'])
                        echo('<td>');
                else
                        echo('<td style="background-color: #ff0000">');
	
                echo(($mw_worker['token_valid'] ? $mw_worker['token'] : 'INVALID') . "</td>");

		if ($mw_worker['finished'])
			echo('<td>');
		else
			echo('<td style="background-color: #ff0000">');
                
		echo(($mw_worker['finished'] ? 'Yes' : 'No') . "</td>");

		if ($mw_worker['finished']) {

	                if ($mw_worker['token_match'])
        	                echo('<td>');
                	else
                        	echo('<td style="background-color: #ff0000">');

        	        echo(($mw_worker['token_match'] ? 'Yes' : ('No (should be '.$mw_worker['token_should_be'].')')) . "</td>");

		} else {
			
			echo('<td>-</td>');
		}

		if (isset($mw_worker["comment"])) {

			echo('<td>'. $mw_worker["comment"] .'</td>');
		} else {
			echo('<td></td>');
		}
		
		echo("<td></td>");
		echo("</tr>");

	}
?>
		
<?php else: ?>
	<tr>
		<th>Microworkers Export</th>
		<td><?= $query ?></td>
	</tr>
	<tr>
		<th>Result</th>
		<td>invalid file!</td>
	</tr>
<?php endif; ?>
</table>
<?php endif; ?>
