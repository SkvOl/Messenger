<?php

namespace App\Http\Source;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version:"1",
    title: "Messenger",
    description: "<h2>url: http://138.124.55.208/api/prod/...</h2><br><br>
 * <h3>test - 138.124.55.208:54321</h3>
 * <h3>prod - 138.124.55.208:54322</h3>"
)]
abstract class Controller
{
    //
}
