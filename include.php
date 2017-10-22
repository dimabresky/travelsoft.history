<?php
$classes = array(
    "travelsoft\\History" => "lib/History.php",
    "travelsoft\\HistoryEeventsHandlers" => "lib/HistoryEeventsHandlers.php"
);
CModule::AddAutoloadClasses("travelsoft.history", $classes);

@include_once 'functions.php';