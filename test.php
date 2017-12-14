<?php

/**
 * @file test.php
 * @author Leandor Miño <leandromq@hotmail.com>
 * Tested with PHP v7.0.3 - Windows 10
 * Library used Porter 2 Stemmer for PHP: https://github.com/markfullmer/porter2
 */

require 'process.inc';

$text = 'Take this paragraph of text and return an alphabetized list of ALL unique words.  A unique word is any form of a word often communicated with essentially the same meaning. For example, fish and fishes could be defined as a unique word by using their stem fish. For each unique word found in this entire paragraph, determine the how many times the word appears in total. Also, provide an analysis of what sentence index position or positions the word is found. The following words should not be included in your analysis or result set: "a", "the", "and", "of", "in", "be", "also" and "as".  Your final result MUST be displayed in a readable console output in the same format as the JSON sample object shown below.';

//STEP 1: Generate array with unique words
$stem_text = porterstemmer_process($text);//Text only with unique words to simplify process

$clean_text = str_replace(array("\"", ":",",","."),"",$stem_text);//Remove special characters

$words_array = preg_split("/[\s]+/", $clean_text); //Generate array with each word

$clean_words_array =array_diff($words_array, array("a", "the", "and", "of", "in", "be", "also", "as")); //Exclude words from array

$unique_clean_words_array = array_unique($clean_words_array); //Exclude duplicated words

//STEP 2: Sort array
sort($unique_clean_words_array, SORT_NATURAL | SORT_FLAG_CASE); //Sort alphabetically

//STEP 3: Count ocurrances
$count_words_array = array_count_values($clean_words_array); //Count words ocurrances

//STEP 4: Generate sentences array to detect indexes
$clean_sentences = str_replace(array("\"", ":",","),"",$stem_text);//Remove special characters
$sentences_array = preg_split("/[.]+/", $clean_sentences); 

//STEP 5: Build final output
$item = array();
foreach ($unique_clean_words_array as $word) {
  $item["word"] =  $word;
  $item["total-ocurrances"] =  $count_words_array[$word];
  $item["total-ocurrances"] =  $count_words_array[$word];
  $item["sentence-indexes"] = find_sentences($word,$sentences_array);
  $items[] = $item;
}

$output["results"] = $items;

echo json_encode($output,JSON_PRETTY_PRINT);


/*
 * Find sentences index where a word appears
 * 
 * @param   string  $word             Word to be searched 
 * @param   array   $sentences_array  Sentences to search  
 * @return  array
 */
function find_sentences($word, $sentences_array) {
  $sentences = array();
  foreach ($sentences_array as $key => $sentence) {
    $words_sentence_array = preg_split("/[\s]+/", $sentence); //Generate array with each word of the sentence
    if(!empty(array_search($word, $words_sentence_array))){ // If the word is found, add the index
      $sentences[] = array($key);
    }
  }
  return $sentences;
}
