<?php defined('BASEPATH') OR exit('No direct script access allowed');

function remap_sentences($standup_id, $nlp_sentences)
{
    $records = array();
    foreach ($nlp_sentences as $sentence) {
        $data = array(
            "standup_id" => $standup_id,
            "sentence" => null,
            "magnitude" => null,
            "score" => null
        );
        // Normalizzazione
        if (!empty($sentence->text->content))
            $data["sentence"] = $sentence->text->content;
        if (!is_null($sentence->sentiment->score))
            $data["score"] = (float)$sentence->sentiment->score;
        if (!is_null($sentence->sentiment->magnitude))
            $data["magnitude"] = (float)$sentence->sentiment->magnitude;
        // Append new
        $records[] = $data;
    }
    return $records;
}

function remap_entities($standup_id, $nlp_entities)
{
    $records = array();
    foreach ($nlp_entities as $entities) {
        $data = array(
            "standup_id" => $standup_id,
            "name" => null,
            "type" => null,
            "salience" => null
        );
        // Normalizzazione
        if (!empty($entities->name))
            $data["name"] = $entities->name;
        if (!empty($entities->type))
            $data["type"] = $entities->type;
        if (!is_null($entities->salience))
            $data["salience"] = round((float)$entities->salience, 8);

        $records[] = $data;
    }
    return $records;
}