<?php

function html($tag, $attrs = [], $contents = []) {

	$attrs_string = '';

	foreach ($attrs as $i => $attr) {
		$attrs_string .= "$i='$attr' ";
	}

	if (is_array($contents)) {

		$contents_array = $contents;
		$contents = '';

		foreach ($contents_array as $content) {
			$contents .= $content;
		}

	} 

	return "<$tag $attrs_string>$contents</$tag>";
	
}