<?php


return [
  [
      "label" => "Format",
      "description" => "Set the date format",
      "action_type"=> "transform", // transform & option
      "action_value" => "date('{Format}', strtotime({Value}))",
  ]
];
