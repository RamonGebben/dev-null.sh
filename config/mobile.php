<?php

declare(strict_types=1);

return [

    /*
     * The URL schemes the authorize page may send an app back to with its code. A redirect_uri has to start with one
     * of these, or else be a loopback address the app listens on, so a code never leaves the device it was asked for
     * on (see App\Rules\DeviceRedirectUri).
     */
    'redirect_uri_prefixes' => ['devnull://'],

];
