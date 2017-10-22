<?php
include_once 'classes/Parser.php';
include_once 'classes/CSV.php';

class Command {
	
	public function __construct($command, $parameter = false){
		switch ($command) {
			case 'parse':
				if($parameter == null){
					echo "The parameter URL is required. The example is : \"php serpstat parse http://example.com\" . Or use the command help to see the instructions.";
					exit();
				}
				$parser = new Parser($parameter);
				$is_parsed = $parser->startParse();
				$csv = new CSV();
				$csv->saveReport($parameter, $parser->links_with_images);
				break;
			case 'report':
				if($parameter == null){
					echo "The parameter URL is required. The example is : \"php serpstat report http://example.com\" . Or use the command help to see the instructions.";
					exit();
				}
				$csv = new CSV();
				$csv->showReport($parameter);
				break;
			case 'help':
				include_once 'help.txt';
				break;
		   default:
				echo 'Use the command "help" for showing the list of commands';
		}
	}
	
}

?>