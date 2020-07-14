<?php

file_put_contents("events.log", json_encode($_REQUEST) . "\n", 8);