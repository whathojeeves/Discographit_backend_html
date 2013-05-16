<?php

$queryName = $_GET['sQuery'];
$queryType = $_GET['qT'];

$url = 'http://www.allmusic.com/search/'.$queryType.'/'.urlencode($queryName);
$url = str_replace(" ","+", $url);

$out_html_content = '<html><head><meta charset="utf-8"></head><body>';
$out_html_content .= '<h1><center>Search Results</h1></center><br/><h4><center>"'.$queryName.'" of type "'.$queryType.'"</center><br/>';

/* allmusic is down. use saved file*/
$returnData = file_get_contents($url);

$in_search_res = preg_match("/search\-results/", $returnData, $matches_main);
//print_r($matches_main);
//echo $in_search_res;
if($in_search_res != 0)
{
	if($queryType == "artists")
	{
		$found_entries = preg_match_all("/search-result artist(.*?)<\/tr>/s", $returnData, $matches_each);
		if($found_entries != 0)
		{
			$out_html_content .= '<center><table border="2"><tr><th>Image</th><th>Name</th><th>Genre</th><th>Year</th><th>Link</th></tr>';
			for($i=0;$i<5 && $i<count($matches_each[0]);$i++)
			{
				$out_html_content .= '<tr>';
				$matchres = preg_match("/class=\"cropped-image\"(.*?)<\/div>/", $matches_each[0][$i], $image_data);
				$out_html_content .= '<td>';
				if($matchres == 0)
				{
					$out_html_content .= '<img src = "./artist_def_image.png"';	
				}
				else
				{
					preg_match("/img src=\"(.*?)\"/", $image_data[0], $image_link_data);
					$image_link = explode("=\"", $image_link_data[0]);
					$img_out = 1;
					$out_html_content .= '<img src = "'.trim($image_link[1]);
				}
				$out_html_content .= '</td>';
				/* Name */

				//preg_match("/class=\"name(.*?)<\/div>/s", $matches_each[0][$i], $name_data);
				if(preg_match("/class=\"name(.*?)<\/div>/s", $matches_each[0][$i], $name_data) != 0)
				{
					$matchres = preg_match("/}}\">(.*?)</", $name_data[0], $name);
				}
				if($matchres == 0)
				{
					$out_html_content .= 'NA';	
				}
				else
				{
					$name_s = trim(substr($name[0],4,-1));	/* Name done */
					$out_html_content .= '<td>'.$name_s.'</td>';
				}

				/* info link */
				if(preg_match("/<a href=\"(.*?)\"/", $name_data[0], $info_link_data) != 0)
				{
					$info_link = explode("=", $info_link_data[0]);
				}
				/* info : genre a;nd year */

				if(preg_match("/class=\"info(.*?)<\/div>/s", $matches_each[0][$i], $data_info) != 0)
				{
					$info_lines = explode("<br/>", $data_info[0]);
					$genre = trim(substr($info_lines[0],13));
					$year = trim(substr($info_lines[1],0,-7));

					if(trim($genre) != null)
						$out_html_content .= '<td>'.$genre.'</td>';
					else
						$out_html_content .= '<td>NA</td>';

					if(trim($year) != null)
						$out_html_content .= '<td>'.$year.'</td>';
					else
						$out_html_content .= '<td>NA</td>';
				}
				if(trim($info_link[1]) != "")
					$out_html_content .= '<td><a href='.$info_link[1].'>details</td>';
				else
					$out_html_content .= '<td>NA</td>';

				/* genre and year done */
				$out_html_content .= '</tr>';
			}
		}
		$out_html_content .= '</table></center></body></html>';
		echo $out_html_content;
	}

else if($queryType == "albums")
	{
		preg_match_all("/search-result album(.*?)<\/tr>/s", $returnData, $matches_each);
		$out_html_content .= '<center><table border="2"><tr><th>Image</th><th>Title</th><th>Artist</th><th>Genre</th><th>year</th><th>details</th></tr>';
		$found_entries = 1;
		if($found_entries == 1)
		{
			for($i=0;$i<5 && $i<count($matches_each[0]);$i++)
			{
				$out_html_content .= '<tr><td>';
				$matchres = preg_match("/class=\"cropped-image\"(.*?)<\/div>/", $matches_each[0][$i], $image_data);
				if($matchres == 0)
				{
					$out_html_content .= '<img src = "./album_def.png">';	
				}
				else
				{
					preg_match("/img src=\"(.*?)\"/", $image_data[0], $image_link_data);
					$image_link = explode("=\"", $image_link_data[0]);
					$out_html_content .= '<img src = "'.$image_link[1];	
				}
				$out_html_content .= '</td>';

				/* title */
				preg_match("/class=\"title(.*?)<\/div>/s", $matches_each[0][$i], $title_data);
				preg_match("/}}\">(.*?)</", $title_data[0], $title);
				$title_s = substr($title[0],4,-1);
				if(trim($title_s) != null)
				{
					$out_html_content .= '<td>'.$title_s.'</td>';		
				}
				else
					$out_html_content .= '<td>NA</td>';

				/* Artist Name */

				preg_match("/class=\"artist(.*?)<\/div>/s", $matches_each[0][$i], $artist_name_data);
                                /* Test */ print_r($artist_name_data); echo " ".$i;
				if(!preg_match("/\">(.*?)</", $artist_name_data[0], $artist_name))
 				{
					preg_match("/\">(.*?)/", $artist_name_data[0], $artist_name);
				}
				
				$artist_name_s = substr($artist_name[0],2,-1);	/* Artist Name done */
				if(trim($artist_name_s) != null)
				{
					$out_html_content .= '<td>'.$artist_name_s.'</td>';
				preg_match("/<a href=\"(.*?)\"/", $artist_name_data[0], $info_link_data);
				$info_link = explode("=", $info_link_data[0]);
                                $info_link_present = 1;
				}
				else {
					$out_html_content .= '<td>NA</td>';
					$info_link_present = 0; }

				/* info link */
				//preg_match("/<a href=\"(.*?)\"/", $artist_name_data[0], $info_link_data);
				//$info_link = explode("=", $info_link_data[0]);
				/* info : genre a;nd year */

				preg_match("/class=\"info(.*?)<\/div>/s", $matches_each[0][$i], $data_info);
				

				$info_lines = explode("<br/>", $data_info[0]);
				$year = substr($info_lines[0],13);
				$genre = substr($info_lines[1],0,-7);

				if(trim($genre) != null)
					$out_html_content .= '<td>'.$genre.'</td>';
				else
					$out_html_content .= '<td>NA</td>';
				if(trim($year) != null)
					$out_html_content .= '<td>'.$year.'</td>';
				else
					$out_html_content .= '<td>NA</td>';

				if($info_link_present == 1 && trim($info_link[1]) != "")
					$out_html_content .= '<td><a href='.$info_link[1].'>details</td>';
				else
					$out_html_content .= '<td>NA</td>';

				/* genre and year done */
				$out_html_content .= '</td>';
				$out_html_content .= '</tr>';
			}
		}
		$out_html_content .= '</table></center></body></html>';
		echo $out_html_content;
	}

	else if($queryType == "songs")
	{
		preg_match_all("/search-result song(.*?)<\/tr>/s", $returnData, $matches_each);
		$found_entries = 1;
		if($found_entries == 1)
		{
			$out_html_content .= '<center><table border="2"><tr><th>Song Sample</th><th>Title</th><th>Performer</th><th>Composers</th><th>Link</th></tr>';
			for($i=0;$i<5 && $i< count($matches_each[0]);$i++)
			{
				$out_html_content .= '<tr>';
				$matchres = preg_match("/class=\"ui360 icon-search-song-new\"(.*?)<\/div>/s", $matches_each[0][$i], $play_song_data);
				if($matchres == 0)
				{
					$out_html_content .= '<td><img src="./play_song.png"></td>';
				}
				else
				{
					preg_match("/<a href=\"(.*?)\"/", $play_song_data[0], $play_song_link_data);
					$play_song_link = explode("=\"", $play_song_link_data[0]);
					$out_html_content .= '<td><a href="'.$play_song_link[1].'><img src="./play_song.png"></a></td>';
				}

				/* title */
				preg_match("/class=\"title(.*?)<\/div>/s", $matches_each[0][$i], $title_data);
				preg_match("/\">(.*?)</", $title_data[0], $title);
				$title_s = substr($title[0],8,-7);
				if(trim($title_s) != null)
				{
					$out_html_content .= '<td>'.$title_s.'</td>';
				}
				else
					$out_html_content .= '<td>NA</td>';

				/* Performer Name */

				preg_match("/span class=\"performer(.*?)<\/span>/s", $title_data[0], $performer_name_data);
				preg_match_all("/\">(.*?)</", $performer_name_data[0], $performer_names);
				if(count($performer_names[0]) == 0)
				{
					$out_html_content .= '<td>NA</td>';
				}
				else
				{
					$out_html_content .= '<td>';
					for($j=0; $j<count($performer_names[0]); $j++)
					{
						if(preg_match("/.?(by).?/", $performer_names[0][$j]) == 0)
						{
							$performer_name_s = substr($performer_names[0][$j],2,-1);	/* Artist Name done */
							$out_html_content .= $performer_name_s;
							if($j != count($performer_names[0]) - 1)
								$out_html_content .= ',';
						}
					}
					$out_html_content .= '</td>';
				}

				/* info link */
				preg_match("/<a href=\"(.*?)\"/", $title_data[0], $info_link_data);
				$info_link = explode("=", $info_link_data[0]);
				/* info : genre a;nd year */

				preg_match("/class=\"info(.*?)<\/div>/s", $matches_each[0][$i], $data_info);
				preg_match_all("/\">(.*?)</", $data_info[0], $composer_name_data);
				if(count($composer_name_data[0]) == 0)
				{
					$out_html_content .= '<td>NA</td>';
				}
				else
				{
					$out_html_content .= '<td>';
					for($j=0; $j<count($composer_name_data[0]); $j++)
					{
						$composer_name_s = substr($composer_name_data[0][$j],2,-1);
						$out_html_content .= $composer_name_s;
						if($j != count($composer_name_data[0]) - 1)
								$out_html_content .= ',';
					}
					$out_html_content .= '</td>';
				}

				if(trim($info_link[1]) != "")
					$out_html_content .= '<td><a href='.$info_link[1].'>details</td>';
				else
					$out_html_content .= '<td>NA</td>';

				$out_html_content .= '</tr>';	
				/*$info_lines = explode("<br/>", $data_info[0]);
				$year = substr($info_lines[0],13);
				$genre = substr($info_lines[1],0,-7);
				echo "<br/>".$genre;
				echo "<br/>".$year;*/
				/* genre and year done */
			}
		}
		$out_html_content .= '</table></center></body></html>';
		echo $out_html_content;
	}
	
}

else
{
	$out_html_content .= "<br/><br/><center><h2> No discography found </h2></center></body></html>";
	echo $out_html_content;
}
//print_r($matches_main);
//print_r($matches_each);



//echo $returnData;
?>
