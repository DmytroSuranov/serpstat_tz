<?php

class Parser {
	public $url;
	public $host;
	public $links = array();
	public $parsed_links = array();
	public $links_with_images = array();
	public $cnt;

	public function __construct($url){
		$this->url = $url;
		return $this;
	}

	public function startParse($link = false){
		if(!$link){
			$link = $this->url;
			$this->links = array();
			$this->cnt = 0;
		}
		$url = parse_url($link);
		if(isset($url['host'])){
			$this->host = $url['host'];
		}else{
			$path = explode('/',$url['path']);
			$this->host = $path[0];
		}
		$ch = curl_init($link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$html = curl_exec($ch);
		if(!$html){
			echo 'The bad URL. Please check and try again.';
			exit();
		}
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		
		$this->links_with_images[$this->cnt]['link'] = $link; 
		$this->links_with_images[$this->cnt]['images'] = $this->getImages($doc); 
		$this->cnt++;
		
		$links = $this->getLinks($doc, $link);
		$this->links = array_merge($this->links, $links);
		if(count($links) != 0 || (count($this->parsed_links) != count($this->links))){
			if(count($links == 0)){
				$links = $this->links;
			}
			foreach($links as $p_link){
				if(!in_array($p_link,$this->parsed_links)){
					$this->parsed_links[] = $p_link;
					$this->startParse($p_link);
				}else{
					continue;
				}
			}
		}		
	}
	
	protected function getLinks(DOMDocument $doc, $current_link){
		$links = $doc->getElementsByTagName('a');
		$link_paths = array();
		
		if($links->length != 0)
			foreach($links as $link){
				if(!in_array($link->getAttribute('href'),$this->links) 
					&& !in_array($this->host.$link->getAttribute('href'),$this->links) 
					&& $link->getAttribute('href') != $this->host 
					&& $link->getAttribute('href') != $current_link 
					&& $this->host.$link->getAttribute('href') != $current_link){
					$url = parse_url($link->getAttribute('href'));
					$array_components = explode('.',$link->getAttribute('href'));
					if(in_array(strtolower($array_components[count($array_components)-1]), ['pdf','png','gif','jpg','jpeg','txt']) || isset($url['fragment'])){
						continue;
					}
					if(!isset($url['host'])){
						if(isset($url['scheme'])){
							if($url['scheme'] == 'http' || $url['scheme'] == 'https')
								$link_paths[] = $this->host.$link->getAttribute('href');
						}else{
							if($link->getAttribute('href') != '/' && $link->getAttribute('href') != '' && $link->getAttribute('href') != '#')	
								$link_paths[] = $this->host.$link->getAttribute('href');
						}
					}elseif($url['host'] == $this->host){
						$link_paths[] = $link->getAttribute('href');
					}
				}
			}
			
		return array_unique($link_paths);		
	}
	
	
	protected function getImages(DOMDocument $doc){
		$images = $doc->getElementsByTagName('img');
		$image_paths = array();
		
		if($images->length != 0)
			foreach($images as $image)
			  $image_paths[] = $image->getAttribute('src');
		
		return $image_paths;
	}
}
?>