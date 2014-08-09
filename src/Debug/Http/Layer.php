<?php

namespace Debug\Http;

use Studio\Http\Layer\AbstractConfigurableLayer;
use Studio\Http\Request;
use Exception;

class Layer extends AbstractConfigurableLayer
{
    public function call(Request $req, Exception $err)
    {
        $this->enableProfiler($req);

        return $this->app->call($req, $err);
    }

    public function enableProfiler($req)
    {
        $req->profiler = new Profiler([
            'host' => $req->host,
            'uri' => $req->uri,
        ]);

        $req->profiler->enable();
    }
}
