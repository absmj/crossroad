<?php


class Dict
{

	function __construct()
	{

	}

	function NitqHissesi_prefix($n)
	{
		$n = preg_replace('/\s|\r|\t|" "/mu', '', $n);
		switch ($n) {
			case 'noun':
				$nitq = "n";
				break;

			case 'adverb':
				$nitq = "adv";
				break;

			case 'adjective':
				$nitq = "adj";
				break;

			case 'verb':
				$nitq = "v";
				break;

			case 'pronoun':
				$nitq = "p";
				break;

			case 'preposition':
				$nitq = "pre";
				break;

			case 'conjuction':
				$nitq = "con";
				break;

			case 'interjection':
				$nitq = "int";
				break;

			case 'article':
				$nitq = "ar";
				break;
			
			default:
				$nitq = substr($n, 0, 3);
				break;
		}

		return $nitq;

	}


	function AzerDict($word)
	{
		$fix_w = preg_replace("/\s/mu", "-", $word);
		$content = file_get_contents("https://azerdict.com/english/".$fix_w);
		$result = array();
		$addition = array();
		$direct = array();
		$nitqHissesi = array();
		$message = '';
		if($content !== FALSE)
		{
			preg_match_all("/(\<a.*?result-word\".*?\>)(.*?)(\<\/a\>)|(\<td.*?pos\"\>)(.*?)(\<\/td\>)/mu", $content, $matches);

			if(!empty($matches))
			{
				foreach ($matches[5] as $key => $value) {

					if($value != '') 
						array_push($nitqHissesi, $matches[5][$key]);
	
				}

				foreach ($matches[2] as $key => $value) {
					if(preg_match("/^".$word."$/mu", $value))
						@array_push($direct, ["en"=>$matches[2][$key], "az"=>$matches[2][$key + 1]]);

					if(preg_match("/.*?[\s|\-]".$word.".*?|.*?[\s|\-]".$word."|".$word."[\s|\-].*?/mu", $value))
						@array_push($addition, ["en"=>$matches[2][$key], "az"=>$matches[2][$key + 1]]);
				}

				foreach ($nitqHissesi as $key => $value) {
					if($key < count($direct) - 1)
						array_push($result, ["en"=>$direct[$key]["en"], "az"=>$direct[$key]["az"], "nitq"=> $this->NitqHissesi_prefix($value)]);
					else
					{
						
						@array_push($result, ["en"=>$addition[$key]["en"], "az"=>$addition[$key]["az"], "nitq"=> $this->NitqHissesi_prefix($value)]);
					}
				}


				foreach ($result as $key => $value) {
					if($value["en"] == '') unset($result[$key]);
					if($value["az"] == $value["en"]) unset($result[$key]);
				}

				echo json_encode($result);
				
				
				
			}
			

		}
		
	}

	function TurEng($word)
	{
		$fix_w = preg_replace("/\s/mu", "%20", $word);
		$content = file_get_contents("https://tureng.com/en/turkish-english/".$fix_w);
		$result = array();
		$addition = array();
		$direct = array();
		$nitqHissesi = array();
		$en = array();
		$tr = array();
		$message = '';
		if($content !== FALSE)
		{
			preg_match_all("/((\<td.*?en tm.*\>)(.*?)(\<\/a\>)|(\<i\>)(.*)(\<\/i\>))|((\<td.*?tr ts.*\>)(.*?)(\<\/a\>))/mu", $content, $matches);

			if(!empty($matches))
			{
				foreach ($matches[6] as $key => $value) {

					if($value != '') 
						array_push($nitqHissesi, $value);
	
				}

				foreach ($matches[3] as $key => $value) {
					if($value != '') 
						array_push($en, $value);
				}

				foreach ($matches[10] as $key => $value) {
					if($value != '') 
						array_push($tr, $value);
				}

	
				foreach ($en as $key => $value) {
					if(preg_match("/^".$word."$/mu", $value))
						@array_push($direct, ["en"=>$value, "tr"=>$tr[$key]]);

				}

				foreach ($nitqHissesi as $key => $value) {
					$rrt = preg_replace("/\.|\s/mu", "", $value);
					if($key < count($direct) - 1)
						array_push($result, ["en"=>$direct[$key]["en"], "tr"=>$direct[$key]["tr"], "nitq"=> substr($rrt, 0, 3)]);

				}


				foreach ($result as $key => $value) {
					if($value["nitq"] == '') unset($result[$key]);
					if($value["tr"] == $value["en"]) unset($result[$key]);

				}
				
				echo json_encode($result);
					
				}
				
			}

		}
	}


/* Use */
$test = new Dict();
$test->AzerDict("after");
$test->TurEng("after");


?>